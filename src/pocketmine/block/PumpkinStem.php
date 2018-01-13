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
use pocketmine\Server;

class PumpkinStem extends Crops {

	protected $id = self::PUMPKIN_STEM;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Pumpkin Stem";
	}

	public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->isTransparent()){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}elseif($type === Level::BLOCK_UPDATE_RANDOM){
			if(mt_rand(0, 2) == 1){
				if($this->meta < 0x07){
					$block = clone $this;
					++$block->meta;
					Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));
					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($this, $ev->getNewState(), true);
					}

					return Level::BLOCK_UPDATE_RANDOM;
				}else{
					for($side = 2; $side <= 5; ++$side){
						$b = $this->getSide($side);
						if($b->getId() === self::PUMPKIN){
							return Level::BLOCK_UPDATE_RANDOM;
						}
					}
					$side = $this->getSide(mt_rand(2, 5));
					$d = $side->getSide(0);
					if($side->getId() === self::AIR and ($d->getId() === self::FARMLAND or $d->getId() === self::GRASS or $d->getId() === self::DIRT)){
						Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($side, new Pumpkin()));
						if(!$ev->isCancelled()){
							$this->getLevel()->setBlock($side, $ev->getNewState(), true);
						}
					}
				}
			}

			return Level::BLOCK_UPDATE_RANDOM;
		}

		return false;
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			Item::get(Item::PUMPKIN_SEEDS, 0, mt_rand(0, 2))
		];
	}

    public function getPickedItem(): Item{
        return Item::get(Item::PUMPKIN_SEEDS);
    }

    public function ticksRandomly(): bool{
        return true;
    }
}