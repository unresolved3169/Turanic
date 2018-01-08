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

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;

class Ice extends Transparent{

	protected $id = self::ICE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Ice";
	}

	public function getHardness() : float{
		return 0.5;
	}

    public function getLightFilter() : int{
        return 2;
    }

    public function getFrictionFactor() : float{
        return 0.98;
    }

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function onBreak(Item $item){
		if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) === 0){
			$this->getLevel()->setBlock($this, Block::get(Block::WATER), true);
		}
		return true;
	}

    public function ticksRandomly() : bool{
        return true;
    }

    public function onUpdate($type){
        if($type === Level::BLOCK_UPDATE_RANDOM){
            if($this->level->getHighestAdjacentBlockLight($this->x, $this->y, $this->z) >= 12){
                $this->level->useBreakOn($this);

                return $type;
            }
        }
        return false;
    }

	public function getDrops(Item $item) : array{
		if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
			return parent::getDrops($item);
		}else{
			return [];
		}
	}
}
