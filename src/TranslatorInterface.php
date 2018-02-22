<?php

namespace TimurFlush\Phalclate;

/**
 * Interface TranslatorInterface
 * @package TimurFlush\Phalclate
 * @author Timur Flush
 * @version 1.0.6
 */
interface TranslatorInterface
{
    /**
     * Translates text.
     *
     * @param string $from Original text.
     * @param string $to Target text.
     * @param string $text Text.
     * @return string|null
     */
    public function translate(string $from, string $to, string $text) : ?string;
}