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

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class WoodenButton extends Flowable {
	protected $id = self::WOODEN_BUTTON;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
        return 0.5;
    }

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

	public function getName() : string{
		return "Wooden Button";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($blockClicked->isTransparent() === false){
			$this->meta = $face;
			$this->getLevel()->setBlock($blockClicked, $this, true, false);
			return true;
		}
		return false;
	}

	public function isActivated(Block $from = null){
		return (($this->meta & 0x08) === 0x08);
	}

	public function onActivate(Item $item, Player $player = null){
		if(!$this->isActivated()){
			$this->meta ^= 0x08;
			$this->level->setBlock($this, $this, true, false);
			$this->level->addSound(new ButtonClickSound($this));
			$this->level->scheduleDelayedBlockUpdate($this, 30);
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
