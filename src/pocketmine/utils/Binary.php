<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

/**
 * Various Utilities used around the code
 */

namespace pocketmine\utils;


class Binary {
	const BIG_ENDIAN = 0x00;
	const LITTLE_ENDIAN = 0x01;

	/**
	 * Reads a 3-byte big-endian number
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function readTriad($str){
		return unpack("N", "\x00" . $str)[1];
	}

	/**
	 * Writes a 3-byte big-endian number
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeTriad($value){
		return substr(pack("N", $value), 1);
	}

	/**
	 * Reads a 3-byte little-endian number
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function readLTriad($str){
		return unpack("V", $str . "\x00")[1];
	}

	/**
	 * Writes a 3-byte little-endian number
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLTriad($value){
		return substr(pack("V", $value), 0, -1);
	}

	/**
	 * Reads a byte boolean
	 *
	 * @param $b
	 *
	 * @return bool
	 */
	public static function readBool($b){
		return self::readByte($b, false) === 0 ? false : true;
	}

	/**
	 * Writes a byte boolean
	 *
	 * @param $b
	 *
	 * @return bool|string
	 */
	public static function writeBool($b){
		return self::writeByte($b === true ? 1 : 0);
	}

	/**
	 * Reads an unsigned/signed byte
	 *
	 * @param string $c
	 * @param bool   $signed
	 *
	 * @return int
	 */
	public static function readByte($c, $signed = true){
		$b = ord($c{0});

		if($signed){
			if(PHP_INT_SIZE === 8){
				return $b << 56 >> 56;
			}else{
				return $b << 24 >> 24;
			}
		}else{
			return $b;
		}
	}

	/**
	 * Writes an unsigned/signed byte
	 *
	 * @param $c
	 *
	 * @return string
	 */
	public static function writeByte($c){
		return chr($c);
	}

	/**
	 * Reads a 16-bit unsigned big-endian number
	 *
	 * @param $str
	 *
	 * @return int
	 */
	public static function readShort($str){
		return unpack("n", $str)[1];
	}

	/**
	 * Reads a 16-bit signed big-endian number
	 *
	 * @param $str
	 *
	 * @return int
	 */
	public static function readSignedShort($str){
		if(PHP_INT_SIZE === 8){
			return unpack("n", $str)[1] << 48 >> 48;
		}else{
			return unpack("n", $str)[1] << 16 >> 16;
		}
	}

	/**
	 * Writes a 16-bit signed/unsigned big-endian number
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeShort($value){
		return pack("n", $value);
	}

	/**
	 * Reads a 16-bit unsigned little-endian number
	 *
	 * @param      $str
	 *
	 * @return int
	 */
	public static function readLShort($str){
		return unpack("v", $str)[1];
	}

	/**
	 * Reads a 16-bit signed little-endian number
	 *
	 * @param      $str
	 *
	 * @return int
	 */
	public static function readSignedLShort($str){
		if(PHP_INT_SIZE === 8){
			return unpack("v", $str)[1] << 48 >> 48;
		}else{
			return unpack("v", $str)[1] << 16 >> 16;
		}
	}

