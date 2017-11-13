<?php

/**
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link   http://www.pocketmine.net/
 *
 *
 */
declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\inventory\PlayerInventory;
use pocketmine\Player;

/**
 * Called when an entity takes damage.
 */
class EntityDamageEvent extends EntityEvent implements Cancellable{
    public static $handlerList = null;

    const MODIFIER_BASE = 0;
    const MODIFIER_RESISTANCE = 1;
    const MODIFIER_ARMOR = 2;
    const MODIFIER_PROTECTION = 3;
    const MODIFIER_STRENGTH = 4;
    const MODIFIER_WEAKNESS = 5;

    const CAUSE_CONTACT = 0;
    const CAUSE_ENTITY_ATTACK = 1;
    const CAUSE_PROJECTILE = 2;
    const CAUSE_SUFFOCATION = 3;
    const CAUSE_FALL = 4;
    const CAUSE_FIRE = 5;
    const CAUSE_FIRE_TICK = 6;
    const CAUSE_LAVA = 7;
    const CAUSE_DROWNING = 8;
    const CAUSE_BLOCK_EXPLOSION = 9;
    const CAUSE_ENTITY_EXPLOSION = 10;
    const CAUSE_VOID = 11;
    const CAUSE_SUICIDE = 12;
    const CAUSE_MAGIC = 13;
    const CAUSE_CUSTOM = 14;
    const CAUSE_STARVATION = 15;

    const CAUSE_LIGHTNING = 16;


    private $cause;
    private $fireProtectL = 0;
    /** @var array */
    private $modifiers;
    private $originals;
    private $usedArmors = [];


    /**
     * @param Entity    $entity
     * @param int       $cause
     * @param int|int[] $damage
     *
     * @throws \Exception
     */
    public function __construct(Entity $entity, $cause, $damage){
        $this->entity = $entity;
        $this->cause = $cause;
        if(is_array($damage)){
            $this->modifiers = $damage;
        }else{
            $this->modifiers = [
                self::MODIFIER_BASE => $damage
            ];
        }

        $this->originals = $this->modifiers;

        if(!isset($this->modifiers[self::MODIFIER_BASE])){
            throw new \InvalidArgumentException("BASE Damage modifier missing");
        }

        if($entity instanceof Player and $entity->getInventory() instanceof PlayerInventory){
            switch($cause) {
                case self::CAUSE_CONTACT:
                case self::CAUSE_ENTITY_ATTACK:
                case self::CAUSE_PROJECTILE:
                case self::CAUSE_FIRE:
                case self::CAUSE_LAVA:
                case self::CAUSE_BLOCK_EXPLOSION:
                case self::CAUSE_ENTITY_EXPLOSION:
                case self::CAUSE_LIGHTNING:
                    $points = 0;
                    foreach ($entity->getInventory()->getArmorContents() as $index => $i) {
                        if ($i->isArmor()) {
                            $points += $i->getArmorValue();
                            $this->usedArmors[$index] = 1;
                        }
                    }
                    break;
            }
        }
    }

    /**
     * @return int
     */
    public function getCause(){
        return $this->cause;
    }

    /**
     * @param int $type
     *
     * @return int
     */
    public function getOriginalDamage($type = self::MODIFIER_BASE){
        return $this->originals[$type] ?? 0;
    }

    /**
     * @param int $type
     *
     * @return int
     */
    public function getDamage($type = self::MODIFIER_BASE){
        return $this->modifiers[$type] ?? 0;
    }

    /**
     * @param float $damage
     * @param int   $type
     *
     * @throws \UnexpectedValueException
     */
    public function setDamage(float $damage, $type = self::MODIFIER_BASE){
        $this->modifiers[$type] = $damage;
    }

    /**
     * @param int $type
     *
     * @return bool
     */
    public function isApplicable($type){
        return isset($this->modifiers[$type]);
    }

    /**
     * @return int
     */
    public function getFinalDamage(){
        return array_sum($this->modifiers);
    }

    /**
     * notice: $usedArmors $index->$cost
     * $index: the $index of ArmorInventory
     * $cost:  the num of durability cost
     */
    public function getUsedArmors(){
        return $this->usedArmors;
    }

    /**
     * @return Int $fireProtectL
     */
    public function getFireProtectL(){
        return $this->fireProtectL;
    }

    /**
     * @return bool
     */
    public function useArmors(){
        if($this->entity instanceof Player){
            if($this->entity->isSurvival() and $this->entity->isAlive()){
                foreach($this->usedArmors as $index => $cost){
                    $i = $this->entity->getInventory()->getArmorItem($index);
                    if($i->isArmor()){
                        $this->entity->getInventory()->damageArmor($index, $cost);
                    }
                }
            }
            return true;
        }
        return false;
    }

}