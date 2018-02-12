<?php

namespace TimurFlush\Phalclate;

/**
 * Interface TranslatorInterface
 * @package TimurFlush\Phalclate
 */
interface TranslatorInterface
{
    /**
     * @param string $from
     * @param string $to
     * @param string $text
     * @return mixed
     */
    public function translate(string $from, string $to, string $text) : ?string;
}