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

declare(strict_types=1);

namespace pocketmine\utils;

#include <rules/DataPacket.h>
#ifndef COMPILE
#endif

use pocketmine\item\Item;

class BinaryStream extends \stdClass {

	public $offset;
	public $buffer;

	/**
	 * BinaryStream constructor.
	 *
	 * @param string $buffer
	 * @param int    $offset
	 */
	public function __construct($buffer = "", $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function reset(){
		$this->buffer = "";
		$this->offset = 0;
	}

	/**
	 * @param null $buffer
	 * @param int  $offset
	 */
	public function setBuffer($buffer = null, $offset = 0){
		$this->buffer = $buffer;
		$this->offset = (int) $offset;
	}

	/**
	 * @return int
	 */
	public function getOffset(){
		return $this->offset;
	}

	/**
	 * @return string
	 */
	public function getBuffer(){
		return $this->buffer;
	}

    public function getRemaining() : string{
        $str = substr($this->buffer, $this->offset);
        $this->offset = strlen($this->buffer);
        return $str;
    }

	/**
	 * @param $len
	 *
	 * @return bool|string
	 */
	public function get($len){
		if($len < 0){
			$this->offset = strlen($this->buffer) - 1;

			return "";
		}elseif($len === true){
			$str = substr($this->buffer, $this->offset);
			$this->offset = strlen($this->buffer);

			return $str;
		}

		return $len === 1 ? $this->buffer{$this->offset++} : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	/**
	 * @param $str
	 */
	public function put($str){
		$this->buffer .= $str;
	}

	/**
	 * @return bool
	 */
	public function getBool() : bool{
		return $this->get(1) !== "\x00";
	}

	/**
	 * @param $v
	 */
	public function putBool($v){
        $this->buffer .= ($v ? "\x01" : "\x00");
	}

	/**
	 * @return int|string
	 */
	public function getLong(){
		return Binary::readLong($this->get(8));
	}

	/**
	 * @param $v
	 */
	public function putLong($v){
		$this->buffer .= Binary::writeLong($v);
	}

	/**
	 * @return int
	 */
	public function getInt(){
		return Binary::readInt($this->get(4));
	}

	/**
	 * @param $v
	 */
	public function putInt($v){
		$this->buffer .= Binary::writeInt($v);
	}

    public function getLShort() : int{
        return Binary::readLShort($this->get(2));
    }

    public function getSignedLShort() : int{
        return Binary::readSignedLShort($this->get(2));
    }

    public function putLShort(int $v){
        $this->buffer .= Binary::writeLShort($v);
    }

	/**
	 * @return int|string
	 */
	public function getLLong(){
		return Binary::readLLong($this->get(8));
	}

	/**
	 * @param $v
	 */
	public function putLLong($v){
		$this->buffer .= Binary::writeLLong($v);
	}

	/**
	 * @return int
	 */
	public function getLInt(){
		return Binary::readLInt($this->get(4));
	}

	/**
	 * @param $v
	 */
	public function putLInt($v){
		$this->buffer .= Binary::writeLInt($v);
	}

	/**
	 * @return int
	 */
	public function getSignedShort(){
		return Binary::readSignedShort($this->get(2));
	}

	/**
	 * @param $v
	 */
	public function putShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

	/**
	 * @return int
	 */
	public function getShort(){
		return Binary::readShort($this->get(2));
	}

	/**
	 * @param $v
	 */
	public function putSignedShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

    public function getFloat() : float{
        return Binary::readFloat($this->get(4));
    }

	/**
	 * @param $v
	 */
	public function putFloat($v){
		$this->buffer .= Binary::writeFloat($v);
	}

	/**
	 * @param int $accuracy
	 *
	 * @return float
	 */
	public function getLFloat(int $accuracy = -1){
		return Binary::readLFloat($this->get(4), $accuracy);
	}

	/**
	 * @param $v
	 */
	public function putLFloat($v){
		$this->buffer .= Binary::writeLFloat($v);
	}

	/**
	 * @return mixed
	 */
	public function getTriad(){
		return Binary::readTriad($this->get(3));
	}

	/**
	 * @param $v
	 */
	public function putTriad($v){
		$this->buffer .= Binary::writeTriad($v);
	}

	/**
	 * @return mixed
	 */
	public function getLTriad(){
		return Binary::readLTriad($this->get(3));
	}

	/**
	 * @param $v
	 */
	public function putLTriad($v){
		$this->buffer .= Binary::writeLTriad($v);
	}

	/**
	 * @return int
	 */
	public function getByte() : int{
		return ord($this->buffer{$this->offset++});
	}

	/**
	 * @param $v
	 */
	public function putByte($v){
		$this->buffer .= chr((int) $v);
	}

	/**
	 * @return UUID
	 */
	public function getUUID(){
        $part1 = $this->getLInt();
        $part0 = $this->getLInt();
        $part3 = $this->getLInt();
        $part2 = $this->getLInt();
        return new UUID($part0, $part1, $part2, $part3);
	}

	/**
	 * @param UUID $uuid
	 */
	public function putUUID(UUID $uuid){
        $this->putLInt($uuid->getPart(1));
        $this->putLInt($uuid->getPart(0));
        $this->putLInt($uuid->getPart(3));
        $this->putLInt($uuid->getPart(2));
	}

	/**
	 * @return Item
	 */
	public function getSlot(){
		$id = $this->getVarInt();

		if($id <= 0){
			return Item::get(0, 0, 0);
		}
		$auxValue = $this->getVarInt();
		$data = $auxValue >> 8;
		if($data === 0x7fff){
			$data = -1;
		}
		$cnt = $auxValue & 0xff;

		$nbtLen = $this->getLShort();
		$nbt = "";

		if($nbtLen > 0){
			$nbt = $this->get($nbtLen);
		}

		$canPlaceOn = $this->getVarInt();
		if($canPlaceOn > 0){
			for($i = 0; $i < $canPlaceOn; ++$i){
				$this->getString();
			}
		}

		$canDestroy = $this->getVarInt();
		if($canDestroy > 0){
			for($i = 0; $i < $canDestroy; ++$i){
				$this->getString();
			}
		}

		return Item::get($id, $data, $cnt, $nbt);
	}


	/**
	 * @param Item $item
	 */
	public function putSlot(Item $item){
		if($item->getId() === 0){
			$this->putVarInt(0);

			return;
		}

		$this->putVarInt($item->getId());
		$auxValue = (($item->getDamage() & 0x7fff) << 8) | $item->getCount();
		$this->putVarInt($auxValue);
		$nbt = $item->getCompoundTag();
		$this->putLShort(strlen($nbt));
		$this->put($nbt);

		$this->putVarInt(0); //CanPlaceOn entry count (TODO)
		$this->putVarInt(0); //CanDestroy entry count (TODO)
	}

	/**
	 * @return bool|string
	 */
	public function getString(){
		return $this->get($this->getUnsignedVarInt());
	}

	/**
	 * @param $v
	 */
	public function putString($v){
		$this->putUnsignedVarInt(strlen($v));
		$this->put($v);
	}

    /**
     * Reads a 32-bit variable-length unsigned integer from the buffer and returns it.
     * @return int
     */
    public function getUnsignedVarInt() : int{
        return Binary::readUnsignedVarInt($this->buffer, $this->offset);
    }

	/**
	 * Writes an unsigned varint32 to the stream.
	 *
	 * @param $v
	 */
	public function putUnsignedVarInt($v){
		$this->put(Binary::writeUnsignedVarInt($v));
	}

    /**
     * Reads a 32-bit zigzag-encoded variable-length integer from the buffer and returns it.
     * @return int
     */
    public function getVarInt() : int{
        return Binary::readVarInt($this->buffer, $this->offset);
    }

	/**
	 * Writes a signed varint32 to the stream.
	 *
	 * @param $v
	 */
	public function putVarInt($v){
		$this->put(Binary::writeVarInt($v));
	}

	/**
	 * @return int
	 */
	public function getEntityId(){
		return $this->getVarInt();
	}

	/**
	 * @param $v
	 */
	public function putEntityId($v){
		$this->putVarInt($v);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function getBlockCoords(&$x, &$y, &$z){
		$x = $this->getVarInt();
		$y = $this->getUnsignedVarInt();
		$z = $this->getVarInt();
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function putBlockCoords($x, $y, $z){
		$this->putVarInt($x);
		$this->putUnsignedVarInt($y);
		$this->putVarInt($z);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function getVector3f(&$x, &$y, &$z){
		$x = $this->getLFloat(4);
		$y = $this->getLFloat(4);
		$z = $this->getLFloat(4);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function putVector3f(float $x, float $y, float $z){
		$this->putLFloat($x);
		$this->putLFloat($y);
		$this->putLFloat($z);
	}

	/**
	 * @return bool
	 */
	public function feof(){
		return !isset($this->buffer{$this->offset});
	}

    public function putSignedVarInt($v) {
        $this->put(Binary::writeSignedVarInt($v));
    }

    public function getSignedVarInt() {
        $result = $this->getVarInt();
        if ($result % 2 == 0) {
            $result = $result / 2;
        } else {
            $result = (-1) * ($result + 1) / 2;
        }
        return $result;
    }

    /**
     * Reads a 64-bit zigzag-encoded variable-length integer from the buffer and returns it.
     * @return int
     */
    public function getVarLong() : int{
        return Binary::readVarLong($this->buffer, $this->offset);
    }

    /**
     * Writes a 64-bit zigzag-encoded variable-length integer to the end of the buffer.
     * @param int
     */
    public function putVarLong($v){
        $this->buffer .= Binary::writeVarLong((int) $v);
    }

    /**
     * Reads a 64-bit variable-length integer from the buffer and returns it.
     * @return int
     */
    public function getUnsignedVarLong() : int{
        return Binary::readUnsignedVarLong($this->buffer, $this->offset);
    }

    /**
     * Writes a 64-bit variable-length integer to the end of the buffer.
     * @param int $v
     */
    public function putUnsignedVarLong($v){
        $this->buffer .= Binary::writeUnsignedVarLong((int) $v);
    }

    public function getRoundedLFloat(int $accuracy) : float{
        return Binary::readRoundedLFloat($this->get(4), $accuracy);
    }
}
