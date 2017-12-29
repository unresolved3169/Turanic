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
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\Server;

class Lava extends Liquid {

	protected $id = self::FLOWING_LAVA;

	/**
	 * Lava constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getLightLevel(){
		return 15;
	}

	/**
	 * @return string
	 */
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
                $this->liquidCollide($colliding, Block::get(Block::OBSIDIAN));
            }elseif($this->getDamage() <= 4){
                $this->liquidCollide($colliding, Block::get(Block::COBBLESTONE));
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
		$ProtectL = 0;
		if(!$entity->hasEffect(Effect::FIRE_RESISTANCE)){
			$ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_LAVA, 4);
			if($entity->attack($ev) === true){
				$ev->useArmors();
			}
			$ProtectL = $ev->getFireProtectL();
		}

		$ev = new EntityCombustByBlockEvent($this, $entity, 15, $ProtectL);
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if(!$ev->isCancelled()){
			$entity->setOnFire($ev->getDuration());
		}

		$entity->resetFallDistance();
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$ret = $this->getLevel()->setBlock($this, $this, true, false);
		$this->getLevel()->scheduleUpdate($this, $this->tickRate());

		return $ret;
	}

}