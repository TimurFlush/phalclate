<?php

namespace TimurFlush\Phalclate;

/**
 * Class Translator
 * @package TimurFlush\Phalclate
 */
abstract class Translator
{
    /**
     * @var array
     */
    protected $_options = [];

    /**
     * Translator constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return array
     */
    public final function getOptions() : array
    {
        return $this->_options;
    }

    /**
     * @param array $options
     */
    public final function setOptions(array $options) : void
    {
        $this->_options = $options;
    }
}