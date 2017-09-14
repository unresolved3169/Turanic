<?php

namespace pocketmine\utils;

class TextUtils{

    public function center(string $input){
        $lines = explode("\n", $input);
        $maxLength = max(array_map("mb_strlen", $lines));
        foreach($lines as $key => $line) {
            $line = str_pad($line, $maxLength, " ", STR_PAD_BOTH);
            $lines[$key] = $line;
        }
        return implode("\n", $lines);
    }
}