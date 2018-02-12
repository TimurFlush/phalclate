<?php

return new \Phalcon\Config([
    'mask' => '%from%-Translate-%to%',
    'translators' => [
        \TimurFlush\Phalclate\Translator\Yandex::class,
        \TimurFlush\Phalclate\Translator\Google::class
    ]
]);