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

namespace pocketmine\item\enchantment;

use pocketmine\item\Item;

class Enchantment {

	const TYPE_INVALID = -1;

	const PROTECTION = 0, TYPE_ARMOR_PROTECTION = 0;
	const FIRE_PROTECTION = 1, TYPE_ARMOR_FIRE_PROTECTION = 1;
	const FEATHER_FALLING = 2, TYPE_ARMOR_FALL_PROTECTION = 2;
	const BLAST_PROTECTION = 3, TYPE_ARMOR_EXPLOSION_PROTECTION = 3;
	const PROJECTILE_PROTECTION = 4, TYPE_ARMOR_PROJECTILE_PROTECTION = 4;
	const THORNS = 5, TYPE_ARMOR_THORNS = 5;
	const RESPIRATION = 6, TYPE_WATER_BREATHING = 6;
	const DEPTH_STRIDER = 7, TYPE_WATER_SPEED = 7;
	const AQUA_AFFINITY = 8, TYPE_WATER_AFFINITY = 8;
	const SHARPNESS = 9, TYPE_WEAPON_SHARPNESS = 9;
	const SMITE = 10, TYPE_WEAPON_SMITE = 10;
	const BANE_OF_ARTHROPODS = 11, TYPE_WEAPON_ARTHROPODS = 11;
	const KNOCKBACK = 12, TYPE_WEAPON_KNOCKBACK = 12;
	const FIRE_ASPECT = 13, TYPE_WEAPON_FIRE_ASPECT = 13;
	const LOOTING = 14, TYPE_WEAPON_LOOTING = 14;
	const EFFIENCY = 15, TYPE_MINING_EFFICIENCY = 15;
	const SILK_TOUCH = 16, TYPE_MINING_SILK_TOUCH = 16;
	const UNBREAKING = 17, TYPE_MINING_DURABILITY = 17;
	const FORTUNE = 18, TYPE_MINING_FORTUNE = 18;
	const POWER = 19, TYPE_BOW_POWER = 19;
	const PUNCH = 20, TYPE_BOW_KNOCKBACK = 20;
	const FLAME = 21, TYPE_BOW_FLAME = 21;
	const INFINITY = 22, TYPE_BOW_INFINITY = 22;
	const LUCK_OF_THE_SEA = 23, TYPE_FISHING_FORTUNE = 23;
	const LURE = 24, TYPE_FISHING_LURE = 24;
	// TODO : ADD ENCHANT
    const FROST_WALKER = 25;
    const MENDING = 26;

	const RARITY_COMMON = 0;
	const RARITY_UNCOMMON = 1;
	const RARITY_RARE = 2;
	const RARITY_MYTHIC = 3;

	const ACTIVATION_EQUIP = 0;
	const ACTIVATION_HELD = 1;
	const ACTIVATION_SELF = 2;

	const SLOT_NONE = 0;
	const SLOT_ALL = 0b11111111111111;
	const SLOT_ARMOR = 0b1111;
	const SLOT_HEAD = 0b1;
	const SLOT_TORSO = 0b10;
	const SLOT_LEGS = 0b100;
	const SLOT_FEET = 0b1000;
	const SLOT_SWORD = 0b10000;
	const SLOT_BOW = 0b100000;
	const SLOT_TOOL = 0b111000000;
	const SLOT_HOE = 0b1000000;
	const SLOT_SHEARS = 0b10000000;
	const SLOT_FLINT_AND_STEEL = 0b10000000;
	const SLOT_DIG = 0b111000000000;
	const SLOT_AXE = 0b1000000000;
	const SLOT_PICKAXE = 0b10000000000;
	const SLOT_SHOVEL = 0b10000000000;
	const SLOT_FISHING_ROD = 0b100000000000;
	const SLOT_CARROT_STICK = 0b1000000000000;


	/** @var Enchantment[] */
	public static $enchantments;

