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

namespace pocketmine\entity\passive;

use pocketmine\block\Wool;
use pocketmine\entity\Animal;
use pocketmine\entity\Colorable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Sheep extends Animal implements Colorable {

    const NETWORK_ID = self::SHEEP;

    const DATA_COLOR_INFO = 16;

    public $width = 0.0;
    public $height = 0;
    
    public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		
		parent::initEntity();
	}

    /**
     * @return string
     */
    public function getName(): string{
        return "Sheep";
    }

    /**
     * Sheep constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        if (!$nbt->hasTag("Color")) {
            $nbt->setByte("Color", self::getRandomColor());
        }
        parent::__construct($level, $nbt);

        $this->propertyManager->setByte(self::DATA_COLOR_INFO, $this->getColor());
    }

    /**
     * @return int
     */
    public static function getRandomColor(): int{
        $rand = "";
        $rand .= str_repeat(Wool::WHITE . " ", 20);
        $rand .= str_repeat(Wool::ORANGE . " ", 5);
        $rand .= str_repeat(Wool::MAGENTA . " ", 5);
        $rand .= str_repeat(Wool::LIGHT_BLUE . " ", 5);
        $rand .= str_repeat(Wool::YELLOW . " ", 5);
        $rand .= str_repeat(Wool::GRAY . " ", 10);
        $rand .= str_repeat(Wool::LIGHT_GRAY . " ", 10);
        $rand .= str_repeat(Wool::CYAN . " ", 5);
        $rand .= str_repeat(Wool::PURPLE . " ", 5);
        $rand .= str_repeat(Wool::BLUE . " ", 5);
        $rand .= str_repeat(Wool::BROWN . " ", 5);
        $rand .= str_repeat(Wool::GREEN . " ", 5);
        $rand .= str_repeat(Wool::RED . " ", 5);
        $rand .= str_repeat(Wool::BLACK . " ", 10);
        $arr = explode(" ", $rand);
        return intval($arr[mt_rand(0, count($arr) - 1)]);
    }

    /**
     * @return int
     */
    public function getColor(): int{
        return $this->namedtag->getByte("Color");
    }

    /**
     * @param int $color
     */
    public function setColor(int $color){
        $this->namedtag->setByte("Color", $color);
    }

    /**
     * @return array
     */
	public function getDrops(){
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING);
				$drops = [ItemItem::get(ItemItem::WOOL, $this->getColor(), 1)];
                $drops[] = ItemItem::get(ItemItem::RAW_MUTTON, 0, mt_rand(1, 2 + $lootingL));
				return $drops;
			}
		}
		return [];
	}

    public function getXpDropAmount(): int{
        return !$this->isBaby() ? mt_rand(1,3) : 0;
    }
}