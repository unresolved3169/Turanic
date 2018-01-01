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

use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\utils\Color;


// TODO : OPTIMIZE
class Potion extends Item implements Consumable {

	//No effects
	const WATER = 0, WATER_BOTTLE = 0;
	const MUNDANE = 1;
	const LONG_MUNDANE = 2, MUNDANE_EXTENDED = 2;
	const THICK = 3;
	const AWKWARD = 4;

	//Actual potions
	const NIGHT_VISION = 5;
	const LONG_NIGHT_VISION = 6, NIGHT_VISION_T = 6;
	const INVISIBILITY = 7;
	const LONG_INVISIBILITY = 8, INVISIBILITY_T = 8;
	const LEAPING = 9;
	const LONG_LEAPING = 10, LEAPING_T = 10;
	const STRONG_LEAPING = 11, LEAPING_TWO = 11;
	const FIRE_RESISTANCE = 12;
	const LONG_FIRE_RESISTANCE = 13, FIRE_RESISTANCE_T = 13;
	const SWIFTNESS = 14;
	const LONG_SWIFTNESS = 15, SWIFTNESS_T = 15;
	const STRONG_SWIFTNESS = 16, SWIFTNESS_TWO = 16;
	const SLOWNESS = 17;
	const LONG_SLOWNESS = 18, SLOWNESS_T = 18;
	const WATER_BREATHING = 19;
	const LONG_WATER_BREATHING = 20, WATER_BREATHING_T = 20;
	const HEALING = 21;
	const STRONG_HEALING = 22, HEALING_TWO = 22;
	const HARMING = 23;
	const STRONG_HARMING = 24, HARMING_TWO = 24;
	const POISON = 25;
	const LONG_POISON = 26, POISON_T = 26;
	const STRONG_POISON = 27, POISON_TWO = 27;
	const REGENERATION = 28;
	const LONG_REGENERATION = 29, REGENERATION_T = 29;
	const STRONG_REGENERATION = 30, REGENERATION_TWO = 30;
	const STRENGTH = 31;
	const LONG_STRENGTH = 32, STRENGTH_T = 32;
	const STRONG_STRENGTH = 33, STRENGTH_TWO = 33;
	const WEAKNESS = 34;
	const LONG_WEAKNESS = 35, WEAKNESS_T = 35;
	const WITHER = 36, DECAY = 36;

    const POTIONS = [
        self::WATER_BOTTLE => false,
        self::MUNDANE => false,
        self::MUNDANE_EXTENDED => false,
        self::THICK => false,
        self::AWKWARD => false,

        self::NIGHT_VISION => [Effect::NIGHT_VISION, (180 * 20), 0],
        self::NIGHT_VISION_T => [Effect::NIGHT_VISION, (480 * 20), 0],

        self::INVISIBILITY => [Effect::INVISIBILITY, (180 * 20), 0],
        self::INVISIBILITY_T => [Effect::INVISIBILITY, (480 * 20), 0],

        self::LEAPING => [Effect::JUMP, (180 * 20), 0],
        self::LEAPING_T => [Effect::JUMP, (480 * 20), 0],
        self::LEAPING_TWO => [Effect::JUMP, (90 * 20), 1],

        self::FIRE_RESISTANCE => [Effect::FIRE_RESISTANCE, (180 * 20), 0],
        self::FIRE_RESISTANCE_T => [Effect::FIRE_RESISTANCE, (480 * 20), 0],

        self::SWIFTNESS => [Effect::SPEED, (180 * 20), 0],
        self::SWIFTNESS_T => [Effect::SPEED, (480 * 20), 0],
        self::SWIFTNESS_TWO => [Effect::SPEED, (90 * 20), 1],

        self::SLOWNESS => [Effect::SLOWNESS, (90 * 20), 0],
        self::SLOWNESS_T => [Effect::SLOWNESS, (240 * 20), 0],

        self::WATER_BREATHING => [Effect::WATER_BREATHING, (180 * 20), 0],
        self::WATER_BREATHING_T => [Effect::WATER_BREATHING, (480 * 20), 0],

        self::HEALING => [Effect::HEALING, (1), 0],
        self::HEALING_TWO => [Effect::HEALING, (1), 1],

        self::HARMING => [Effect::HARMING, (1), 0],
        self::HARMING_TWO => [Effect::HARMING, (1), 1],

        self::POISON => [Effect::POISON, (45 * 20), 0],
        self::POISON_T => [Effect::POISON, (120 * 20), 0],
        self::POISON_TWO => [Effect::POISON, (22 * 20), 1],

        self::REGENERATION => [Effect::REGENERATION, (45 * 20), 0],
        self::REGENERATION_T => [Effect::REGENERATION, (120 * 20), 0],
        self::REGENERATION_TWO => [Effect::REGENERATION, (22 * 20), 1],

        self::STRENGTH => [Effect::STRENGTH, (180 * 20), 0],
        self::STRENGTH_T => [Effect::STRENGTH, (480 * 20), 0],
        self::STRENGTH_TWO => [Effect::STRENGTH, (90 * 20), 1],

        self::WEAKNESS => [Effect::WEAKNESS, (90 * 20), 0],
        self::WEAKNESS_T => [Effect::WEAKNESS, (240 * 20), 0]
    ];