	public static function init(){
		self::$enchantments = new \SplFixedArray(256);

		self::registerEnchantment(new Enchantment(self::PROTECTION, "%enchantment.protect.all", self::RARITY_COMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::FIRE_PROTECTION, "%enchantment.protect.fire", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::FEATHER_FALLING, "%enchantment.protect.fall", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::BLAST_PROTECTION, "%enchantment.protect.explosion", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::PROJECTILE_PROTECTION, "%enchantment.protect.projectile", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::THORNS, "%enchantment.protect.thorns", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::RESPIRATION, "%enchantment.protect.waterbrething", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::DEPTH_STRIDER, "%enchantment.waterspeed", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::AQUA_AFFINITY, "%enchantment.protect.wateraffinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::SHARPNESS, "%enchantment.weapon.sharpness", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::SMITE, "%enchantment.weapon.smite", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::BANE_OF_ARTHROPODS, "%enchantment.weapon.arthropods", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::KNOCKBACK, "%enchantment.weapon.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::FIRE_ASPECT, "%enchantment.weapon.fireaspect", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::LOOTING, "%enchantment.weapon.looting", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::EFFIENCY, "%enchantment.mining.efficiency", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::SILK_TOUCH, "%enchantment.mining.silktouch", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::UNBREAKING, "%enchantment.mining.durability", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::FORTUNE, "%enchantment.mining.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::POWER, "%enchantment.bow.power", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::PUNCH, "%enchantment.bow.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::FLAME, "%enchantment.bow.flame", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::INFINITY, "%enchantment.bow.infinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::LUCK_OF_THE_SEA, "%enchantment.fishing.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD));
		self::registerEnchantment(new Enchantment(self::LURE, "%enchantment.fishing.lure", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD));

	}

	/**
	 * @param int $id
	 *
	 * @return Enchantment
	 */
	public static function getEnchantment(int $id){
        if(isset(self::$enchantments[$id])){
            return clone self::$enchantments[$id];
        }
        return null;
	}


    /**
     * Registers an enchantment type.
     *
     * @param Enchantment $enchantment
     */
    public static function registerEnchantment(Enchantment $enchantment){
        self::$enchantments[$enchantment->getId()] = clone $enchantment;
	}

	/**
	 * @param $name
	 *
	 * @return Enchantment|null
	 */
	public static function getEnchantmentByName(string $name){
        if(defined(Enchantment::class . "::" . strtoupper($name))){
            return self::getEnchantment(constant(Enchantment::class . "::" . strtoupper($name)));
        }
        return null;
	}

	/**
	 * @param Item $item
	 *
	 * @return int
	 */
	public static function getEnchantAbility(Item $item) : int{
		switch($item->getId()){
			case Item::BOOK:
			case Item::BOW:
			case Item::FISHING_ROD:
				return 4;
			// armors
            case Item::CHAIN_HELMET:
            case Item::CHAIN_CHESTPLATE:
            case Item::CHAIN_LEGGINGS:
            case Item::CHAIN_BOOTS:
                return 12;
            case Item::IRON_HELMET:
            case Item::IRON_CHESTPLATE:
            case Item::IRON_LEGGINGS:
            case Item::IRON_BOOTS:
                return 9;
            case Item::DIAMOND_HELMET:
            case Item::DIAMOND_CHESTPLATE:
            case Item::DIAMOND_LEGGINGS:
            case Item::DIAMOND_BOOTS:
                return 10;
            case Item::LEATHER_CAP:
            case Item::LEATHER_TUNIC:
            case Item::LEATHER_PANTS:
            case Item::LEATHER_BOOTS:
                return 15;
            case Item::GOLD_HELMET:
            case Item::GOLD_CHESTPLATE:
            case Item::GOLD_LEGGINGS:
            case Item::GOLD_BOOTS:
                return 25;

            case Item::WOODEN_HOE:
            case Item::WOODEN_AXE:
            case Item::WOODEN_PICKAXE:
            case Item::WOODEN_SWORD:
            case Item::WOODEN_SHOVEL:
                return 15;
            case Item::STONE_HOE:
            case Item::STONE_AXE:
            case Item::STONE_PICKAXE:
            case Item::STONE_SWORD:
            case Item::STONE_SHOVEL:
                return 5;
            case Item::DIAMOND_HOE:
            case Item::DIAMOND_AXE:
            case Item::DIAMOND_PICKAXE:
            case Item::DIAMOND_SWORD:
            case Item::DIAMOND_SHOVEL:
                return 10;
            case Item::IRON_HOE:
            case Item::IRON_AXE:
            case Item::IRON_PICKAXE:
            case Item::IRON_SWORD:
            case Item::IRON_SHOVEL:
                return 14;
            case Item::GOLD_HOE:
            case Item::GOLD_AXE:
            case Item::GOLD_PICKAXE:
            case Item::GOLD_SWORD:
            case Item::GOLD_SHOVEL:
                return 22;
            default:
                return 0;
		}
	}

