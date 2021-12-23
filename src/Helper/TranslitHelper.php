<?php

namespace App\Helper;

/**
 * Class TransliteratorHelper
 * @package App\Helper
 */
class TranslitHelper
{
    /**
     * @param $strName
     * @param string $connector
     * @return string
     */
    public static function cyrillicTransliter($strName, $connector = '-')
    {
        $strName = (string) $strName;
        $strName = strip_tags($strName); // remove html-tags
        $strName = str_replace(array("\n", "\r"), " ", $strName);
        $strName = preg_replace('/\s+/', ' ', $strName);
        $strName = trim($strName);
        $strName = function_exists('mb_strtolower') ? mb_strtolower($strName) : strtolower($strName);

        $strName = strtr($strName, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
                'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
                'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
                'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e',
                'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''
            )
        );
        $strName = str_replace(' ', $connector, $strName);

        return $strName;
    }
}
