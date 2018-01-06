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

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\BrewingStand as TileBrewingStand;
use pocketmine\tile\Tile;

class BrewingStand extends Transparent {

	protected $id = self::BREWING_STAND_BLOCK;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($blockReplace->getSide(Vector3::SIDE_DOWN)->isTransparent() === false){
			$bool = $this->getLevel()->setBlock($blockReplace, $this, true, true);
            Tile::createTile(Tile::BREWING_STAND, $this->getLevel(), TileBrewingStand::createNBT($this, $face, $item, $player));
			return $bool;
		}
		return false;
	}

	public function getHardness() : float{
		return 0.5;
	}

    public function canHarvestWithHand(): bool{
        return false;
	}

	public function getLightLevel() : int{
		return 1;
	}

	public function getName() : string{
		return "Brewing Stand";
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$t = $this->getLevel()->getTile($this);
			if($t instanceof TileBrewingStand){
				$brewingStand = $t;
			}else{
				$brewingStand = Tile::createTile(Tile::BREWING_STAND, $this->getLevel(), TileBrewingStand::createNBT($this));
			}

            if($player->isCreative() and $player->getServer()->limitedCreative){
                return true;
            }

            if($brewingStand->namedtag->hasTag("Lock", StringTag::class) and $brewingStand->namedtag->getString("Lock") !== $item->getCustomName()){
                return true;
            }

			$player->addWindow($brewingStand->getInventory());
		}
		return true;
	}

	public function getDrops(Item $item) : array{
		$drops = [];
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			$drops[] = [Item::BREWING_STAND, 0, 1];
		}
		return $drops;
	}
}
