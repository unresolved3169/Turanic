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

class Lever extends Flowable {
	protected $id = self::LEVER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Lever";
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
            $side = $this->isActivated() ? $this->meta ^ 0x08 : $this->meta;
			$faces = [
				5 => 0,
				6 => 0,
				3 => 2,
				1 => 4,
				4 => 3,
				2 => 5,
				0 => 1,
				7 => 1,
			];

			$block = $this->getSide($faces[$side]);
			if(!$block->isSolid()){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if(!$blockClicked->isTransparent() && $blockClicked->isSolid()){
			$faces = [
				3 => 3,
				2 => 4,
				4 => 2,
				5 => 1,
			];
			if($face === 0){
				$to = $player instanceof Player ? $player->getDirection() : 0;
				$this->meta = ($to % 2 != 1 ? 0 : 7);
			}elseif($face === 1){
				$to = $player instanceof Player ? $player->getDirection() : 0;
				$this->meta = ($to % 2 != 1 ? 6 : 5);
			}else{
				$this->meta = $faces[$face];
			}
			$this->getLevel()->setBlock($blockReplace, $this, true, true);
			return true;
		}
		return false;
	}

	public function onActivate(Item $item, Player $player = null){
		$this->meta ^= 0x08;
		$this->getLevel()->setBlock($this, $this, false, true);
		$this->getLevel()->addSound(new ButtonClickSound($this));
        $this->level->updateAroundRedstone($this);
		return true;
	}

	public function onBreak(Item $item){
		if($this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
            $this->level->updateAroundRedstone($this);
        }
		$this->getLevel()->setBlock($this, new Air(), true, false);
	}

	public function isActivated(Block $from = null){
        return ($this->meta & 0x08) > 0;
	}

	public function getHardness() : float{
		return 0.5;
	}

	public function isRedstoneSource(){
        return true;
    }

    public function getWeakPower(int $side): int{
        return $this->isActivated() ? 15 : 0;
    }
}