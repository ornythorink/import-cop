<?php

namespace AppBundle\Utils;


class Sources
{
    protected static $sources = array(
        'znx' =>
            array(
                'prefix'               => 'Znx',
                'merchantCategoryName' => 'MerchantProductCategoryPath',
                'separator' => ',',
            ),
        'sdc' =>
            array(
                'prefix'               => 'Sdc',
                'merchantCategoryName' => '',
                'separator' => ',',
            ),
        'tdd' =>
            array(
                'prefix'               => 'Tdd',
                'merchantCategoryName' => 'merchantCategoryName',
                'separator' => '|',
            ),
    );

    public static function getSourceKey($source, $key)
    {
        return self::$sources[$source][$key];
    }
}