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

use pocketmine\entity\Entity;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class Cactus extends Transparent{

	protected $id = self::CACTUS;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.4;
	}

	public function hasEntityCollision(){
		return true;
	}

	public function getName() : string{
		return "Cactus";
	}

    protected function recalculateBoundingBox(){
        return new AxisAlignedBB(
            $this->x + 0.0625,
            $this->y + 0.0625,
            $this->z + 0.0625,
            $this->x + 0.9375,
            $this->y + 0.9375,
            $this->z + 0.9375
        );
    }

    public function ticksRandomly() : bool{
        return true;
    }

	public function onEntityCollide(Entity $entity){
		$ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_CONTACT, 1);
		if($entity->attack($ev) === true){
			$ev->useArmors();
		}
	}

	public function onUpdate($type){
        if($type === Level::BLOCK_UPDATE_NORMAL){
            $down = $this->getSide(Vector3::SIDE_DOWN);
            if($down->getId() !== self::SAND and $down->getId() !== self::CACTUS){
                $this->getLevel()->useBreakOn($this);
            }else{
                for($side = 2; $side <= 5; ++$side){
                    $b = $this->getSide($side);
                    if(!$b->canBeFlowedInto()){
                        $this->getLevel()->useBreakOn($this);
                    }
                }
            }
        }elseif($type === Level::BLOCK_UPDATE_RANDOM){
            if($this->getSide(Vector3::SIDE_DOWN)->getId() !== self::CACTUS){
                if($this->meta === 0x0f){
                    for($y = 1; $y < 3; ++$y){
                        $b = $this->getLevel()->getBlockAt($this->x, $this->y + $y, $this->z);
                        if($b->getId() === self::AIR){
                            Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($b, Block::get(Block::CACTUS)));
                            if(!$ev->isCancelled()){
                                $this->getLevel()->setBlock($b, $ev->getNewState(), true);
                            }
                        }
                    }
                    $this->meta = 0;
                    $this->getLevel()->setBlock($this, $this);
                }else{
                    ++$this->meta;
                    $this->getLevel()->setBlock($this, $this);
                }
            }
        }

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        $down = $this->getSide(Vector3::SIDE_DOWN);
        if($down->getId() === self::SAND or $down->getId() === self::CACTUS){
            $block0 = $this->getSide(Vector3::SIDE_NORTH);
            $block1 = $this->getSide(Vector3::SIDE_SOUTH);
            $block2 = $this->getSide(Vector3::SIDE_WEST);
            $block3 = $this->getSide(Vector3::SIDE_EAST);
            if($block0->isTransparent() === true and $block1->isTransparent() === true and $block2->isTransparent() === true and $block3->isTransparent() === true){
                $this->getLevel()->setBlock($this, $this, true);

                return true;
            }
        }

        return false;
	}

    public function getVariantBitmask() : int{
        return 0;
    }
}