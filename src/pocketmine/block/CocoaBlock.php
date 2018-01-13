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

use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class CocoaBlock extends Solid {

	protected $id = self::COCOA_BLOCK;
	protected $itemId = Item::DYE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Cocoa Block";
	}

	public function getHardness() : float{
		return 0.2;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($item->getId() === Item::DYE and $item->getDamage() === 0x0F){
			$block = clone $this;
			if($block->meta > 7){
				return false;
			}
			$block->meta += 4;
			Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));
			if(!$ev->isCancelled()){
				$this->getLevel()->setBlock($this, $ev->getNewState(), true, true);
			}
			$item->count--;
			return true;
		}
		return false;
	}

	public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$faces = [3, 4, 2, 5, 3, 4, 2, 5, 3, 4, 2, 5];
			if($this->getSide($faces[$this->meta])->isTransparent() === true){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}elseif($type === Level::BLOCK_UPDATE_RANDOM){
			if(mt_rand(0, 45) === 1){
				if($this->meta <= 7){
					$block = clone $this;
					$block->meta += 4;
					Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));
					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($this, $ev->getNewState(), true, true);
					}else{
						return Level::BLOCK_UPDATE_RANDOM;
					}
				}
			}else{
				return Level::BLOCK_UPDATE_RANDOM;
			}
		}
		return false;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($blockClicked->getId() === Block::WOOD and $blockClicked->getDamage() === 3){
			if($face !== 0 and $face !== 1){
				$faces = [
					2 => 0,
					3 => 2,
					4 => 3,
					5 => 1,
				];
				$this->meta = $faces[$face];
				$this->getLevel()->setBlock($blockReplace, BlockFactory::get(Item::COCOA_BLOCK, $this->meta), true);
				return true;
			}
		}
		return false;
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		if($this->meta >= 8){
			return [
			    Item::get($this->getItemId(), $this->getVariant(), 3)
            ];
		}

		return parent::getDropsForCompatibleTool($item);
	}

    public function isAffectedBySilkTouch(): bool{
        return false;
    }

	public function getVariant(): int{
        return 3;
    }

    public function ticksRandomly(): bool{
        return true;
    }
}
