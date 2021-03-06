<?php

namespace App\Helper;

use App\Model\WikiLink;

class FileParser
{
    private const CODE_DEF = '```';

    public function createHashUrl($id, $title)
    {
        return str_replace(['/', '_', ' ', '.'], ['-'], substr($id, 1) . '-' .$this->translit($title));
    }

    private function translit($str) {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }


    public function clean($str)
    {
        return \strip_tags(\trim(\str_replace([PHP_EOL, "\r\n", "#"], "", $str)));
    }

    public function linkParser($url, $file, callable $linkFindFunction)
    {
        // Вынести в отдельный файл, смотреть, что нет ```
        $res = \fopen($file, 'r');

        // Пропуска название
        \fgets($res);

        $codDef = false;

        while(!feof($res)) {
            $origStr = \fgets($res);
            $str =  \trim($origStr);

            if ( 0 === strpos($str, static::CODE_DEF)) {
                $codDef = !$codDef;
            }
            $link = null;

            if (!$codDef && strpos($str, '#') === 0) {
                $link = new WikiLink( $this->clean($str),  $this->createHashUrl(str_replace('\\', '/', $url), $this->clean($str)));
                $linkFindFunction($str, $link);
            } else{
                $linkFindFunction($origStr, $link);
            }


        }
        \fclose($res);
    }
}

