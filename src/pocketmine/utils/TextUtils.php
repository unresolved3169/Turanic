<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
*/

namespace pocketmine\utils;

class TextUtils{

    public static function center(string $input){
        $lines = explode("\n", $input);
        $max = max(array_map("strlen", $lines));
        foreach($lines as $key => $line){
            $sayi = strlen($line);
            if($sayi == $max) continue;
            $spacecounter = substr_count(implode($lines), ' ');
            $remaining = round((($max + $spacecounter) - $sayi) / 2);
            $space = str_repeat(" ", $remaining);
            $lines[$key] = $space.$line.$space;
        }
        return implode("\n", $lines);
    }
}
