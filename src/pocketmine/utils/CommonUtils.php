<?php

namespace pocketmine\utils;

class CommonUtils{

    /**
     * Searches case insensitively array $haystack for $needle.
     * src: http://php.net/manual/en/function.in-array.php#89256
     * @param        mixed $needle
     * @param        array $haystack
     * @return        bool
     */
    static function in_arrayi($needle, array $haystack): bool{
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}