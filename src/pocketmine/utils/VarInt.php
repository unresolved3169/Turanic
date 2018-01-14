<?php

namespace pocketmine\utils;

use Psr\Log\InvalidArgumentException;

class VarInt{

    public static function encodeZigZag32(int $v): int{
        // Note:  the right-shift must be arithmetic
        return (int)(($v << 1) ^ ($v >> 31));
    }

    public static function decodeZigZag32(int $v): int{
        return (int)($v >> 1) ^ -(int)($v & 1);
    }

    public static function encodeZigZag64(int $v): int{
        return ($v << 1) ^ ($v >> 63);
    }

    public static function decodeZigZag64(int $v){
        return (Utils::urshift($v, 1)) ^ -($v & 1);
    }

    private static function read(BinaryStream $stream, int $maxSize){
        $value = 0;
        $size = 0;
        while ((($b = $stream->getByte()) & 0x80) == 0x80) {
            $value |= (int)($b & 0x7F) << ($size++ * 7);
            if ($size >= $maxSize) {
                throw new InvalidArgumentException("VarLong too big");
            }
        }

        return $value | ((int)($b & 0x7F) << ($size * 7));
    }

    public static function readVarInt(BinaryStream $stream): int{
        return self::decodeZigZag32(self::readUnsignedVarInt($stream));
    }

    public static function readUnsignedVarInt(BinaryStream $stream): int{
        return self::read($stream, 5);
    }

    public static function readVarLong(BinaryStream $stream): int{
        return self::decodeZigZag64(self::readUnsignedVarLong($stream));
    }

    public static function readUnsignedVarLong(BinaryStream $stream): int{
        return self::read($stream, 10);
    }

    private static function write(BinaryStream $stream, int $value){
        do {
            $temp = (int)($value & 0b01111111);
            // Note: >>> means that the sign bit is shifted with the rest of the number rather than being left alone
            $value = Utils::urshift($value, 7);
            if ($value != 0) {
                $temp |= 0b10000000;
            }
            $stream->putByte($temp);
        } while ($value != 0);
    }

    public static function writeVarInt(BinaryStream $stream, int $value){
        self::writeUnsignedVarInt($stream, self::encodeZigZag32($value));
    }

    public static function writeUnsignedVarInt(BinaryStream $stream, int $value){
        self::write($stream, $value);
    }

    public static function writeVarLong(BinaryStream $stream, int $value){
        self::writeUnsignedVarLong($stream, self::encodeZigZag64($value));
    }

    public static function writeUnsignedVarLong(BinaryStream $stream, int $value){
        self::write($stream, $value);
    }

}