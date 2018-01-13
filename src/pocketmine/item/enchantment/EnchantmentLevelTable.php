<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\item\enchantment;

use pocketmine\item\Item;
use pocketmine\utils\Range;

class EnchantmentLevelTable {

	private static $map = [];

	public static function init(){
		self::$map = [
			Enchantment::PROTECTION => [
				new Range(1, 21),
				new Range(12, 32),
				new Range(23, 43),
				new Range(34, 54)
			],

			Enchantment::FIRE_PROTECTION => [
				new Range(10, 22),
				new Range(18, 30),
				new Range(26, 38),
				new Range(34, 46)],

			Enchantment::FEATHER_FALLING => [
				new Range(5, 12),
				new Range(11, 21),
				new Range(17, 27),
				new Range(23, 33)
			],

			Enchantment::BLAST_PROTECTION => [
				new Range(5, 17),
				new Range(13, 25),
				new Range(21, 33),
				new Range(29, 41)
			],

			Enchantment::PROJECTILE_PROTECTION => [
				new Range(3, 18),
				new Range(9, 24),
				new Range(15, 30),
				new Range(21, 36)
			],

			Enchantment::RESPIRATION => [
				new Range(10, 40),
				new Range(20, 50),
				new Range(30, 60)
			],

			Enchantment::AQUA_AFFINITY => [
				new Range(10, 41)
			],

			Enchantment::THORNS => [
				new Range(10, 60),
				new Range(30, 80),
				new Range(50, 100)
			],

			//Weapon
			Enchantment::SHARPNESS => [
				new Range(1, 21),
				new Range(12, 32),
				new Range(23, 43),
				new Range(34, 54),
				new Range(45, 65)
			],

			Enchantment::SMITE => [
				new Range(5, 25),
				new Range(13, 33),
				new Range(21, 41),
				new Range(29, 49),
				new Range(37, 57)
			],

			Enchantment::BANE_OF_ARTHROPODS => [
				new Range(5, 25),
				new Range(13, 33),
				new Range(21, 41),
				new Range(29, 49),
				new Range(37, 57)
			],

			Enchantment::KNOCKBACK => [
				new Range(5, 55),
				new Range(25, 75)
			],

			Enchantment::FIRE_ASPECT => [
				new Range(10, 60),
				new Range(30, 80)
			],

			Enchantment::LOOTING => [
				new Range(15, 65),
				new Range(24, 74),
				new Range(33, 83)
			],

			//Bow
			Enchantment::POWER => [
				new Range(1, 16),
				new Range(11, 26),
				new Range(21, 36),
				new Range(31, 46),
				new Range(41, 56)
			],

			Enchantment::PUNCH => [
				new Range(12, 37),
				new Range(32, 57)
			],

			Enchantment::FLAME => [
				new Range(20, 50)
			],

			Enchantment::INFINITY => [
				new Range(20, 50)
			],

			//Mining
			Enchantment::EFFICIENCY => [
				new Range(1, 51),
				new Range(11, 61),
				new Range(21, 71),
				new Range(31, 81),
				new Range(41, 91)
			],

			Enchantment::SILK_TOUCH => [
				new Range(15, 65)
			],

			Enchantment::UNBREAKING => [
				new Range(5, 55),
				new Range(13, 63),
				new Range(21, 71)
			],

			Enchantment::FORTUNE => [
				new Range(15, 55),
				new Range(24, 74),
				new Range(33, 83)
			],

			//Fishing
			Enchantment::LUCK_OF_THE_SEA => [
				new Range(15, 65),
				new Range(24, 74),
				new Range(33, 83)
			],

			Enchantment::LURE => [
				new Range(15, 65),
				new Range(24, 74),
				new Range(33, 83)
			]
		];
	}

	/**
	 * @param Item $item
	 * @param int  $modifiedLevel
	 *
	 * @return Enchantment[]
	 */
	public static function getPossibleEnchantments(Item $item, int $modifiedLevel){
		$result = [];

		$enchantmentIds = [];

		if($item->getId() == Item::BOOK){
			$enchantmentIds = array_keys(self::$map);
		}elseif($item->isArmor()){
			$enchantmentIds[] = Enchantment::PROTECTION;
			$enchantmentIds[] = Enchantment::FIRE_PROTECTION;
			$enchantmentIds[] = Enchantment::BLAST_PROTECTION;
			$enchantmentIds[] = Enchantment::PROJECTILE_PROTECTION;
			$enchantmentIds[] = Enchantment::THORNS;

			if($item->isBoots()){
				$enchantmentIds[] = Enchantment::FEATHER_FALLING;
			}

			if($item->isHelmet()){
				$enchantmentIds[] = Enchantment::RESPIRATION;
				$enchantmentIds[] = Enchantment::AQUA_AFFINITY;
			}

		}elseif($item->isSword()){
			$enchantmentIds[] = Enchantment::SHARPNESS;
			$enchantmentIds[] = Enchantment::SMITE;
			$enchantmentIds[] = Enchantment::BANE_OF_ARTHROPODS;
			$enchantmentIds[] = Enchantment::KNOCKBACK;
			$enchantmentIds[] = Enchantment::FIRE_ASPECT;
			$enchantmentIds[] = Enchantment::LOOTING;

		}elseif($item->isTool()){
			$enchantmentIds[] = Enchantment::EFFICIENCY;
			$enchantmentIds[] = Enchantment::SILK_TOUCH;
			$enchantmentIds[] = Enchantment::FORTUNE;

		}elseif($item->getId() == Item::BOW){
			$enchantmentIds[] = Enchantment::POWER;
			$enchantmentIds[] = Enchantment::PUNCH;
			$enchantmentIds[] = Enchantment::FLAME;
			$enchantmentIds[] = Enchantment::INFINITY;

		}elseif($item->getId() == Item::FISHING_ROD){
			$enchantmentIds[] = Enchantment::LUCK_OF_THE_SEA;
			$enchantmentIds[] = Enchantment::LURE;

		}

		if($item->isTool() || $item->isArmor()){
			$enchantmentIds[] = Enchantment::UNBREAKING;
		}

		foreach($enchantmentIds as $enchantmentId){
			$enchantment = Enchantment::getEnchantment($enchantmentId);
			$ranges = self::$map[$enchantmentId];
			$i = 0;
			/** @var Range $range */
			foreach($ranges as $range){
				$i++;
				if($range->isInRange($modifiedLevel)){
				    $ench = new EnchantmentInstance($enchantment, $i);
					$result[] = $ench;
				}
			}
		}

		return $result;
	}

}
