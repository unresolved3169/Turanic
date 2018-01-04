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

namespace pocketmine\item;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Bucket extends Item implements Consumable {

    const TYPE_MILK = 1;
    const TYPE_WATER = Block::FLOWING_WATER;
    const TYPE_LAVA = Block::FLOWING_LAVA;

	/**
	 * Bucket constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::BUCKET, $meta, "Bucket");
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
        return $this->meta === Block::AIR ? 16 : 1; //empty buckets stack to 16
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		$targetBlock = Block::get($this->meta);

		if($targetBlock instanceof Air){
			if($blockClicked instanceof Liquid and $blockClicked->getDamage() === 0){
                $stack = clone $this;

                $resultItem = $stack->pop();
                $resultItem->setDamage($blockClicked->getFlowingForm()->getId());
				$player->getServer()->getPluginManager()->callEvent($ev = new PlayerBucketFillEvent($player, $blockReplace, $face, $this, $result));
				if(!$ev->isCancelled()){
					$player->getLevel()->setBlock($blockClicked, Block::get(Block::AIR), true, true);
                    $player->getLevel()->broadcastLevelSoundEvent($blockClicked->add(0.5, 0.5, 0.5), $blockClicked->getBucketFillSound());
					if($player->isSurvival()){
                        if($stack->getCount() === 0){
                            $player->getInventory()->setItemInHand($ev->getItem());
                        }else{
                            $player->getInventory()->setItemInHand($stack);
                            $player->getInventory()->addItem($ev->getItem());
                        }
					}else{
                        $player->getInventory()->addItem($ev->getItem());
                    }
					return true;
				}else{
					$player->getInventory()->sendContents($player);
				}
			}
		}elseif($targetBlock instanceof Liquid){
			$result = clone $this;
			$result->setDamage(0);
			$player->getServer()->getPluginManager()->callEvent($ev = new PlayerBucketEmptyEvent($player, $blockReplace, $face, $this, $result));
			if(!$ev->isCancelled()){
				//Only disallow water placement in the Nether, allow other liquids to be placed
				//In vanilla, water buckets are emptied when used in the Nether, but no water placed.
				if(!($player->getLevel()->getDimension() === Level::DIMENSION_NETHER and $targetBlock->getId() === self::WATER)){
					$player->getLevel()->setBlock($blockReplace, $targetBlock->getFlowingForm(), true, true);
                    $player->getLevel()->broadcastLevelSoundEvent($blockClicked->add(0.5, 0.5, 0.5), $targetBlock->getBucketEmptySound());
				}

				if($player->isSurvival()){
					$player->getInventory()->setItemInHand($ev->getItem());
				}
				return true;
			}else{
				$player->getInventory()->sendContents($player);
			}
		}

		return false;
	}

	public function getFuelTime(): int{
        return ($this->meta == Block::LAVA or $this->meta == Block::FLOWING_LAVA) ? 20000 : 0;
    }

    /**
     * Returns the leftover that this Consumable produces when it is consumed. For Items, this is usually air, but could
     * be an Item to add to a Player's inventory afterwards (such as a bowl).
     *
     * @return Item|Block|mixed
     */
    public function getResidue(){
        return Item::get(Item::BUCKET, 0, 1);
    }

    /**
     * @return Effect[]
     */
    public function getAdditionalEffects(): array{
        return [];
    }

    public function canBeConsumed(): bool{
        return $this->meta == self::TYPE_MILK;
    }

    /**
     * Called when this Consumable is consumed by mob, after standard resulting effects have been applied.
     *
     * @param Living $consumer
     */
    public function onConsume(Living $consumer){
        $consumer->removeAllEffects();
    }

}