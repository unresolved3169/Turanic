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

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class WoodenButton extends Flowable {
	protected $id = self::WOODEN_BUTTON;

	/**
	 * WoodenButton constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getResistance(){
        return 2.5;
    }

    public function getHardness(){
        return 0.5;
    }

    /**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
	    switch($type){
            case Level::BLOCK_UPDATE_NORMAL:
                if($this->getSide($this->getOpposite())->isTransparent()){
                    $this->getLevel()->useBreakOn($this);

                    return Level::BLOCK_UPDATE_NORMAL;
                }
                break;
            case Level::BLOCK_UPDATE_SCHEDULED:

                break;
        }
		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->isActivated()){
				$this->meta ^= 0x08;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->getLevel()->addSound(new ButtonClickSound($this));
                $this->level->updateAroundRedstone($this);
                $this->level->updateAroundRedstone($this->getSide($this->getOpposite()));
			}
			return Level::BLOCK_UPDATE_SCHEDULED;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Wooden Button";
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($target->isTransparent() === false){
			$this->meta = $face;
			$this->getLevel()->setBlock($block, $this, true, false);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return (($this->meta & 0x08) === 0x08);
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
		if(!$this->isActivated()){
			$this->meta ^= 0x08;
			$this->level->setBlock($this, $this, true, false);
			$this->level->addSound(new ButtonClickSound($this));
			$this->level->scheduleUpdate($this, 30);
			$this->level->updateAroundRedstone($this);
			$this->level->updateAroundRedstone($this->getSide($this->getOpposite()));
		}
		return true;
	}

	public function getOpposite() : int{
	    $side = $this->isActivated() ? $this->meta ^ 0x08 : $this->meta;
	    return self::getOppositeSide($side);
    }

    public function getWeakPower(int $side): int{
        return $this->isActivated() ? 15 : 0;
    }

    public function isRedstoneSource(){
        return true;
    }
}
