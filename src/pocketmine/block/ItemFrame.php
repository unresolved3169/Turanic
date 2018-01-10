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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ItemFrameAddItemSound;
use pocketmine\level\sound\ItemFrameRotateItemSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\ItemFrame as TileItemFrame;
use pocketmine\tile\Tile;

class ItemFrame extends Flowable {
	protected $id = Block::ITEM_FRAME_BLOCK;
	protected $itemId = Item::ITEM_FRAME;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Item Frame";
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if(!(($tile = $this->level->getTile($this)) instanceof TileItemFrame)){
		    /** @var TileItemFrame $tile */
            $tile = Tile::createTile(Tile::ITEM_FRAME, $this->getLevel(), TileItemFrame::createNBT($this));
		}

		if($tile->hasItem()){
			$tile->setItemRotation(($tile->getItemRotation() + 1) % 8);
			$this->getLevel()->addSound(new ItemFrameRotateItemSound($this));
		}elseif(!$item->isNull()){
            $tile->setItem($item->pop());
            $this->getLevel()->addSound(new ItemFrameAddItemSound($this));
            /*if($item->getId() === Item::FILLED_MAP){
                $tile->setMapID($item->getMapId()); // TODO
            }*/
        }

		return true;
	}

	public function onBreak(Item $item, Player $player = null) : bool{
	    /** @var TileItemFrame $tile */
		if(($tile = $this->level->getTile($this)) instanceof TileItemFrame){
			//TODO: add events
			if(lcg_value() <= $tile->getItemDropChance() and !$tile->getItem()->isNull()){
				$this->level->dropItem($tile->getBlock(), $tile->getItem());
			}
		}
		return parent::onBreak($item);
	}

	public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$sides = [
				0 => 4,
				1 => 5,
				2 => 2,
				3 => 3
			];
			if(!$this->getSide($sides[$this->meta])->isSolid()){
				$this->level->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($face === Vector3::SIDE_DOWN or $face === Vector3::SIDE_UP or !$blockClicked->isSolid()){
			return false;
		}

		$faces = [
			2 => 3,
			3 => 2,
			4 => 1,
			5 => 0
		];

		$this->meta = $faces[$face];
		$this->level->setBlock($blockReplace, $this, true, true);

        Tile::createTile(Tile::ITEM_FRAME, $this->getLevel(), TileItemFrame::createNBT($this, $face, $item, $player));

		return true;

	}

}