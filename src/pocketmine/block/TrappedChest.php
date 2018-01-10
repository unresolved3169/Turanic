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

use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Chest as TileChest;
use pocketmine\tile\Tile;

class TrappedChest extends RedstoneSource {
	protected $id = self::TRAPPED_CHEST;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getBoundingBox(){
		if($this->boundingBox === null){
			$this->boundingBox = $this->recalculateBoundingBox();
		}
		return $this->boundingBox;
	}

	public function isSolid() : bool{
		return true;
	}

	public function canBeFlowedInto() : bool{
		return false;
	}

	public function getHardness() : float{
		return 2.5;
	}

	public function getName() : string{
		return "Trapped Chest";
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	protected function recalculateBoundingBox(){
        return new AxisAlignedBB(
            $this->x + 0.025,
            $this->y,
            $this->z + 0.025,
            $this->x + 0.975,
            $this->y + 0.95,
            $this->z + 0.975
        );
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 2,
			1 => 5,
			2 => 3,
			3 => 4,
		];

		$chest = null;
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

		for($side = 2; $side <= 5; ++$side){
			if(($this->meta === 4 or $this->meta === 5) and ($side === 4 or $side === 5)){
				continue;
			}elseif(($this->meta === 3 or $this->meta === 2) and ($side === 2 or $side === 3)){
				continue;
			}
			$c = $this->getSide($side);
			if($c instanceof Chest and $c->getDamage() === $this->meta){
				$tile = $this->getLevel()->getTile($c);
				if($tile instanceof TileChest and !$tile->isPaired()){
					$chest = $tile;
					break;
				}
			}
		}

		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		$tile = Tile::createTile(Tile::CHEST, $this->getLevel(), TileChest::createNBT($this, $face, $item, $player));

		if($chest instanceof TileChest and $tile instanceof TileChest){
			$chest->pairWith($tile);
			$tile->pairWith($chest);
		}

		return true;
	}

	public function onBreak(Item $item, Player $player = null) : bool{
		$t = $this->getLevel()->getTile($this);
		if($t instanceof TileChest){
			$t->unpair();
		}
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$top = $this->getSide(1);
			if($top->isTransparent() !== true){
				return true;
			}

			$t = $this->getLevel()->getTile($this);
			$chest = null;
			if($t instanceof TileChest){
				$chest = $t;
			}else{
				$chest = Tile::createTile(Tile::CHEST, $this->getLevel(), TileChest::createNBT($this));
			}

			if(isset($chest->namedtag->Lock) and $chest->namedtag->Lock instanceof StringTag){
				if($chest->namedtag->Lock->getValue() !== $item->getCustomName()){
					return true;
				}
			}

			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}
			$player->addWindow($chest->getInventory());
			$this->level->updateAroundRedstone($this, [self::SIDE_DOWN, self::SIDE_NORTH, self::SIDE_SOUTH, self::SIDE_WEST, self::SIDE_EAST]);
		}

		return true;
	}

	public function getWeakPower(int $side): int{
        $playerCount = 0;

        $t = $this->getLevel()->getTile($this);

        if($t instanceof TileChest){
            $playerCount = count($t->getInventory()->getViewers());
        }

        return $playerCount < 0 ? 0 : $playerCount > 15 ? 15 : $playerCount;
    }

    public function isRedstoneSource(){
        return true;
    }
}