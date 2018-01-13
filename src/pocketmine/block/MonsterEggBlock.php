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

use pocketmine\entity\Entity;
use pocketmine\entity\hostile\Silverfish;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;

class MonsterEggBlock extends Solid{

    protected $id = self::MONSTER_EGG_BLOCK;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Monster Egg Block";
    }

    public function getHardness() : float{
        return 0.75;
    }

    public function onBreak(Item $item, Player $player = null) : bool{
        if($item->getEnchantmentLevel(Enchantment::SILK_TOUCH) == 0){
            $sf = Entity::createEntity("Silverfish", $this->level, Entity::createBaseNBT($this));
            if($sf instanceof Silverfish){
                $sf->spawnToAll();
            }
        }
        return parent::onBreak($item, $player);
    }

    public function getDropsForCompatibleTool(Item $item): array{
        return [];
    }
}