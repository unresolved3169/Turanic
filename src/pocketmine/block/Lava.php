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

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\Server;

class Lava extends Liquid {

	protected $id = self::FLOWING_LAVA;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel() : int{
		return 15;
	}

	public function getName() : string{
		return "Lava";
	}

    public function tickRate() : int{
        return 30;
    }

    public function getFlowDecayPerBlock() : int{
	    if($this->level->getDimension() == DimensionIds::NETHER){
	        return 1;
        }
        return 2;
    }

    protected function checkForHarden(){
        $colliding = null;
        for($side = 1; $side <= 5; ++$side){ //don't check downwards side
            $blockSide = $this->getSide($side);
            if($blockSide instanceof Water){
                $colliding = $blockSide;
                break;
            }
        }
        if($colliding !== null){
            if($this->getDamage() === 0){
                $this->liquidCollide($colliding, BlockFactory::get(Block::OBSIDIAN));
            }elseif($this->getDamage() <= 4){
                $this->liquidCollide($colliding, BlockFactory::get(Block::COBBLESTONE));
            }
        }
    }

    protected function flowIntoBlock(Block $block, int $newFlowDecay){
        if($block instanceof Water){
            $block->liquidCollide($this, Block::get(Block::STONE));
        }else{
            parent::flowIntoBlock($block, $newFlowDecay);
        }
    }

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){
		$entity->fallDistance *= 0.5;

		$damage = true;
		if($entity instanceof Living and !$entity->hasEffect(Effect::FIRE_RESISTANCE)){
            $damage = false;
		}

		if($damage){
            $ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_LAVA, 4);
            $entity->attack($ev);
        }

        $ev = new EntityCombustByBlockEvent($this, $entity, 15);
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if(!$ev->isCancelled()){
			$entity->setOnFire($ev->getDuration());
		}

		$entity->resetFallDistance();
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$ret = $this->getLevel()->setBlock($this, $this, true, false);
		$this->getLevel()->scheduleDelayedBlockUpdate($this, $this->tickRate());

		return $ret;
	}

    public function getStillForm(): Block{
        return Block::get(Block::STILL_LAVA, $this->meta);
    }

    public function getFlowingForm(): Block{
        return Block::get(Block::FLOWING_LAVA, $this->meta);
    }

    public function getBucketFillSound() : int{
        return LevelSoundEventPacket::SOUND_BUCKET_FILL_LAVA;
 	}

	public function getBucketEmptySound() : int{
        return LevelSoundEventPacket::SOUND_BUCKET_EMPTY_LAVA;
 	}

}