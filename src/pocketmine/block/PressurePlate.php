<?php

/*
 *
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
 *
*/

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ClickSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class PressurePlate extends Flowable {

    protected $onPitch;
    protected $offPitch;

	/**
	 * PressurePlate constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canPassThrough(){
        return true;
    }

    protected function recalculateBoundingBox(){
        if ($this->isActivated()) {
            return new AxisAlignedBB($this->x + 0.0625, $this->y, $this->z + 0.0625, $this->x + 0.9375, $this->y + 0.03125, $this->z + 0.9375);
        } else {
            return new AxisAlignedBB($this->x + 0.0625, $this->y, $this->z + 0.0625, $this->x + 0.9375, $this->y + 0.0625, $this->z + 0.9375);
        }
    }

    protected function recalculateCollisionBoxes(): array{
        return [new AxisAlignedBB($this->x + 0.125, $this->y, $this->z + 0.125, $this->x + 0.875, $this->y + 0.25, $this->z + 0.875)];
    }

    public function isRedstoneSource(){
        return true;
    }

    /**
	 * @return bool
	 */
	public function hasEntityCollision(){
		return true;
	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){
        $power = $this->getDamage();

        if($power == 0){
            $this->updateState($power);
        }
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return ($this->meta != 0);
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
	    switch($type){
            case Level::BLOCK_UPDATE_NORMAL:
                $below = $this->getSide(Vector3::SIDE_DOWN);
                if($below->isTransparent()){
                    $this->getLevel()->useBreakOn($this);
                }
                break;
            case Level::BLOCK_UPDATE_SCHEDULED:
                $power = $this->getDamage();

                if($power > 0){
                    $this->updateState($power);
                }
                break;
        }
		return true;
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
	 * @return bool|void
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$below = $this->getSide(Vector3::SIDE_DOWN);
		if($below->isTransparent()) return;
		$this->getLevel()->setBlock($block, $this, true, false);
	}

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true, true);

        if($this->getDamage() > 0){
            $this->level->updateAroundRedstone($this);
            $this->level->updateAroundRedstone($this->getSide(self::SIDE_DOWN));
        }
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 0.5;
	}

	/**
	 * @return float
	 */
	public function getResistance(){
		return 2.5;
	}

    public function canHarvestWithHand(): bool{
        return false;
    }

    public function getWeakPower(int $side): int{
        return $this->meta;
    }

    public function updateState(int $oldStrength){
	    $strength = $this->computeRedstoneStrength();
	    $wasPowered = $oldStrength > 0;
	    $isPowered = $strength > 0;

	    if($oldStrength != $strength){
	        $this->meta = $strength;
	        $this->level->setBlock($this, $this, false, false);
            $this->level->updateAroundRedstone($this);
            $this->level->updateAroundRedstone($this->getSide(self::SIDE_DOWN));

            if(!$isPowered && $wasPowered){ // close
                $this->level->addSound(new ClickSound($this, $this->offPitch));
            }elseif($isPowered && !$wasPowered){
                $this->level->addSound(new ClickSound($this, $this->onPitch));
            }
        }

        if($isPowered){
            $this->level->scheduleUpdate($this, 20);
        }
    }

    protected abstract function computeRedstoneStrength() : int;
}
