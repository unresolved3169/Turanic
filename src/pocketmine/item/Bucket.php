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
use pocketmine\block\BlockFactory;
use pocketmine\block\Liquid;
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

	public function __construct(int $meta = 0){
		parent::__construct(self::BUCKET, $meta, "Bucket");
	}

	public function getMaxStackSize() : int{
        return $this->meta === Block::AIR ? 16 : 1; //empty buckets stack to 16
	}

    public function getFuelTime(): int{
        return ($this->meta == Block::LAVA or $this->meta == Block::FLOWING_LAVA) ? 20000 : 0;
    }

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
        $resultBlock = BlockFactory::get($this->meta);

        if($resultBlock instanceof Air){
            if($blockClicked instanceof Liquid and $blockClicked->getDamage() === 0){
                $stack = clone $this;

                $resultItem = $stack->pop();
                $resultItem->setDamage($blockClicked->getFlowingForm()->getId());
                $player->getServer()->getPluginManager()->callEvent($ev = new PlayerBucketFillEvent($player, $blockReplace, $face, $this, $resultItem));
                if(!$ev->isCancelled()){
                    $player->getLevel()->setBlock($blockClicked, BlockFactory::get(Block::AIR), true, true);
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
        }elseif($resultBlock instanceof Liquid){
            $resultItem = clone $this;
            $resultItem->setDamage(0);
            $player->getServer()->getPluginManager()->callEvent($ev = new PlayerBucketEmptyEvent($player, $blockReplace, $face, $this, $resultItem));
            if(!$ev->isCancelled()){
                $player->getLevel()->setBlock($blockReplace, $resultBlock->getFlowingForm(), true, true);
                $player->getLevel()->broadcastLevelSoundEvent($blockClicked->add(0.5, 0.5, 0.5), $resultBlock->getBucketEmptySound());

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

    public function getResidue(){
        return Item::get(Item::BUCKET, 0, 1);
    }

    public function getAdditionalEffects(): array{
        return [];
    }

    public function onConsume(Living $consumer){
        $consumer->removeAllEffects();
    }

}