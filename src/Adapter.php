<?php
namespace TimurFlush\Phalclate;
/**
 * Class Adapter
 * @package TimurFlush
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

        $this->setStorage(new Storage());
        $this->setOptions($options);
    }
    /**
     * @param string|null $from
     * @param string|null $to
     * @param string $group
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
     * @param string|null $from
     * @param string|null $to
     * @param string|null $group
     * @param string $text
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
     * @param string|null $from
     * @param string|null $to
     * @param null $group
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
     * @param string|null $from
     * @param string|null $to
     * @param array $data
     * @param bool $onlyReplace
     * @param string|null $group
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
     * @param string $text
     * @param string|null $from
     * @param array $placeholders
     * @param string|null $group
     * @return string
     */
    public function _(string $text, string $from = null, array $placeholders = [], string $group = null)
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        if ($from === $to OR $text === '') return $text;
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
        /*$translatedText = '';
        foreach($config->translators as $translatorClass)
        {
            $translatorObject = new $translatorClass($this->getOptions());
            $map = preg_split('/%(.*?)%/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            foreach ($map as $chunk){
                if (isset($placeholders[$chunk])){
                    $translatedText .= '%' . $chunk . '%';
                    continue;
                }
                $translated = $translatorObject->translate($from, $to, $chunk);
                if (is_string($translated)){
                    $translatedText .= $translated;
                }else{
                    return '';
                }
            }
            if ($translatedText === null) continue;
        }*/
        /**  BEGIN **/
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
        /**  END  **/
        if ($translatedText === '') return '';
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
     * @param string|null $from
     * @param string|null $to
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
     * @param string $from
     * @param string $to
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
     * @param string|null $from
     * @param string|null $to
     * @param string $group
     * @return array
     */
    public function getGroup(string $from = null, string $to = null, string $group) : array
    {
        $options = $this->getOptions();
        if (!isset($from) OR $from === '')
            $from = $options['default_language'];
        if (!isset($to) OR $to === '')
            $to = $options['current_language'];
        $cache = $this->getCache($from, $to);
        if (isset($cache->{$group}))
            if (is_object($cache->{$group}))
                return get_object_vars($cache->{$group});
        return [];
    }
    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->_options;
    }
    /**
     * @param string|null $from
     * @param string|null $to
     * @return string
     */
    public function getFileName(string $from = null, string $to = null) : string
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