<?php

namespace TimurFlush\Phalclate\Translator;

use Phalcon\Cache\Backend\File;
use Phalcon\Cache\Frontend\Json;
use TimurFlush\Phalclate\Translator;
use TimurFlush\Phalclate\TranslatorInterface;

/**
 * Class Yandex
 * @package TimurFlush\Phalclate\Translator
 * @author Timur Flush
 * @version 1.0.3
 */
class Yandex extends Translator implements TranslatorInterface
{
    /**
     * @var \Phalcon\Cache\Backend\File
     */
    private $backendCache;

    /**
     * Yandex constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->backendCache = new File(new Json([
            'lifetime' => 60,
        ]), [
           'cacheDir' => rtrim($options['cache_directory'], '/') . '/'
        ]);

        parent::__construct($options);
    }

    /**
     * @return null|string
     */
    private function getSID()
    {
        $fileName = str_replace('\\', '-', __CLASS__);
        $cache = $this->backendCache->get($fileName);
        if ($cache !== null) {
            return $cache->SID;
        }

        $parsed = [];
        $page = @file_get_contents("http://translate.yandex.com");
        if ($page === false){
            return null;
        }

        $page = preg_replace(
            ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/>(\s)+</', '/\n/', '/\r/', '/\t/',],
            ['>', '<', '\\1', '><', '', '', '',],
            $page
        );

        if (preg_match_all("/SID:[[:space:]]*'([^']+)'/U", $page, $parsed, PREG_SET_ORDER) === false)
            return null;

        $explode = explode('.', $parsed[0][1]);
        $normalSID = implode('.', [
                strrev($explode[0]),
                strrev($explode[1]),
                strrev($explode[2]),
            ]) . '-0-0';
        $this->backendCache->save($fileName, ['SID' => $normalSID]);
        return $normalSID;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $text
     * @return null|string
     */
    public function translate(string $from, string $to, string $text): ?string
    {
        $SID = $this->getSID();
        if ($SID === null)
            return null;

        $ch = curl_init('https://translate.yandex.net/api/v1/tr.json/translate?' . http_build_query([
                'id' => $SID,
                'srv' => 'tr-text',
                'text' => $text,
                'lang' => "{$from}-{$to}",
                'format' => 'html'
            ])
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ( $result === false OR $http_code != 200 )
            return null;

        $response = json_decode($result);

        return $response->text[0];
    }
}