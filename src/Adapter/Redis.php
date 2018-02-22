<?php

namespace TimurFlush\Phalclate\Adapter;

use TimurFlush\Phalclate\Adapter;
use Phalcon\Cache\Backend\Redis as BackendCache;

/**
 * Class File
 * @package TimurFlush\Adapter
 * @author Timur Flush
 * @version 1.0.6
 */
class Redis extends Adapter
{
    /**
     * File constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['host']))
            trigger_error("Param 'host' isn't specified.");
        if (!isset($options['port']))
            trigger_error("Param 'port' isn't specified.");
        if (!isset($options['auth']))
            $options['auth'] = '';
        if (!isset($options['persistent']))
            $options['persistent'] = false;
        if (!isset($options['index']))
            $options['index'] = 0;

        $this->setBackendCache(new BackendCache($this->getFrontendCache(), [
            'host' => (string)$options['host'],
            'port' => (int)$options['port'],
            'auth' => (string)$options['auth'],
            'persistent' => (bool)$options['persistent'],
            'index' => $options['index']
        ]));

        parent::__construct($options);
    }
}