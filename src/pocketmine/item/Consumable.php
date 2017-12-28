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

use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\Living;

/**
 * Interface implemented by objects that can be consumed by mobs.
 */
interface Consumable{

    /**
     * Returns the leftover that this Consumable produces when it is consumed. For Items, this is usually air, but could
     * be an Item to add to a Player's inventory afterwards (such as a bowl).
     *
     * @return Item|Block|mixed
     */
    public function getResidue();

    /**
     * @return Effect[]
     */
    public function getAdditionalEffects() : array;

    /**
     * Called when this Consumable is consumed by mob, after standard resulting effects have been applied.
     *
     * @param Living $consumer
     */
    public function onConsume(Living $consumer);
}