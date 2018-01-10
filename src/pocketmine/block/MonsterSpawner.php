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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\MobSpawner;
use pocketmine\tile\Tile;

class MonsterSpawner extends Solid {

	protected $id = self::MONSTER_SPAWNER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 5;
	}

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_WOODEN;
    }

	public function getName() : string{
		return "Monster Spawner";
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($this->getDamage() == 0){
			if($item->getId() == Item::SPAWN_EGG){
				$tile = $this->getLevel()->getTileAt($this->x, $this->y, $this->z);
				if(!($tile instanceof MobSpawner)){
                    $tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), MobSpawner::createNBT($this));
				}
				$this->meta = $item->getDamage();
				$tile->setEntityId($item->getDamage());
				return true;
			}
		}
		return false;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), MobSpawner::createNBT($this, $face, $item, $player));
		return true;
	}

	public function getDropsForCompatibleTool(Item $item): array{
        $tile = $this->getLevel()->getTileAt($this->x, $this->y, $this->z);
        if(!$tile instanceof MobSpawner) return [];
        if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
            return [
                Item::get($this->id, $tile->getEntityId(), 1, $tile->getNBT())
            ];
        }

        return [];
    }
}