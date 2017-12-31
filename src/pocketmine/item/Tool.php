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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;

abstract class Tool extends Durable {

    const TYPE_NONE = 0;
    const TYPE_SWORD = 1 << 0;
    const TYPE_SHOVEL = 1 << 1;
    const TYPE_PICKAXE = 1 << 2;
    const TYPE_AXE = 1 << 3;
    const TYPE_SHEARS = 1 << 4;

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}

    /**
     * TODO: Move this to each item
     *
     * @param Entity|Block $object
     * @return bool
     *
     */
	public function useOn($object){
        if($this->isUnbreakable()){
            return true;
        }

        $unbreakingl = $this->getEnchantmentLevel(Enchantment::TYPE_MINING_DURABILITY);
        $unbreakingl = $unbreakingl > 3 ? 3 : $unbreakingl;
        if(mt_rand(1, $unbreakingl + 1) !== 1){
            return true;
        }

        if($object instanceof Block){
            if(($object->getToolType() & $this->getBlockToolType()) !== 0){
                $this->applyDamage(1);
            }elseif(!$this->isShears() and $object->getBreakTime($this) > 0){
                $this->applyDamage(2);
            }
        }elseif($this->isHoe()){
            if(($object instanceof Block) and ($object->getId() === self::GRASS or $object->getId() === self::DIRT)){
                $this->applyDamage(1);
            }
        }elseif(($object instanceof Entity) and !$this->isSword()){
            $this->applyDamage(2);
        }else{
            $this->applyDamage(1);
        }

        return true;
	}

    public function isTool(){
        return true;
    }

    public function getMiningEfficiency(Block $block) : float{
        $efficiency = 1;
        if(($block->getToolType() & $this->getBlockToolType()) !== 0){
            $efficiency = $this->getBaseMiningEfficiency();
            //TODO: check Efficiency enchantment
        }

        return $efficiency;
    }

    protected function getBaseMiningEfficiency() : float{
        return 1;
    }
}
