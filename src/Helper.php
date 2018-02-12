<?php

namespace TimurFlush\Phalclate;

use Phalcon\Translate\Interpolator\AssociativeArray;

class Helper
{
    /**
     * @var \Phalcon\Translate\Interpolator\AssociativeArray
     */
    private static $assocArray;

    /**
     * @param string $text
     * @param array $placeholders
     * @return string
     */
    public static function replacePlaceholders(string $text, array $placeholders)
    {
        if (!self::$assocArray)
            self::$assocArray = new AssociativeArray();

        return self::$assocArray->replacePlaceholders($text, $placeholders);

    }

    /**
     * @param string $config
     * @return mixed
     */
    public static function getConfig(string $config)
    {
        $file = require __DIR__ . '/Config/' . $config . '.php';
        if (!($file instanceof \Phalcon\Config))
            trigger_error("The plug-in configuration file is not a class \Phalcon\Config::class.", E_USER_ERROR);
        return $file;
    }
}