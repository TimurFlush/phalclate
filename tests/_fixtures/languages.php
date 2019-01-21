<?php

use TimurFlush\Phalclate\Entity\Language;
use TimurFlush\Phalclate\Entity\Dialect;

try {
    $ru = new Language('ru');
    $en = new Language('en');

    $en->setDialects(
        [
            new Dialect('US'),
            new Dialect('GB')
        ]
    );
} catch (\Throwable $e) {
    throw new \Exception('Language fixtures error:' . $e->getMessage());
}

return [
    $ru, $en
];