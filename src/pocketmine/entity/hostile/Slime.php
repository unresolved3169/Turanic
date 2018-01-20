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

namespace pocketmine\entity\hostile;

use pocketmine\entity\Monster;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Slime extends Monster {
	const NETWORK_ID = self::SLIME;

	const SIZE_TINY = 0;
	const SIZE_SMALL = 1;
	const SIZE_BIG = 3;

	const DATA_SLIME_SIZE = 16;

	public $width = 0.3;
	public $height = 0;
	
	public $drag = 0.2;
	public $gravity = 0.3;

	/** @var int */
	private $slimeSize = 1;

	public function __construct(Level $level, CompoundTag $nbt){
        if(!isset($nbt->Size)){
            $this->setSlimeSize();
            $nbt->Size = new IntTag("Size", $this->slimeSize);
        }
        parent::__construct($level, $nbt);
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Slime";
	}
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth($this->getHealthFromSize());
		parent::initEntity();
	}

	public function getSlimeSize(){
	    return $this->slimeSize;
    }

    /**
     * @param int $size
     */
    public function setSlimeSize(int $size = null){
        if($size == null){
            $size = [0,1,3];
            $size = $size[array_rand($size)];
        }
        $this->slimeSize = $size;
        switch($size){
            case self::SIZE_TINY:
                $this->height = 0.51;
                $this->width = 0.51;
                break;
            case self::SIZE_SMALL:
                $this->height = 1.02;
                $this->width = 1.02;
                break;
            case self::SIZE_BIG:
                $this->height = 2.04;
                $this->width = 2.04;
                break;
        }
        $this->setMaxHealth($this->getHealthFromSize());
        $this->propertyManager->setPropertyValue(self::DATA_TYPE_INT,self::DATA_SLIME_SIZE, $this->slimeSize); // i am not sure
    }

    public function getHealthFromSize() : int{
        switch($this->slimeSize){
            case self::SIZE_TINY:
                return 3;
            case self::SIZE_SMALL:
                return 4;
            case self::SIZE_BIG:
                return 6;
        }
        return 3;
    }

    public function getXpDropAmount(): int{
        switch($this->slimeSize){
            case self::SIZE_TINY:
                return 1;
            case self::SIZE_SMALL:
                return 2;
            case self::SIZE_BIG:
                return 4;
        }
        return parent::getXpDropAmount();
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->Size = new IntTag("Size", $this->slimeSize);
    }
}