	/**
	 * @param int $enchantmentId
	 *
	 * @return int
	 */
	public static function getEnchantWeight(int $enchantmentId){
		switch($enchantmentId){
			case self::TYPE_ARMOR_PROTECTION:
				return 10;
			case self::TYPE_ARMOR_FIRE_PROTECTION:
				return 5;
			case self::TYPE_ARMOR_FALL_PROTECTION:
				return 2;
			case self::TYPE_ARMOR_EXPLOSION_PROTECTION:
				return 5;
			case self::TYPE_WATER_BREATHING:
				return 2;
			case self::TYPE_WATER_AFFINITY:
				return 2;
			case self::TYPE_WEAPON_SHARPNESS:
				return 10;
			case self::TYPE_WEAPON_SMITE:
				return 5;
			case self::TYPE_WEAPON_ARTHROPODS:
				return 5;
			case self::TYPE_WEAPON_KNOCKBACK:
				return 5;
			case self::TYPE_WEAPON_FIRE_ASPECT:
				return 2;
			case self::TYPE_WEAPON_LOOTING:
				return 2;
			case self::TYPE_MINING_EFFICIENCY:
				return 10;
			case self::TYPE_MINING_SILK_TOUCH:
				return 1;
			case self::TYPE_MINING_DURABILITY:
				return 5;
			case self::TYPE_MINING_FORTUNE:
				return 2;
			case self::TYPE_BOW_POWER:
				return 10;
			case self::TYPE_BOW_KNOCKBACK:
				return 2;
			case self::TYPE_BOW_FLAME:
				return 2;
			case self::TYPE_BOW_INFINITY:
				return 1;
		}
		return 0;
	}

	/**
	 * @param int $enchantmentId
	 *
	 * @return int
	 */
	public static function getEnchantMaxLevel(int $enchantmentId) : int{
		switch($enchantmentId){
			case self::PROTECTION:
			case self::FIRE_PROTECTION:
			case self::FEATHER_FALLING:
			case self::BLAST_PROTECTION:
			case self::PROJECTILE_PROTECTION:
				return 4;
			case self::THORNS:
				return 3;
			case self::RESPIRATION:
			case self::DEPTH_STRIDER:
				return 3;
			case self::AQUA_AFFINITY:
				return 1;
			case self::SHARPNESS:
			case self::SMITE:
			case self::BANE_OF_ARTHROPODS:
				return 5;
			case self::KNOCKBACK:
			case self::FIRE_ASPECT:
				return 2;
			case self::LOOTING:
				return 3;
			case self::EFFIENCY:
				return 5;
			case self::SILK_TOUCH:
				return 1;
			case self::UNBREAKING:
			case self::FORTUNE:
				return 3;
			case self::POWER:
				return 5;
			case self::PUNCH:
				return 2;
			case self::FLAME:
			case self::INFINITY:
				return 1;
			case self::LUCK_OF_THE_SEA:
			case self::LURE:
				return 3;
		}
		return 999;
	}

	/** @var int */
	private $id;
	/** @var int */
	private $level = 1;
	/** @var string */
	private $name;
	/** @var int */
	private $rarity;
	/** @var int */
	private $activationType;
	/** @var int */
	private $slot;

	/**
	 * Enchantment constructor.
	 *
	 * @param        $id
	 * @param        $name
	 * @param        $rarity
	 * @param        $activationType
	 * @param        $slot
	 */
	public function __construct(int $id, string $name, int $rarity, int $activationType, int $slot){
		$this->id = $id;
		$this->name = $name;
		$this->rarity = $rarity;
		$this->activationType = $activationType;
		$this->slot = $slot;
	}

	/**
     * Returns the ID of this enchantment as per Minecraft BE
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
     * Returns a translation key for this enchantment's name.
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
     * Returns an int constant indicating how rare this enchantment type is.
	 * @return int
	 */
	public function getRarity() : int{
		return $this->rarity;
	}

	/**
     * Returns an int constant describing what type of activation this enchantment requires. For example armor enchantments only apply when worn.
	 * @return int
	 */
	public function getActivationType() : int{
		return $this->activationType;
	}

	/**
     * Returns an int with bitflags set to indicate what item types this enchantment can apply to.
	 * @return int
	 */
	public function getSlot() : int{
		return $this->slot;
	}

	/**
     * Returns whether this enchantment can apply to the specified item type.
	 * @param $slot
	 *
	 * @return bool
	 */
	public function hasSlot($slot) : bool{
		return ($this->slot & $slot) > 0;
	}

	/**
     * Returns the level of the enchantment.
	 * @return int
	 */
	public function getLevel() : int{
		return $this->level;
	}

	/**
     * Sets the level of the enchantment.
	 * @param int $level
	 *
	 * @return $this
	 */
	public function setLevel(int $level) : Enchantment{
		$this->level = $level;

		return $this;
	}

	/**
	 * @param Enchantment $ent
	 *
	 * @return bool
	 */
	public function equals(Enchantment $ent) : bool{
		return $ent->getId() == $this->getId() and $ent->getLevel() == $this->getLevel() and $ent->getActivationType() == $this->getActivationType() and $ent->getRarity() == $this->getRarity();
	}
}