	/**
	 * Writes a 16-bit signed/unsigned little-endian number
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLShort($value){
		return pack("v", $value);
	}

	/**
	 * @param $str
	 *
	 * @return int
	 */
	public static function readInt($str){
		if(PHP_INT_SIZE === 8){
			return unpack("N", $str)[1] << 32 >> 32;
		}else{
			return unpack("N", $str)[1];
		}
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeInt($value){
		return pack("N", $value);
	}

	/**
	 * @param $str
	 *
	 * @return int
	 */
	public static function readLInt($str){
		if(PHP_INT_SIZE === 8){
            return self::signInt(unpack("V", $str)[1]);
		}else{
			return unpack("V", $str)[1];
		}
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLInt($value){
		return pack("V", $value);
	}

    /**
     * Reads a 4-byte floating-point number, rounded to the specified number of decimal places.
     *
     * @param string $str
     * @param int $accuracy
     *
     * @return float
     */
    public static function readRoundedFloat(string $str, int $accuracy) : float{
        return round(self::readFloat($str), $accuracy);
    }

    /**
     * Reads a 4-byte floating-point number
     *
     * @param string $str
     * @return float
     */
    public static function readFloat(string $str) : float{
        return (ENDIANNESS === self::BIG_ENDIAN ? unpack("f", $str)[1] : unpack("f", strrev($str))[1]);
    }

    /**
     * Writes a 4-byte floating-point number.
     *
     * @param float $value
     * @return string
     */
    public static function writeFloat(float $value) : string{
        return ENDIANNESS === self::BIG_ENDIAN ? pack("f", $value) : strrev(pack("f", $value));
    }

	/**
	 * @param     $str
	 * @param int $accuracy
	 *
	 * @return float
	 */
	public static function readLFloat($str, int $accuracy = -1){
		$value = ENDIANNESS === self::BIG_ENDIAN ? unpack("f", strrev($str))[1] : unpack("f", $str)[1];
		if($accuracy > -1){
			return round($value, $accuracy);
		}else{
			return $value;
		}
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLFloat($value){
		return ENDIANNESS === self::BIG_ENDIAN ? strrev(pack("f", $value)) : pack("f", $value);
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function printFloat($value){
		return preg_replace("/(\\.\\d+?)0+$/", "$1", sprintf("%F", $value));
	}

	/**
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function readDouble($str){
		return ENDIANNESS === self::BIG_ENDIAN ? unpack("d", $str)[1] : unpack("d", strrev($str))[1];
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeDouble($value){
		return ENDIANNESS === self::BIG_ENDIAN ? pack("d", $value) : strrev(pack("d", $value));
	}

	/**
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function readLDouble($str){
		return ENDIANNESS === self::BIG_ENDIAN ? unpack("d", strrev($str))[1] : unpack("d", $str)[1];
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLDouble($value){
		return ENDIANNESS === self::BIG_ENDIAN ? strrev(pack("d", $value)) : pack("d", $value);
	}

	/**
	 * @param $x
	 *
	 * @return int|string
	 */
	public static function readLong($x){
		if(PHP_INT_SIZE === 8){
			$int = unpack("N*", $x);

			return ($int[1] << 32) | $int[2];
		}else{
			$value = "0";
			for($i = 0; $i < 8; $i += 2){
				$value = bcmul($value, "65536", 0);
				$value = bcadd($value, (string) self::readShort(substr($x, $i, 2)), 0);
			}

			if(bccomp($value, "9223372036854775807") == 1){
				$value = bcadd($value, "-18446744073709551616");
			}

			return $value;
		}
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLong($value){
		if(PHP_INT_SIZE === 8){
			return pack("NN", $value >> 32, $value & 0xFFFFFFFF);
		}else{
			$x = "";
			$value = (string) $value;

			if(bccomp($value, "0") == -1){
				$value = bcadd($value, "18446744073709551616");
			}

			$x .= self::writeShort((int) bcmod(bcdiv($value, "281474976710656"), "65536"));
			$x .= self::writeShort((int) bcmod(bcdiv($value, "4294967296"), "65536"));
			$x .= self::writeShort((int) bcmod(bcdiv($value, "65536"), "65536"));
			$x .= self::writeShort((int) bcmod($value, "65536"));

			return $x;
		}
	}

	/**
	 * @param $str
	 *
	 * @return int|string
	 */
	public static function readLLong($str){
		return self::readLong(strrev($str));
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeLLong($value){
		return strrev(self::writeLong($value));
	}

	//TODO: proper varlong support

    /**
     * Reads a 32-bit zigzag-encoded variable-length integer.
     *
     * @param string $buffer
     * @param int    &$offset
     *
     * @return int
     */
    public static function readVarInt(string $buffer, int &$offset) : int{
        $shift = PHP_INT_SIZE === 8 ? 63 : 31;
        $raw = self::readUnsignedVarInt($buffer, $offset);
        $temp = ((($raw << $shift) >> $shift) ^ $raw) >> 1;
        return $temp ^ ($raw & (1 << $shift));
    }

    /**
     * Reads a 32-bit variable-length unsigned integer.
     *
     * @param string $buffer
     * @param int    &$offset
     *
     * @return int
     *
     * @throws \InvalidArgumentException if the var-int did not end after 5 bytes
     */
    public static function readUnsignedVarInt(string $buffer, int &$offset) : int{
        $value = 0;
        for($i = 0; $i <= 35; $i += 7){
            $b = ord($buffer{$offset++});
            $value |= (($b & 0x7f) << $i);

            if(($b & 0x80) === 0){
                return $value;
            }elseif(!isset($buffer{$offset})){
                throw new \UnexpectedValueException("Expected more bytes, none left to read");
            }
        }

        throw new \InvalidArgumentException("VarInt did not terminate after 5 bytes!");
    }


    /**
	 * @param $v
	 *
	 * @return string
	 */
	public static function writeVarInt($v){
		return self::writeUnsignedVarInt(($v << 1) ^ ($v >> (PHP_INT_SIZE === 8 ? 63 : 31)));
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function writeUnsignedVarInt($value){
		$buf = "";
		for($i = 0; $i < 10; ++$i){
			if(($value >> 7) !== 0){
				$buf .= chr($value | 0x80); //Let chr() take the last byte of this, it's faster than adding another & 0x7f.
			}else{
				$buf .= chr($value & 0x7f);

				return $buf;
			}
			$value = (($value >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
		}
		throw new \InvalidArgumentException("Value too large to be encoded as a varint");
	}

    public static function writeSignedVarInt($v){
        if ($v >= 0) {
            $v = 2 * $v;
        } else {
            $v = 2 * abs($v) - 1;
        }
        return self::writeVarInt($v);
    }

    /**
     * Reads a 32_64-bit zigzag-encoded variable-length integer.
     *
     * @param string $buffer
     * @param int    &$offset
     *
     * @return int
     */
    public static function readVarLong(string $buffer, int &$offset) : int{
        if (PHP_INT_SIZE === 8) {
            $raw = self::readUnsignedVarLong($buffer, $offset);
            $temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
            return $temp ^ ($raw & (1 << 63));
        } else {
            /** @var string $raw */
            $raw = self::readUnsignedVarLong($buffer, $offset);
            $result = bcdiv($raw, "2");
            if (bcmod($raw, "2") === "1") {
                $result = bcsub(bcmul($result, "-1"), "1");
            }

            return $result;
        }
    }

    /**
     * Reads a 32_64-bit unsigned variable-length integer.
     *
     * @param string $buffer
     * @param int    &$offset
     *
     * @return int
     */
    public static function readUnsignedVarLong(string $buffer, int &$offset) : int{
        if (PHP_INT_SIZE === 8) {
            $value = 0;
            for($i = 0; $i <= 63; $i += 7){
                $b = ord($buffer{$offset++});
                $value |= (($b & 0x7f) << $i);
                if(($b & 0x80) === 0){
                    return $value;
                }elseif(!isset($buffer{$offset})){
                    throw new \UnexpectedValueException("Expected more bytes, none left to read");
                }
            }
            throw new \InvalidArgumentException("VarLong did not terminate after 10 bytes!");
        } else {
            $value = "0";
            for ($i = 0; $i <= 63; $i += 7) {
                $b = ord($buffer{$offset++});
                $value = bcadd($value, bcmul((string)($b & 0x7f), bcpow("2", "$i")));

                if (($b & 0x80) === 0) {
                    return $value;
                } elseif (!isset($buffer{$offset})) {
                    throw new \UnexpectedValueException("Expected more bytes, none left to read");
                }
            }

            throw new \InvalidArgumentException("VarLong did not terminate after 10 bytes!");
        }
    }

    /**
     * Writes a 32_64-bit integer as a zigzag-encoded variable-length long.
     *
     * @param int $v
     * @return string
     */
    public static function writeVarLong(int $v) : string{
        if (PHP_INT_SIZE === 8) {
            return self::writeUnsignedVarLong(($v << 1) ^ ($v >> 63));
        } else {
            $v = (string) $v;
            $v = bcmod(bcmul($v, "2"), "18446744073709551616");
            if (bccomp($v, "0") == -1) {
                $v = bcsub(bcmul($v, "-1"), "1");
            }

            return self::writeUnsignedVarLong($v);
        }
    }

    /**
     * Writes a 32_64-bit unsigned integer as a variable-length long.
     * @param int $value
     *
     * @return string
     */
    public static function writeUnsignedVarLong(int $value) : string{
        if (PHP_INT_SIZE === 8) {
            $buf = "";
            for ($i = 0; $i < 10; ++$i) {
                if (($value >> 7) !== 0) {
                    $buf .= chr($value | 0x80); //Let chr() take the last byte of this, it's faster than adding another & 0x7f.
                } else {
                    $buf .= chr($value & 0x7f);
                    return $buf;
                }
                $value = (($value >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
            }
            throw new \InvalidArgumentException("Value too large to be encoded as a VarLong");
        } else {
            $value = (string) $value;
            $buf = "";

            if (bccomp($value, "0") == -1) {
                $value = bcadd($value, "18446744073709551616");
            }

            for ($i = 0; $i < 10; ++$i) {
                $byte = (int)bcmod($value, "128");
                $value = bcdiv($value, "128");
                if ($value !== "0") {
                    $buf .= chr($byte | 0x80);
                } else {
                    $buf .= chr($byte);
                    return $buf;
                }
            }

            throw new \InvalidArgumentException("Value too large to be encoded as a VarLong");
        }
    }

    public static function readRoundedLFloat(string $str, int $accuracy) : float{
        return round(self::readLFloat($str), $accuracy);
    }

    public static function signInt(int $value) : int{
        return $value << 32 >> 32;
    }
    public static function unsignInt(int $value) : int{
        return $value & 0xffffffff;
    }
}