	/**
	 * Potion constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::POTION, $meta, self::getNameByMeta($meta));
	}

	/**
	 * @param int $meta
	 *
	 * @return Color
	 */
	public static function getColor(int $meta) : Color{
		$effect = Effect::getEffect(self::getEffectId($meta));
		if($effect !== null){
			return $effect->getColor();
		}
		return new Color(0, 0, 0);
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}

	/**
	 * @return array
	 */
	public function getEffects() : array{
		return self::getPotionEffectsById($this->meta);
	}

	/**
	 * @param int $id
	 *
	 * @return Effect[]
	 */
	public static function getEffectsById(int $id) : array{
		return self::getPotionEffectsById($id);
	}


	/**
	 * @param Living $human
	 */
	public function onConsume(Living $human){
	}

	public function getResidue(){
        return Item::get(Item::GLASS_BOTTLE);
    }

    public function getAdditionalEffects(): array{
        return $this->getEffects();
    }

    /**
	 * @param int $meta
	 *
	 * @return int
	 */
	public static function getEffectId(int $meta) : int{
		switch($meta){
			case self::INVISIBILITY:
			case self::INVISIBILITY_T:
				return Effect::INVISIBILITY;
			case self::LEAPING:
			case self::LEAPING_T:
			case self::LEAPING_TWO:
				return Effect::JUMP;
			case self::FIRE_RESISTANCE:
			case self::FIRE_RESISTANCE_T:
				return Effect::FIRE_RESISTANCE;
			case self::SWIFTNESS:
			case self::SWIFTNESS_T:
			case self::SWIFTNESS_TWO:
				return Effect::SPEED;
			case self::SLOWNESS:
			case self::SLOWNESS_T:
				return Effect::SLOWNESS;
			case self::WATER_BREATHING:
			case self::WATER_BREATHING_T:
				return Effect::WATER_BREATHING;
			case self::HARMING:
			case self::HARMING_TWO:
				return Effect::HARMING;
			case self::POISON:
			case self::POISON_T:
			case self::POISON_TWO:
				return Effect::POISON;
			case self::HEALING:
			case self::HEALING_TWO:
				return Effect::HEALING;
			case self::NIGHT_VISION:
			case self::NIGHT_VISION_T:
				return Effect::NIGHT_VISION;
			case self::REGENERATION:
			case self::REGENERATION_T:
			case self::REGENERATION_TWO:
				return Effect::REGENERATION;
			default:
				return 0;
		}
	}

	/**
	 * @param int $meta
	 *
	 * @return string
	 */
	public static function getNameByMeta(int $meta) : string{
		switch($meta){
			case self::WATER_BOTTLE:
				return "Water Bottle";
			case self::MUNDANE:
			case self::MUNDANE_EXTENDED:
				return "Mundane Potion";
			case self::THICK:
				return "Thick Potion";
			case self::AWKWARD:
				return "Awkward Potion";
			case self::INVISIBILITY:
			case self::INVISIBILITY_T:
				return "Potion of Invisibility";
			case self::LEAPING:
			case self::LEAPING_T:
				return "Potion of Leaping";
			case self::LEAPING_TWO:
				return "Potion of Leaping II";
			case self::FIRE_RESISTANCE:
			case self::FIRE_RESISTANCE_T:
				return "Potion of Fire Resistance";
			case self::SWIFTNESS:
			case self::SWIFTNESS_T:
				return "Potion of Swiftness";
			case self::SWIFTNESS_TWO:
				return "Potion of Swiftness II";
			case self::SLOWNESS:
			case self::SLOWNESS_T:
				return "Potion of Slowness";
			case self::WATER_BREATHING:
			case self::WATER_BREATHING_T:
				return "Potion of Water Breathing";
			case self::HARMING:
				return "Potion of Harming";
			case self::HARMING_TWO:
				return "Potion of Harming II";
			case self::POISON:
			case self::POISON_T:
				return "Potion of Poison";
			case self::POISON_TWO:
				return "Potion of Poison II";
			case self::HEALING:
				return "Potion of Healing";
			case self::HEALING_TWO:
				return "Potion of Healing II";
			case self::NIGHT_VISION:
			case self::NIGHT_VISION_T:
				return "Potion of Night Vision";
			case self::STRENGTH:
			case self::STRENGTH_T:
				return "Potion of Strength";
			case self::STRENGTH_TWO:
				return "Potion of Strength II";
			case self::REGENERATION:
			case self::REGENERATION_T:
				return "Potion of Regeneration";
			case self::REGENERATION_TWO:
				return "Potion of Regeneration II";
			case self::WEAKNESS:
			case self::WEAKNESS_T:
				return "Potion of Weakness";
			default:
				return "Potion";
		}
	}

