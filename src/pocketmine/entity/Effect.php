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

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Color;

class Effect {

	const SPEED = 1;
	const SLOWNESS = 2;
	const HASTE = 3;
	const SWIFTNESS = 3;
	const FATIGUE = 4;
	const MINING_FATIGUE = 4;
	const STRENGTH = 5;
	const HEALING = 6;
	const HARMING = 7;
	const JUMP = 8;
	const NAUSEA = 9;
	const CONFUSION = 9;
	const REGENERATION = 10;
	const DAMAGE_RESISTANCE = 11;
	const FIRE_RESISTANCE = 12;
	const WATER_BREATHING = 13;
	const INVISIBILITY = 14;
	const BLINDNESS = 15;
	const NIGHT_VISION = 16;
	const HUNGER = 17;
	const WEAKNESS = 18;
	const POISON = 19;
	const WITHER = 20;
	const HEALTH_BOOST = 21;
	const ABSORPTION = 22;
	const SATURATION = 23;
    const LEVITATION = 24;
    const FATAL_POISON = 25;

	const MAX_DURATION = INT32_MAX;

	/** @var Effect[] */
	protected static $effects;

	public static function init(){
		self::$effects = new \SplFixedArray(256);

		self::$effects[Effect::SPEED] = new Effect(Effect::SPEED, "%potion.moveSpeed", new Color(124, 175, 198));
		self::$effects[Effect::SLOWNESS] = new Effect(Effect::SLOWNESS, "%potion.moveSlowdown", new Color(90, 108, 129), true);
		self::$effects[Effect::SWIFTNESS] = new Effect(Effect::SWIFTNESS, "%potion.digSpeed", new Color(217, 192, 67));
		self::$effects[Effect::FATIGUE] = new Effect(Effect::FATIGUE, "%potion.digSlowDown", new Color(74, 66, 23), true);
		self::$effects[Effect::STRENGTH] = new Effect(Effect::STRENGTH, "%potion.damageBoost", new Color(147, 36, 35));
		self::$effects[Effect::HEALING] = new InstantEffect(Effect::HEALING, "%potion.heal", new Color(248, 36, 35));
		self::$effects[Effect::HARMING] = new InstantEffect(Effect::HARMING, "%potion.harm", new Color(67, 10, 9), true);
		self::$effects[Effect::JUMP] = new Effect(Effect::JUMP, "%potion.jump", new Color(34, 255, 76));
		self::$effects[Effect::NAUSEA] = new Effect(Effect::NAUSEA, "%potion.confusion", new Color(85, 29, 74), true);
		self::$effects[Effect::REGENERATION] = new Effect(Effect::REGENERATION, "%potion.regeneration", new Color(205, 92, 171));
		self::$effects[Effect::DAMAGE_RESISTANCE] = new Effect(Effect::DAMAGE_RESISTANCE, "%potion.resistance", new Color(153, 69, 58));
		self::$effects[Effect::FIRE_RESISTANCE] = new Effect(Effect::FIRE_RESISTANCE, "%potion.fireResistance", new Color(228, 154, 58));
		self::$effects[Effect::WATER_BREATHING] = new Effect(Effect::WATER_BREATHING, "%potion.waterBreathing", new Color(46, 82, 153));
		self::$effects[Effect::INVISIBILITY] = new Effect(Effect::INVISIBILITY, "%potion.invisibility", new Color(127, 131, 146));

		self::$effects[Effect::BLINDNESS] = new Effect(Effect::BLINDNESS, "%potion.blindness", new Color(31, 31, 35));
		self::$effects[Effect::NIGHT_VISION] = new Effect(Effect::NIGHT_VISION, "%potion.nightVision", new Color(31, 31, 161));
		self::$effects[Effect::HUNGER] = new Effect(Effect::HUNGER, "%potion.hunger", new Color(88, 188, 83));

		self::$effects[Effect::WEAKNESS] = new Effect(Effect::WEAKNESS, "%potion.weakness", new Color(72, 77, 72), true);
		self::$effects[Effect::POISON] = new Effect(Effect::POISON, "%potion.poison", new Color(78, 147, 49), true);
		self::$effects[Effect::WITHER] = new Effect(Effect::WITHER, "%potion.wither", new Color(53, 42, 39), true);
		self::$effects[Effect::HEALTH_BOOST] = new Effect(Effect::HEALTH_BOOST, "%potion.healthBoost", new Color(248, 125, 35));

		self::$effects[Effect::ABSORPTION] = new Effect(Effect::ABSORPTION, "%potion.absorption", new Color(37, 82, 165));
		self::$effects[Effect::SATURATION] = new Effect(Effect::SATURATION, "%potion.saturation", new Color(248, 36, 35));

        self::$effects[Effect::LEVITATION] = new Effect(Effect::LEVITATION, "%potion.levitation", new Color(206, 255, 255));
        self::$effects[Effect::FATAL_POISON] = new Effect(Effect::FATAL_POISON, "%potion.poison", new Color(78, 147, 49), true);
	}

