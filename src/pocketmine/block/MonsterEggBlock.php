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

use pocketmine\entity\Entity;
use pocketmine\entity\hostile\Silverfish;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;

class MonsterEggBlock extends Solid{

    protected $id = self::MONSTER_EGG_BLOCK;

    public function __construct($meta = 0){
        $this->meta = $meta;
    }

    public function getName(){
        return "Monster Egg Block";
    }

    public function getHardness(){
        return 0.75;
    }

    public function onBreak(Item $item){
        parent::onBreak($item);
        if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) == 0){
            $sf = Entity::createEntity("Silverfish", $this->level, Entity::createBaseNBT($this));
            if($sf instanceof Silverfish){
                $sf->spawnToAll();
            }
        }
    }

    public function getDrops(Item $item): array{
        if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
            return [
                [Item::GLASS, 0, 1],
            ];
        }else{
            return [];
        }
    }
}