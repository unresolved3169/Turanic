<?php

namespace pocketmine\utils;

class TextUtils{

    protected $charWidths = [
        ' ' => 4,
        '!' => 2,
        '"' => 5,
        '\'' => 3,
        '(' => 5,
        ')' => 5,
        '*' => 5,
        ',' => 2,
        '.' => 2,
        ':' => 2,
        ';' => 2,
        '<' => 5,
        '>' => 5,
        '@' => 7,
        'I' => 4,
        '[' => 4,
        ']' => 4,
        'f' => 5,
        'i' => 2,
        'k' => 5,
        'l' => 3,
        't' => 4,
        '{' => 5,
        '|' => 2,
        '}' => 5,
        '~' => 7,
        '█' => 9,
        '░' => 8,
        '▒' => 9,
        '▓' => 9,
        '▌' => 5,
        '─' => 9
    ];

    public function center(string $input, int $maxLength = 0, bool $addRightPadding = false){
        $lines = explode("\n", trim($input));
        $sortedLines = $this->siralaKelimeUzunlugu($lines);
        $longest = $this->ilkDeger($sortedLines);
        if($maxLength == 0){
            $maxLength = strlen($longest);
        }

        $result = "";
        $spaceWidth = $this->getCharWidth(" ");

        foreach($lines as $sortedLine){
            $len = max($maxLength - strlen($sortedLine), 0);
            $padding = round($len / (2 * $spaceWidth));
            $paddingRight = floor($len / (2 * $spaceWidth));
            $result .= str_repeat(" ", $padding) . $sortedLine . "§r" . ($addRightPadding ? str_repeat(" ", $paddingRight) : "") . "\n";
        }

        $result = substr($result, 0, strlen($result) - 1);
        return $result;
    }

    public function getCharWidth($c){
        $width = 6;
        if(isset($this->charWidths[$c])){
            $width = $this->charWidths[$c];
        }
        return $width;
    }

    public function siralaKelimeUzunlugu(array $array){
        $degerler = [];
        foreach($array as $deger){
            $degerler[strlen($deger)] = $deger;
        }
        krsort($degerler);
        return $degerler;
    }

    public function ilkDeger(array $array){
        return array_shift($array);
    }

    /*
     *
     * var sortedLines = lines.OrderByDescending(GetPixelLength);

var longest = sortedLines.First();
if (maxLength == 0)
{
maxLength = GetPixelLength(longest);
}

var result = "";

var spaceWidth = GetCharWidth(SpaceChar);

foreach (var sortedLine in lines)
			{
                var len = Math.Max(maxLength - GetPixelLength(sortedLine), 0);
                var padding = (int)Math.Round(len / (2d * spaceWidth));
				var paddingRight = (int)Math.Floor(len / (2d * spaceWidth));
				result += new string(SpaceChar, padding) + sortedLine + "§r" + (addRightPadding ? new string(SpaceChar, paddingRight) : "" ) + "\n";
			}

			result = result.TrimEnd('\n');

			return result;
     */
}