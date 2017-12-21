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

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Color;

class Cauldron extends Spawnable {

	/**
	 * Cauldron constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if($nbt->hasTag("PotionId", ShortTag::class)){
			$nbt->setShort("PotionId", -1);
		}
		if($nbt->hasTag("SplashPotion", ByteTag::class)){
			$nbt->setByte("SplashPotion", 0);
		}
		if($nbt->hasTag("Items", ListTag::class)){
			$nbt->setTag(new ListTag("Items", []));
		}
		parent::__construct($level, $nbt);
	}

    /**
     * @return int
     */
    public function getPotionId() : int{
		return $this->namedtag->getShort("PotionId", -1);
	}

	/**
	 * @param int $potionId
	 */
	public function setPotionId(int $potionId){
		$this->namedtag->setShort("PotionId", $potionId);
		$this->onChanged();
	}

	/**
	 * @return bool
	 */
	public function hasPotion(){
		return $this->getPotionId() !== -1;
	}

	/**
	 * @return bool
	 */
	public function getSplashPotion(){
		return ($this->namedtag->getByte("SplashPotion") == 1);
	}

	/**
	 * @param $bool
	 */
	public function setSplashPotion(bool $bool){
		$this->namedtag->setByte("SplashPotion", ($bool == true) ? 1 : 0);
		$this->onChanged();
	}

	/**
	 * @return null|Color
	 */
	public function getCustomColor(){//
		if($this->isCustomColor()){
			$color = $this->namedtag->getInt("CustomColor");
			$green = ($color >> 8) & 0xff;
			$red = ($color >> 16) & 0xff;
			$blue = ($color) & 0xff;
			return Color::getRGB($red, $green, $blue);
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getCustomColorRed(){
		return ($this->namedtag->getInt("CustomColor") >> 16) & 0xff;
	}

	/**
	 * @return int
	 */
	public function getCustomColorGreen(){
		return ($this->namedtag->getInt("CustomColor") >> 8) & 0xff;
	}

	/**
	 * @return int
	 */
	public function getCustomColorBlue(){
		return ($this->namedtag->getInt("CustomColor")) & 0xff;
	}

	/**
	 * @return bool
	 */
	public function isCustomColor(){
		return $this->namedtag->hasTag("CustomColor", IntTag::class);
	}

	public function setCustomColor(Color $color){
		$this->namedtag->setInt("CustomColor", $color->toARGB());
		$this->onChanged();
	}

	public function clearCustomColor(){
		if(isset($this->namedtag->CustomColor)){
			unset($this->namedtag->CustomColor);
		}
		$this->onChanged();
	}

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		$nbt = new CompoundTag("", [
			new StringTag("id", Tile::CAULDRON),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new ShortTag("PotionId", $this->namedtag->getShort("PotionId", -1)),
			new ByteTag("SplashPotion", $this->namedtag->getByte("SplashPotion", 0)),
			new ListTag("Items", $this->namedtag->getTagValue("Items", ListTag::class, []))//unused?
		]);

		if($this->getPotionId() === -1 and $this->isCustomColor()){
			$nbt->CustomColor = $this->namedtag->getInt("CustomColor");
		}
		return $nbt;
	}
}