    /**
     * Returns a list of effects applied by potions with the specified ID.
     *
     * @param int $id
     * @return Effect[]
     *
     * @throws \InvalidArgumentException if the potion type is unknown
     */
    public static function getPotionEffectsById(int $id) : array{
        switch($id){
            case self::WATER:
            case self::MUNDANE:
            case self::LONG_MUNDANE:
            case self::THICK:
            case self::AWKWARD:
                return [];
            case self::NIGHT_VISION:
                return [
                    Effect::getEffect(Effect::NIGHT_VISION)->setDuration(3600)
                ];
            case self::LONG_NIGHT_VISION:
                return [
                    Effect::getEffect(Effect::NIGHT_VISION)->setDuration(9600)
                ];
            case self::INVISIBILITY:
                return [
                    Effect::getEffect(Effect::INVISIBILITY)->setDuration(3600)
                ];
            case self::LONG_INVISIBILITY:
                return [
                    Effect::getEffect(Effect::INVISIBILITY)->setDuration(9600)
                ];
            case self::LEAPING:
                return [
                    Effect::getEffect(Effect::JUMP_BOOST)->setDuration(3600)
                ];
            case self::LONG_LEAPING:
                return [
                    Effect::getEffect(Effect::JUMP_BOOST)->setDuration(9600)
                ];
            case self::STRONG_LEAPING:
                return [
                    Effect::getEffect(Effect::JUMP_BOOST)->setDuration(1800)->setAmplifier(1)
                ];
            case self::FIRE_RESISTANCE:
                return [
                    Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(3600)
                ];
            case self::LONG_FIRE_RESISTANCE:
                return [
                    Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(9600)
                ];
            case self::SWIFTNESS:
                return [
                    Effect::getEffect(Effect::SPEED)->setDuration(3600)
                ];
            case self::LONG_SWIFTNESS:
                return [
                    Effect::getEffect(Effect::SPEED)->setDuration(9600)
                ];
            case self::STRONG_SWIFTNESS:
                return [
                    Effect::getEffect(Effect::SPEED)->setDuration(1800)->setAmplifier(1)
                ];
            case self::SLOWNESS:
                return [
                    Effect::getEffect(Effect::SLOWNESS)->setDuration(1800)
                ];
            case self::LONG_SLOWNESS:
                return [
                    Effect::getEffect(Effect::SLOWNESS)->setDuration(4800)
                ];
            case self::WATER_BREATHING:
                return [
                    Effect::getEffect(Effect::WATER_BREATHING)->setDuration(3600)
                ];
            case self::LONG_WATER_BREATHING:
                return [
                    Effect::getEffect(Effect::WATER_BREATHING)->setDuration(9600)
                ];
            case self::HEALING:
                return [
                    Effect::getEffect(Effect::HEALING)
                ];
            case self::STRONG_HEALING:
                return [
                    Effect::getEffect(Effect::HEALING)->setAmplifier(1)
                ];
            case self::HARMING:
                return [
                    Effect::getEffect(Effect::HARMING)
                ];
            case self::STRONG_HARMING:
                return [
                    Effect::getEffect(Effect::HARMING)->setAmplifier(1)
                ];
            case self::POISON:
                return [
                    Effect::getEffect(Effect::POISON)->setDuration(900)
                ];
            case self::LONG_POISON:
                return [
                    Effect::getEffect(Effect::POISON)->setDuration(2400)
                ];
            case self::STRONG_POISON:
                return [
                    Effect::getEffect(Effect::POISON)->setDuration(440)->setAmplifier(1)
                ];
            case self::REGENERATION:
                return [
                    Effect::getEffect(Effect::REGENERATION)->setDuration(900)
                ];
            case self::LONG_REGENERATION:
                return [
                    Effect::getEffect(Effect::REGENERATION)->setDuration(2400)
                ];
            case self::STRONG_REGENERATION:
                return [
                    Effect::getEffect(Effect::REGENERATION)->setDuration(440)->setAmplifier(1)
                ];
            case self::STRENGTH:
                return [
                    Effect::getEffect(Effect::STRENGTH)->setDuration(3600)
                ];
            case self::LONG_STRENGTH:
                return [
                    Effect::getEffect(Effect::STRENGTH)->setDuration(9600)
                ];
            case self::STRONG_STRENGTH:
                return [
                    Effect::getEffect(Effect::STRENGTH)->setDuration(1800)->setAmplifier(1)
                ];
            case self::WEAKNESS:
                return [
                    Effect::getEffect(Effect::WEAKNESS)->setDuration(1800)
                ];
            case self::LONG_WEAKNESS:
                return [
                    Effect::getEffect(Effect::WEAKNESS)->setDuration(4800)
                ];
            case self::WITHER:
                return [
                    Effect::getEffect(Effect::WITHER)->setDuration(800)->setAmplifier(1)
                ];
        }
        throw new \InvalidArgumentException("Unknown potion type $id");
    }

}
