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
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\utils\Color;

class Cauldron extends Spawnable {

    const TAG_POTION_ID = "PotionId";
    const TAG_SPLASH_POTION = "SplashPotion";
    const TAG_CUSTOM_COLOR = "CustomColor";

	/**
	 * Cauldron constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_POTION_ID, ShortTag::class)){
			$nbt->setShort(self::TAG_POTION_ID, -1);
		}
		if(!$nbt->hasTag(self::TAG_SPLASH_POTION, ByteTag::class)){
			$nbt->setByte(self::TAG_SPLASH_POTION, 0);
		}
		if(!$nbt->hasTag("Items", ListTag::class)){
			$nbt->setTag(new ListTag("Items", [], NBT::TAG_Compound));
		}
		parent::__construct($level, $nbt);
	}

    /**
     * @return int
     */
    public function getPotionId() : int{
		return $this->namedtag->getShort(self::TAG_POTION_ID);
	}

	/**
	 * @param int $potionId
	 */
	public function setPotionId(int $potionId){
		$this->namedtag->setShort(self::TAG_POTION_ID, $potionId);
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
		return ($this->namedtag->getByte(self::TAG_SPLASH_POTION) == 1);
	}

	/**
	 * @param $bool
	 */
	public function setSplashPotion(bool $bool){
		$this->namedtag->setByte(self::TAG_SPLASH_POTION, ($bool == true) ? 1 : 0);
		$this->onChanged();
	}

	/**
	 * @return null|Color
	 */
	public function getCustomColor(){//
		if($this->isCustomColor()){
			$color = $this->namedtag->getInt(self::TAG_CUSTOM_COLOR);
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
		return ($this->namedtag->getInt(self::TAG_CUSTOM_COLOR) >> 16) & 0xff;
	}

	/**
	 * @return int
	 */
	public function getCustomColorGreen(){
		return ($this->namedtag->getInt(self::TAG_CUSTOM_COLOR) >> 8) & 0xff;
	}

	/**
	 * @return int
	 */
	public function getCustomColorBlue(){
		return ($this->namedtag->getInt(self::TAG_CUSTOM_COLOR)) & 0xff;
	}

	/**
	 * @return bool
	 */
	public function isCustomColor(){
		return $this->namedtag->hasTag(self::TAG_CUSTOM_COLOR, IntTag::class);
	}

	public function setCustomColor(Color $color){
		$this->namedtag->setInt(self::TAG_CUSTOM_COLOR, $color->toARGB());
		$this->onChanged();
	}

	public function clearCustomColor(){
		if($this->namedtag->hasTag(self::TAG_CUSTOM_COLOR)){
			$this->namedtag->removeTag(self::TAG_CUSTOM_COLOR);
		}
		$this->onChanged();
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
	    $nbt->setTag($this->namedtag->getTag(self::TAG_POTION_ID));
	    $nbt->setTag($this->namedtag->getTag(self::TAG_SPLASH_POTION));
	    $nbt->setTag($this->namedtag->getTag("Items"));

        if($this->getPotionId() === -1 and $this->isCustomColor()){
            $nbt->setTag($this->namedtag->getTag(self::TAG_CUSTOM_COLOR));
        }
    }
}
