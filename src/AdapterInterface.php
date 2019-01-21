<?php
/*
 ***********************************************************************
 * Copyright (c) 2018 - present, Timur Flush. All rights reserved.
 ***********************************************************************
 * Author: Timur Flush <flush02@tutanota.com> <https://github.com/timurflush>
 ***********************************************************************
*/
namespace TimurFlush\Phalclate;

interface AdapterInterface
{
    /**
     * Get translation from adapter.
     *
     * @param string        $key        Translation key.
     * @param string        $language   Language name.
     * @param null|string   $dialect    Dialect name.
     * @param bool          $firstFetch First fetch mode.
     * @return null|string
     */
    public function getTranslation(string $key, string $language, ?string $dialect, bool $firstFetch = false);

    /**
     * Whether the adapter is ready to work.
     *
     * @return bool
     */
    public function isReady(): bool;
}
