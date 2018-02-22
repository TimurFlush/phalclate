<?php

namespace TimurFlush\Phalclate\Translator;
use TimurFlush\Phalclate\Translator;
use TimurFlush\Phalclate\TranslatorInterface;

/**
 * Class Google
 * @package TimurFlush\Phalclate\Translator
 * @author Timur Flush
 * @version 1.0.6
 */
class Google extends Translator implements TranslatorInterface
{
    public function translate(string $from, string $to, string $text): ?string
    {
        $url = "https://translate.google.com/translate_a/single?format=html&client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
        $fields = [
            'sl' => urlencode($from),
            'tl' => urlencode($to),
            'q' => urlencode($text)
        ];

        $fields_string = "";
        foreach ($fields as $key => $value)
            $fields_string .= $key . '=' . $value . '&';

        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ( $result === false OR $http_code != 200 )
            return null;

        $sentencesArray = json_decode($result, true);
        $sentences = "";
        foreach ($sentencesArray["sentences"] as $s)
            $sentences .= isset($s["trans"]) ? $s["trans"] : '';

        return $sentences;
    }
}