    public static function registerEffect(Effect $effect){
        self::$effects[$effect->getId()] = $effect;
    }

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public static function getEffect(int $id){
		if(isset(self::$effects[$id])){
			return clone self::$effects[$id];
		}
		return null;
	}

	/**
	 * @param $name
	 *
	 * @return null|Effect
	 */
	public static function getEffectByName(string $name){
		if(defined(Effect::class . "::" . strtoupper($name))){
			return self::getEffect(constant(Effect::class . "::" . strtoupper($name)));
		}
		return null;
	}

	/** @var int */
	protected $id;
    /** @var string */
	protected $name;
    /** @var int */
	protected $duration;
    /** @var int */
	protected $amplifier = 0;
    /** @var Color */
	protected $color;
    /** @var bool */
	protected $visible = true;
    /** @var bool */
	protected $ambient = false;
    /** @var bool */
	protected $bad;

	public function __construct(int $id, string $name, Color $color, bool $isBad = false){
		$this->id = $id;
		$this->name = $name;
		$this->bad = $isBad;
		$this->color = $color;
	}

	/**
     * Returns the translation key used to translate this effect's name.
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
     * Returns the effect ID as per Minecraft BE
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
     * Sets the duration in ticks of the effect.
	 * @param $ticks
	 *
	 * @return $this
	 */
	public function setDuration(int $ticks){
        if($ticks < 0 or $ticks > INT32_MAX){
            throw new \InvalidArgumentException("Effect duration must be in range of 0 - " . INT32_MAX);
        }
		$this->duration = $ticks;
		return $this;
	}

    /**
     * Returns the duration remaining of the effect in ticks.
     * @return int
     */
    public function getDuration() : int{
		return $this->duration;
	}

	/**
     * Returns whether this effect will produce some visible effect, such as bubbles or particles.
	 * @return bool
	 */
	public function isVisible() : bool{
		return $this->visible;
	}

	/**
     * Changes the visibility of the effect.
	 * @param $bool
	 *
	 * @return $this
	 */
	public function setVisible(bool $bool){
		$this->visible = $bool;
		return $this;
	}

    /**
     * Returns the level of this effect, which is always one higher than the amplifier.
     * @return int
     */
    public function getEffectLevel() : int{
        return $this->amplifier + 1;
    }

	/**
     * Returns the amplifier of this effect.
	 * @return int
	 */
	public function getAmplifier() : int{
		return $this->amplifier;
	}

	/**
     * Sets the amplifier of this effect.
	 * @param int $amplifier
	 *
	 * @return $this
	 */
	public function setAmplifier(int $amplifier){
		$this->amplifier = ($amplifier & 0xff);
		return $this;
	}

	/**
     * Returns whether the effect originated from the ambient environment.
     * Ambient effects can originate from things such as a Beacon's area of effect radius.
     * If this flag is set, the amount of visible particles will be reduced by a factor of 5.
     *
	 * @return bool
	 */
	public function isAmbient() : bool{
		return $this->ambient;
	}

	/**
     * Sets the ambiency of this effect.
	 * @param bool $ambient
	 *
	 * @return $this
	 */
	public function setAmbient(bool $ambient = true){
		$this->ambient = $ambient;
		return $this;
	}

	/**
     * Returns whether this effect is harmful.
	 * @return bool
	 */
	public function isBad() : bool{
		return $this->bad;
	}

