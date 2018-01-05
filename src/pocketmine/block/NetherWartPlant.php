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
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class NetherWartPlant extends Flowable {

	protected $id = self::NETHER_WART_PLANT;

    protected $itemId = Item::NETHER_WART;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Nether Wart";
	}

    public function ticksRandomly() : bool{
        return true;
    }

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        $down = $this->getSide(Vector3::SIDE_DOWN);
        if($down->getId() === Block::SOUL_SAND){
            $this->getLevel()->setBlock($block, $this, false, true);

            return true;
        }

        return false;
	}

	public function onUpdate($type){
        switch($type){
            case Level::BLOCK_UPDATE_RANDOM:
                if($this->meta < 3 and mt_rand(0, 10) === 0){ //Still growing
                    $block = clone $this;
                    $block->meta++;
                    $this->getLevel()->getServer()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));

                    if(!$ev->isCancelled()){
                        $this->getLevel()->setBlock($this, $ev->getNewState(), false, true);

                        return $type;
                    }
                }
                break;
            case Level::BLOCK_UPDATE_NORMAL:
                if($this->getSide(Vector3::SIDE_DOWN)->getId() !== Block::SOUL_SAND){
                    $this->getLevel()->useBreakOn($this);
                    return $type;
                }
                break;
        }

		return false;
	}

	public function getDrops(Item $item) : array{
		$drops = [];
		if($this->meta >= 0x03){
			$fortunel = $item->getEnchantmentLevel(Enchantment::TYPE_MINING_FORTUNE);
			$fortunel = $fortunel > 3 ? 3 : $fortunel;
			$drops[] = [$this->getItemId(), 0, mt_rand(2, 4 + $fortunel)];
		}else{
			$drops[] = [$this->getItemId(), 0, 1];
		}
		return $drops;
	}
}
