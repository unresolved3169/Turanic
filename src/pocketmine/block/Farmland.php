<?php

/*
 *
 *
 * _______  _
 *   |__   __|   (_)
 *   | |_   _ _ __ __ _ _ __  _  ___
 *   | | | | | '__/ _` | '_ \| |/ __|
 *   | | |_| | | | (_| | | | | | (__
 *   |_|\__,_|_|  \__,_|_| |_|_|\___|
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

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\level\weather\Weather;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\utils\Color;

class Farmland extends Solid {

	protected $id = self::FARMLAND;

	/**
	 * Farmland constructor.
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
		return "Farmland";
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 0.6;
	}

	public function getResistance(){
  return 3;
	}

 /**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 0.9375,
			$this->z + 1
		);
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [
			[Item::DIRT, 0, 1],
		];
	}

	public function onUpdate($type){
	 if($type == Level::BLOCK_UPDATE_RANDOM){
	  $up = $this->getSide(1);
   $x = $this->x;
   $y = $this->y;
   $z = $this->z;
   $v = new Vector3($x, $y, $z);
   if($up instanceof Crops){
 return 0;
   }
   if($up->isSolid()){
	   $this->level->setBlock($this, new Dirt(), true, true);
 return Level::BLOCK_UPDATE_RANDOM;
	  }

	  $found = false;
   if($this->level->getWeather()->getWeather() == Weather::RAIN){
 $found = true;
   }else{
 for($x = $this->x - 4; $x <= $this->x + 4; $x++){
  for($z = $this->z - 4; $z <= $this->z + 4; $z++){
   for($y = $this->y; $y <= $this->y + 1; $y++){
    if($this->x == $x && $this->y == $y && $this->z == $z){
  continue;
    }
    $v = new Vector3($x, $y, $z);
    $blockid = $this->level->getBlockIdAt($v->getFloorX(), $v->getFloorY(), $v->getFloorZ());
    switch($blockid){
  case self::WATER:
  case self::STILL_WATER:
   $found = true;
   break;
    }
    if($found) break;
   }
  }
 }
   }

   $block = $this->level->getBlock($v->setComponents($x, $y - 1, $z));
   if($found || $block instanceof Water){
 if($this->meta < 7){
  $this->meta = 7;
  $this->level->setBlock($this, $this, true, false);
 }
 return Level::BLOCK_UPDATE_RANDOM;
   }

   if($this->meta > 0){
 $this->meta--;
 $this->level->setBlock($this, $this, true, false);
   }else{
 $this->level->setBlock($this, new Dirt(), true, true);
   }

   return Level::BLOCK_UPDATE_RANDOM;
  }

  return 0;
 }

 public function getColor(){
	 return new Color(183, 106, 47);
 }
}
