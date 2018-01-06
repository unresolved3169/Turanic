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

use pocketmine\inventory\EnchantInventory;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\EnchantTable;

class EnchantingTable extends Transparent {

	protected $id = self::ENCHANTING_TABLE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel() : int{
		return 12;
	}

	public function getBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 0.75,
			$this->z + 1
		);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

        Tile::createTile(Tile::ENCHANT_TABLE, $this->getLevel(), EnchantTable::createNBT($this, $face, $item, $player));
		return true;
	}

	public function getHardness() : float{
		return 5;
	}

	public function getResistance() : float{
		return 6000;
	}

	public function getName() : string{
		return "Enchanting Table";
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function onActivate(Item $item, Player $player = null){
        if (!$this->getLevel()->getServer()->enchantingTableEnabled) {
            return true;
        }
        if ($player instanceof Player) {
            if ($player->isCreative() and $player->getServer()->limitedCreative) {
                return true;
            }
            $enchantTable = null;
            $this->getLevel()->setBlock($this, $this, true, true);
            Tile::createTile(Tile::ENCHANT_TABLE, $this->getLevel(), EnchantTable::createNBT($this));
        }

        $player->addWindow(new EnchantInventory($this));
        $player->craftingType = Player::CRAFTING_ENCHANT;

        return true;
    }

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
				[$this->id, 0, 1],
			];
		}else{
			return [];
		}
	}
}