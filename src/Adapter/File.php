<?php

namespace TimurFlush\Phalclate\Adapter;

use TimurFlush\Phalclate\Adapter;
use Phalcon\Cache\Backend\File as BackendCache;

/**
 * Class File
 * @package TimurFlush\Adapter
 */
class File extends Adapter
{
    /**
     * File constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->setBackendCache(new BackendCache($this->getFrontendCache(), [
            'cacheDir' => rtrim($options['cache_directory'], '/\\') . '/'
        ]));

        parent::__construct($options);
    }
}