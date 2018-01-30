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

use pocketmine\event\entity\EntityDamageEvent;

class ProtectionEnchantment extends Enchantment{
    /** @var float */
    protected $typeModifier;
    /** @var int[]|null */
    protected $applicableDamageTypes = null;

    /**
     * ProtectionEnchantment constructor.
     *
     * @param int        $id
     * @param string     $name
     * @param int        $rarity
     * @param int        $slot
     * @param int        $maxLevel
     * @param float      $typeModifier
     * @param int[]|null $applicableDamageTypes EntityDamageEvent::CAUSE_* constants which this enchantment type applies to, or null if it applies to all types of damage.
     */
    public function __construct(int $id, string $name, int $rarity, int $slot, int $maxLevel, float $typeModifier, $applicableDamageTypes){
        parent::__construct($id, $name, $rarity, $slot, $maxLevel);

        $this->typeModifier = $typeModifier;
        if($applicableDamageTypes !== null){
            $this->applicableDamageTypes = array_flip($applicableDamageTypes);
        }
    }

    /**
     * Returns the multiplier by which this enchantment type's EPF increases with each enchantment level.
     * @return float
     */
    public function getTypeModifier() : float{
        return $this->typeModifier;
    }

    /**
     * Returns the base EPF this enchantment type offers for the given enchantment level.
     * @param int $level
     *
     * @return int
     */
    public function getProtectionFactor(int $level) : int{
        return (int) floor((6 + $level ** 2) * $this->typeModifier / 3);
    }

    /**
     * Returns whether this enchantment type offers protection from the specified damage source's cause.
     * @param EntityDamageEvent $event
     *
     * @return bool
     */
    public function isApplicable(EntityDamageEvent $event) : bool{
        return $this->applicableDamageTypes === null or isset($this->applicableDamageTypes[$event->getCause()]);
    }
}