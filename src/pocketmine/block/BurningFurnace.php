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

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Furnace as TileFurnace;
use pocketmine\tile\Tile;

class BurningFurnace extends Solid {

	protected $id = self::BURNING_FURNACE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Burning Furnace";
	}

	public function getHardness() : float{
		return 3.5;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getLightLevel() : int{
		return 13;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$bool = $this->getLevel()->setBlock($blockReplace, $this, true, true);
        Tile::createTile(Tile::FURNACE, $this->getLevel(), TileFurnace::createNBT($this, $face, $item, $player));

		return $bool;
	}

	public function onBreak(Item $item){
		return $this->getLevel()->setBlock($this, new Air(), true, true);
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$furnace = $this->getLevel()->getTile($this);
			if(!($furnace instanceof TileFurnace)){
                $furnace = Tile::createTile(Tile::FURNACE, $this->getLevel(), TileFurnace::createNBT($this));
			}

            if($furnace->namedtag->hasTag("Lock", StringTag::class) and $furnace->namedtag->getString("Lock") !== $item->getCustomName()){
                return true;
			}

			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}
			$player->addWindow($furnace->getInventory());
		}

		return true;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return [
			    Item::get(Item::FURNACE)
            ];
		}

		return [];
	}

	public function canHarvestWithHand(): bool{
        return false;
    }
}