<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneWire extends Flowable {

	const ON = 1;
	const OFF = 2;
	const PLACE = 3;
	const DESTROY = 4;

	protected $id = self::REDSTONE_WIRE;

	/** @var bool */
	protected $canProvidePower = true;

	/**
	 * RedstoneWire constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Redstone Wire";
	}

	/**
	 * @return int
	 */
	public function getStrength(){
		return $this->meta;
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return ($this->meta > 0);
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
        if ($type != Level::BLOCK_UPDATE_NORMAL && $type != Level::BLOCK_UPDATE_REDSTONE) {
            return 0;
        }

        if ($type == Level::BLOCK_UPDATE_NORMAL && !$this->canBePlacedOn($this->getSide(Vector3::SIDE_DOWN))) {
            $this->level->useBreakOn($this);
            return Level::BLOCK_UPDATE_NORMAL;
        }

        $this->calculateCurrentChanges(false);

        return Level::BLOCK_UPDATE_NORMAL;
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
        if ($face != Vector3::SIDE_UP || !$this->canBePlacedOn($target)) {
            return false;
        }

        $this->level->setBlock($block, $this, true, false);
        $this->calculateCurrentChanges(true);

        $horizontal = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];
        $vertical = [Vector3::SIDE_UP, Vector3::SIDE_DOWN];

        foreach ($vertical as $face) {
            $this->level->updateAroundRedstone($this->getSide($face), [static::getOppositeSide($face)]);
        }

        foreach ($vertical as $face) {
            $this->updateAround($this->getSide($face), static::getOppositeSide($face));
        }

        foreach ($horizontal as $face) {
            $v = $this->getSide($face);

            if ($this->level->getBlock($v)->isNormal()) {
                $this->updateAround($v->getSide(Vector3::SIDE_UP), Vector3::SIDE_DOWN);
            } else {
                $this->updateAround($v->getSide(Vector3::SIDE_DOWN), Vector3::SIDE_UP);
            }
        }
        return true;
	}

	private function calculateCurrentChanges(bool $force){
	    $meta = $this->meta;
        $maxStrength = $meta;
        $this->canProvidePower = false;
        $power = $this->getIndirectPower();

        $this->canProvidePower = true;

        if ($power > 0 && $power > $maxStrength - 1) {
            $maxStrength = $power;
        }

        $strength = 0;

        $horizontal = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];
        foreach ($horizontal as $face) {
            $v = $this->getSide($face);
            $flag = $v->getX() != $this->getX() || $v->getZ() != $this->getZ();

            if ($flag) {
                $strength = $this->getMaxCurrentStrength($v, $strength);
            }

            if ($this->level->getBlock($v)->isNormal() && !$this->level->getBlock($this->getSide(Vector3::SIDE_UP))->isNormal()) {
                if ($flag) {
                    $strength = $this->getMaxCurrentStrength($v->getSide(Vector3::SIDE_UP), $strength);
                }
            } else if ($flag && !$this->level->getBlock($v)->isNormal()) {
                $strength = $this->getMaxCurrentStrength($v->getSide(Vector3::SIDE_DOWN), $strength);
            }
        }

        if ($strength > $maxStrength) {
            $maxStrength = $strength - 1;
        } else if ($maxStrength > 0) {
            --$maxStrength;
        } else {
            $maxStrength = 0;
        }

        if ($power > $maxStrength - 1) {
            $maxStrength = $power;
        }

        $faces = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_UP, Vector3::SIDE_DOWN];
        if ($meta != $maxStrength) {
            $this->meta = $maxStrength;
            $this->level->setBlock($this, $this, false, false);

            $this->level->updateAroundRedstone($this);
            foreach ($faces as $face) {
                $this->level->updateAroundRedstone($this->getSide($face), [static::getOppositeSide($face)]);
            }
        } else if ($force) {
            foreach ($faces as $face) {
                $this->level->updateAroundRedstone($this->getSide($face), [static::getOppositeSide($face)]);
            }
        }
    }

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item){
        $this->level->setBlock($this, new Air(), true, true);

        $this->calculateCurrentChanges(false);

        $faces = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_UP, Vector3::SIDE_DOWN];
        foreach ($faces as $face) {
            $this->level->updateAroundRedstone($this->getSide($face));
        }

        $horizontal = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];
        foreach ($horizontal as $face) {
            $v = $this->getSide($face);

            if ($this->level->getBlock($v)->isNormal()) {
                $this->updateAround($v->getSide(Vector3::SIDE_UP), Vector3::SIDE_DOWN);
            } else {
                $this->updateAround($v->getSide(Vector3::SIDE_DOWN), Vector3::SIDE_UP);
            }
        }
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [
			[Item::REDSTONE, 0, 1]
		];
	}

    private function getIndirectPower() : int{
        $power = 0;
	    $faces = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_UP, Vector3::SIDE_DOWN];

	    foreach($faces as $face){
	        $blockPower = $this->getSide($face)->getWeakPower($face);

	        if($blockPower >= 15){
                return 15;
            }

            if($blockPower > $power){
                $power = $blockPower;
            }
        }

        return $power;
    }

    private function getMaxCurrentStrength(Vector3 $pos, int $maxStrength) : int{
        if($this->level->getBlockIdAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ()) != $this->getId()){
            return $maxStrength;
        }else{
            $strength = $this->level->getBlockDataAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ());
            return $strength > $maxStrength ? $strength : $maxStrength;
        }
    }

    private function updateAround(Vector3 $pos, int $face){
        if ($this->level->getBlock($pos)->getId() == Block::REDSTONE_WIRE) {
            $this->level->updateAroundRedstone($pos, [$face]);

            $faces = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST, Vector3::SIDE_UP, Vector3::SIDE_DOWN];

            foreach ($faces as $side) {
                $this->level->updateAroundRedstone($pos->getSide($side), [static::getOppositeSide($side)]);
            }
        }
    }

    private function canBePlacedOn(Vector3 $v){
        $b = $this->level->getBlock($v);

        return $b->isSolid() && !$b->isTransparent() && $b->getId() != Block::GLOWSTONE;
    }

    public function isRedstoneSource(){
        return $this->canProvidePower;
    }

    public function getWeakPower(int $side): int{
        if (!$this->canProvidePower) {
            return 0;
        } else {
            $power = $this->meta;

            if ($power == 0) {
                return 0;
            } else if ($side == Vector3::SIDE_UP) {
                return $power;
            } else {
                $horizontal = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];
                $list = [];

                foreach ($horizontal as $face) {
                    if ($this->isPowerSourceAt($face)) {
                        $list[] = $face;
                    }
                }

                if (in_array($side, $horizontal) && count($list) == 0) {
                    return $power;
                } else if (in_array($side, $list) && !in_array(Vector3::rotateYCCW($side), $list) && !in_array(Vector3::rotateY($side), $list)) {
                    return $power;
                } else {
                    return 0;
                }
            }
        }
    }

    private function isPowerSourceAt(int $side){
        $v = $this->getSide($side);
        $block = $this->level->getBlock($v);
        $flag = $block->isNormal();
        $flag1 = $this->level->getBlock($this->getSide(Vector3::SIDE_UP))->isNormal();
        return !$flag1 && $flag && $this->canConnectTo($this->level->getBlock($v->getSide(Vector3::SIDE_UP)), null) || ($this->canConnectTo($block, $side) || !$flag && $this->canConnectTo($this->level->getBlock($block->getSide(Vector3::SIDE_DOWN)), null));
    }

    protected function canConnectTo(Block $block, $side) : bool{
        if($block->getId() == Block::REDSTONE_WIRE){
            return true;
        }elseif($block instanceof RedstoneDiode){
            $face = $block->getFacing();
            return $face == $side || static::getOppositeSide($face) == $side;
        }else{
            return $block->isRedstoneSource() && $side != null;
        }
    }

}
