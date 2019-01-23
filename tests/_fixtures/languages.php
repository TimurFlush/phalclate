<?php

use TimurFlush\Phalclate\Entity\Language;
use TimurFlush\Phalclate\Entity\Region;

try {
    $ru = new Language('ru');
    $en = new Language('en');

    $en->setRegions(
        [
            new Region('US'),
            new Region('GB')
        ]
    );
} catch (\Throwable $e) {
    throw new \Exception('Language fixtures error:' . $e->getMessage());
}

return [
    $ru, $en
];