	/**
     * Returns whether the effect will do something on the current tick.
	 * @return bool
	 */
	public function canTick() : bool{
		switch($this->id){
			case Effect::FATAL_POISON:
			case Effect::POISON:
				if(($interval = (25 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::WITHER:
				if(($interval = (50 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::REGENERATION:
				if(($interval = (40 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::HUNGER:
				if($this->amplifier < 0){ // prevents hacking with amplifier -1
					return false;
				}
				if(($interval = 20) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::HEALING:
			case Effect::HARMING:
				return true;
			case Effect::SATURATION:
				if(($interval = (20 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
		}
		return false;
	}

	/**
     * Applies effect results to an entity.
	 * @param Entity $entity
	 */
	public function applyEffect(Entity $entity){
		switch($this->id){
            /** @noinspection PhpMissingBreakStatementInspection */
			case Effect::POISON:
				if($entity->getHealth() <= 1){
                    break;
                }
            case Effect::FATAL_POISON:
                if(!($entity instanceof Player and $entity->isCreative())){
                    $ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
                    $entity->attack($ev);
                }
            break;

			case Effect::WITHER:
                if(!($entity instanceof Player and $entity->isCreative())) {
                    $ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
                    $entity->attack($ev);
                }
				break;

			case Effect::REGENERATION:
				if($entity->getHealth() < $entity->getMaxHealth() and !($entity instanceof Player and $entity->isCreative())){
					$ev = new EntityRegainHealthEvent($entity, 1, EntityRegainHealthEvent::CAUSE_MAGIC);
					$entity->heal($ev);
				}
				break;
			case Effect::HUNGER:
				if($entity instanceof Human){
					$entity->exhaust(0.5 * $this->getEffectLevel(), PlayerExhaustEvent::CAUSE_POTION);
				}
				break;
			case Effect::HEALING:
				$level = $this->getEffectLevel();
				if(($entity->getHealth() + 4 * $level) <= $entity->getMaxHealth()){
					$ev = new EntityRegainHealthEvent($entity, 4 * $level, EntityRegainHealthEvent::CAUSE_MAGIC);
					$entity->heal($ev);
				}else{
					$ev = new EntityRegainHealthEvent($entity, $entity->getMaxHealth() - $entity->getHealth(), EntityRegainHealthEvent::CAUSE_MAGIC);
					$entity->heal($ev);
				}
				break;
			case Effect::HARMING:
				$level = $this->getEffectLevel();
				if(($entity->getHealth() - 6 * $level) >= 0){
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 6 * $level);
					$entity->attack($ev);
				}else{
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, $entity->getHealth());
					$entity->attack($ev);
				}
				break;
			case Effect::SATURATION:
				if($entity instanceof Human){
					if(Server::getInstance()->foodEnabled){
                        $entity->addFood($this->getEffectLevel());
                        $entity->addSaturation($this->getEffectLevel() * 2);
					}
				}
				break;
		}
	}

    /**
     * Returns a Color object representing this effect's particle colour.
     * @return Color
     */
    public function getColor() : Color{
		return clone $this->color;
	}

    /**
     * Sets the color of this effect.
     * @param Color $color
     */
    public function setColor(Color $color){
		$this->color = clone $color;
	}

    public function add(Entity $entity, Effect $oldEffect = null){
        if($entity instanceof Player){
            $pk = new MobEffectPacket();
            $pk->entityRuntimeId = $entity->getId();
            $pk->effectId = $this->getId();
            $pk->amplifier = $this->getAmplifier();
            $pk->particles = $this->isVisible();
            $pk->duration = $this->getDuration();
            if($oldEffect !== null){
                $pk->eventId = MobEffectPacket::EVENT_MODIFY;
            }else{
                $pk->eventId = MobEffectPacket::EVENT_ADD;
            }
            $entity->dataPacket($pk);
        }
        if($oldEffect !== null){
            $oldEffect->remove($entity, false);
        }
        switch($this->id){
            case Effect::INVISIBILITY:
                $entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
                $entity->setNameTagVisible(false);
                break;
            case Effect::SPEED:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
                $attr->setValue($attr->getValue() * (1 + 0.2 * $this->getEffectLevel()));
                break;
            case Effect::SLOWNESS:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
                $attr->setValue($attr->getValue() * (1 - 0.15 * $this->getEffectLevel()), true);
                break;
            case Effect::HEALTH_BOOST:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::HEALTH);
                $attr->setMaxValue($attr->getMaxValue() + 4 * $this->getEffectLevel());
                break;
            case Effect::ABSORPTION:
                $new = (4 * $this->getEffectLevel());
                if($new > $entity->getAbsorption()){
                    $entity->setAbsorption($new);
                }
                break;
        }
    }


    /**
     * Removes the effect from the entity, resetting any changed values back to their original defaults.
     *
     * @param Entity $entity
     * @param bool   $send
     */
    public function remove(Entity $entity, bool $send = true){
        if($send and $entity instanceof Player){
            $pk = new MobEffectPacket();
            $pk->entityRuntimeId = $entity->getId();
            $pk->eventId = MobEffectPacket::EVENT_REMOVE;
            $pk->effectId = $this->getId();

            $entity->dataPacket($pk);
        }
        switch($this->id){
            case Effect::INVISIBILITY:
                $entity->setDataFlag(Entity::DATA_FLAGS,Entity::DATA_FLAG_INVISIBLE, false);
                $entity->setNameTagVisible(true);
                break;
            case Effect::SPEED:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
                $attr->setValue($attr->getValue() / (1 + 0.2 * $this->getEffectLevel()));
                break;
            case Effect::SLOWNESS:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
                $attr->setValue($attr->getValue() / (1 - 0.15 * $this->getEffectLevel()));
                break;
            case Effect::HEALTH_BOOST:
                $attr = $entity->getAttributeMap()->getAttribute(Attribute::HEALTH);
                $attr->setMaxValue($attr->getMaxValue() - 4 * $this->getEffectLevel());
                break;
            case Effect::ABSORPTION:
                $entity->setAbsorption(0);
                break;
        }
    }

    public function __clone(){
        $this->color = clone $this->color;
    }
}
