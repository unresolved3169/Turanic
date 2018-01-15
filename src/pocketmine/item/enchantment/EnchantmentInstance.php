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

/**
 * Container for enchantment data applied to items.
 */
class EnchantmentInstance{
    /** @var Enchantment */
    private $enchantment;
    /** @var int */
    private $level;

    /**
     * EnchantmentInstance constructor.
     *
     * @param Enchantment $enchantment Enchantment type
     * @param int         $level Level of enchantment
     */
    public function __construct(Enchantment $enchantment, int $level = 1){
        $this->enchantment = $enchantment;
        $this->level = $level;
    }

    /**
     * Returns the type of this enchantment.
     * @return Enchantment
     */
    public function getType() : Enchantment{
        return $this->enchantment;
    }

    /**
     * Returns the type identifier of this enchantment instance.
     * @return int
     */
    public function getId() : int{
        return $this->enchantment->getId();
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
    public function setLevel(int $level){
        $this->level = $level;

        return $this;
    }

    public function getRepairCost(bool $isBook = false) : int{
        return $this->enchantment->getRepairCost($isBook) * $this->getLevel();
    }
}
