<?php

namespace TimurFlush\Phalclate;

use TimurFlush\Phalclate\Storage\Memory;

/**
 * Class Adapter
 * @package TimurFlush
 * @author Timur Flush
 * @version 1.0.3
 *
 * @property array $_options Options.
 * @property \Phalcon\Cache\BackendInterface $_backendCache Backend cacher.
 * @property StorageInterface $_storage Memory.
 * @property string $_defaultGroup Default group.
 */
abstract class Adapter
{
    /**
     * @var array
     */
    protected $_options = [];
    /**
     * @var \Phalcon\Cache\BackendInterface
     */
    protected $_backendCache;

    /**
     * @var StorageInterface
     */
    protected $_storage;

    /**
     * @var string
     */
    protected $_defaultGroup = 'default';

    /**
     * Adapter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['default_language']))
            trigger_error("Param 'default_language' isn't specified.");
        if (!isset($options['current_language']))
            trigger_error("Param 'current_language' isn't specified.");
        if (!isset($options['cache_directory']))
            trigger_error("Param 'cache_directory' isn't specified.");
        if (!($this->getBackendCache()) instanceof \Phalcon\Cache\BackendInterface)
            trigger_error("Backend cache adapter isn't specified.");

        $this->setStorage(new Memory());
        $this->setOptions($options);
    }

    /**
     * Deletes the group.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @param string $group The group of translation.
     * @return bool
     */
    public function removeGroup(string $from = null, string $to = null, string $group)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        return $this->saveCache($from, $to, [
            $group => Operations::DELETE
        ]);
    }

    /**
     * Removes the translation from the group.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @param string|null $group The group of translation.
     * @param string|array $text Text or an array of texts to be deleted.
     * @return bool
     */
    public function removeTranslate(string $from = null, string $to = null, string $group = null, $text)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        if (!isset($group) OR $group === '')
            $group = $this->_defaultGroup;
        if (is_string($text)) {
            return $this->saveCache($from, $to, [
                $group => [
                    $text => Operations::DELETE
                ]
            ]);
        }else if (is_array($text)){
            $commands = [];
            foreach($text as $translate)
                $commands[$translate] = Operations::DELETE;
            return $this->saveCache($from, $to, [
                $group => $commands
            ]);
        }
        return false;
    }

    /**
     * Returns the translation cache.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @param null $group The group of translation.
     * @return mixed|null|\stdClass
     */
    public function getCache(string $from = null, string $to = null, $group = null)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];

        $filename = $this->getFileName($from, $to);
        if ($this->getStorage()->exists($filename)){
            $cache = $this->getStorage()->get($filename) ?? new \stdClass();
        }else{
            $cache = $this->getBackendCache()->get($this->getFileName($from, $to)) ?? new \stdClass();
            $this->getStorage()->save($filename, $cache);
        }
        if (is_string($group)){
            if (isset($cache->{$group}))
                return $cache->{$group};
        }else if (is_array($group)){
            $retCache = new \stdClass();
            foreach ($group as $group)
                if (isset($cache->{$group}))
                    $retCache->{$group} = $cache->{$group};
            return $retCache;
        }
        return $cache;
    }

    /**
     * Saves data to the translation cache.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @param array $data Data for save.
     * @param bool $onlyReplace Only replace.
     * @param string|null $group The group of translation.
     * @return bool
     */
    public function saveCache(string $from = null, string $to = null, array $data, $onlyReplace = false, string $group = null)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        $filename = $this->getFileName($from, $to);
        $cache = $this->getCache($from, $to);
        if (is_null($group)){
            foreach ($data as $group => $translates){
                if (is_array($translates)) {
                    foreach ($translates as $original => $translate) {
                        if ($translate === Operations::DELETE){
                            if (isset($cache->{$group}->{$original}))
                                unset($cache->{$group}->{$original});
                            continue;
                        }
                        if ($onlyReplace) {
                            if (isset($cache->{$group}->{$original}))
                                $cache->{$group}->{$original} = $translate;
                        } else {
                            if (!isset($cache->{$group}))
                                $cache->{$group} = new \stdClass();
                            $cache->{$group}->{$original} = $translate;
                        }
                    }
                }else if (is_int($translates) && $translates === Operations::DELETE){
                    if (isset($cache->{$group}))
                        unset($cache->{$group});
                }
            }
        }else{
            foreach ($data as $original => $translate){
                if ($translate === Operations::DELETE){
                    if (isset($cache->{$group}->{$original}))
                        unset($cache->{$group}->{$original});
                    continue;
                }
                if ($onlyReplace){
                    if (isset($cache->{$group}->{$original}))
                        $cache->{$group}->{$original} = $translate;
                }else{
                    if (!isset($cache->{$group}))
                        $cache->{$group} = new \stdClass();
                    $cache->{$group}->{$original} = $translate;
                }
            }
        }
        $this->getStorage()->save($filename, $cache);
        return $this->getBackendCache()->save($filename, $cache);
    }

    /**
     * Translates text and writes it to the translation cache.
     *
     * @param string $text Text for translate.
     * @param string|null $from Original language.
     * @param array $placeholders Placeholders.
     * @param string|null $group The group of translation.
     * @return string
     */
    public function _(string $text, string $from = null, array $placeholders = [], string $group = null)
    {
        $options = $this->getOptions();

        if (!isset($from) OR $from === '')
            $from = $options['default_language'];

        if (!isset($to) OR $to === '')
            $to = $options['current_language'];

        if ($from === $to OR $text === '') return Helper::replacePlaceholders($text, $placeholders);


        $config = Helper::getConfig('Config');
        $cache = $this->getCache($from, $to);
        if ($group === null) {
            if (isset($cache->{$this->_defaultGroup}->{$text})) {
                return Helper::replacePlaceholders($cache->{$this->_defaultGroup}->{$text}, $placeholders);
            }
        }else{
            if (isset($cache->{$group}->{$text})) {
                return Helper::replacePlaceholders($cache->{$group}->{$text}, $placeholders);
            }
        }

        $translatedText = '';
        $map = preg_split('/%(.*?)%/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach($map as $chunk){
            if (isset($placeholders[$chunk])){
                $translatedText .= '%' . $chunk . '%';
                continue;
            }
            foreach($config->translators as $translatorClass){
                $translatorObject = new $translatorClass($this->getOptions());
                $translated = $translatorObject->translate($from, $to, $chunk);
                if (is_string($translated)){
                    $translatedText .= $translated;
                    break;
                }else if ($translated === null){
                    return '';
                }
            }
        }

        if ($translatedText === '')
            return '';

        if ($group === null){
            $this->saveCache($from, $to, [
                $this->_defaultGroup => [
                    $text => $translatedText
                ]
            ]);
        }else{
            $this->saveCache($from, $to, [
                $group => [
                    $text => $translatedText
                ]
            ]);
        }
        return Helper::replacePlaceholders($translatedText, $placeholders);
    }

    /**
     * Clears the cache.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @return bool
     */
    public final function flushCache(string $from = null, string $to = null) : bool
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        return $this->_backendCache->delete($this->getFileName($from, $to));
    }

    /**
     * Returns a list of all translation groups from the cache.
     *
     * @param string $from Original language.
     * @param string $to The group of translation.
     * @return array
     */
    public function getGroups(string $from = null, string $to = null) : array
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];

        $cache = $this->getCache($from, $to);

        $groups = [];
        foreach(get_object_vars($cache) as $group => $translates)
            $groups[$group] = sizeof(get_object_vars($translates));

        ksort($groups);

        return $groups;
    }

    /**
     * Returns the translation group from the cache.
     *
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @param string $group The group of translation.
     * @return array|null
     */
    public function getGroup(string $from = null, string $to = null, string $group)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        $cache = $this->getCache($from, $to);
        if (isset($cache->{$group})) {
            if (is_object($cache->{$group})) {
                $group = get_object_vars($cache->{$group});
                ksort($group);
                return $group;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->_options;
    }

    /**
     * @param string|null $from Original language.
     * @param string|null $to Target language.
     * @return string
     */
    private function getFileName(string $from = null, string $to = null) : string
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        $config = Helper::getConfig('Config');
        return Helper::replacePlaceholders($config->mask, ['from' => $from, 'to' => $to]);
    }

    /**
     * @param $lifetime
     * @return \Phalcon\Cache\Frontend\Json
     */
    protected final function getFrontendCache($lifetime = 86400 * 30 * 12 * 1000) : \Phalcon\Cache\Frontend\Json
    {
        return new \Phalcon\Cache\Frontend\Json([
            'lifetime' => $lifetime
        ]);
    }

    /**
     * @param \Phalcon\Cache\BackendInterface $backend
     */
    protected final function setBackendCache(\Phalcon\Cache\BackendInterface $backend) : void
    {
        $this->_backendCache = $backend;
    }

    /**
     * @return \Phalcon\Cache\BackendInterface
     */
    protected final function getBackendCache() : \Phalcon\Cache\BackendInterface
    {
        return $this->_backendCache;
    }

    /**
     * @param StorageInterface $storage
     */
    protected final function setStorage(StorageInterface $storage) : void
    {
        $this->_storage = $storage;
    }

    /**
     * @return StorageInterface
     */
    protected final function getStorage() : StorageInterface
    {
        return $this->_storage;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options) : void
    {
        $this->_options = $options;
    }
}