<?php

namespace App\Helper;

use DateTime;

class TokenHelper
{
    /**
     * @return string
     */
    public static function generateToken() : string
    {
        $bytes = openssl_random_pseudo_bytes(20);
        return md5(bin2hex($bytes) . '_' . microtime());
    }

    /**
     * @param int $lifetime
     * @return DateTime
     */
    public static function calculateExpire(int $lifetime) : DateTime
    {
        $lifetimeSeconds = $lifetime * 60;
        $dateExpire = new DateTime();
        $dateExpire->format('Y-m-d H:i:s');
        $dateExpire->modify("+{$lifetimeSeconds} seconds");
        return $dateExpire;
    }
}
