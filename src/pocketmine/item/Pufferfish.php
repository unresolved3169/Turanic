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

class Pufferfish extends Food{

    /**
     * RawSalmon constructor.
     *
     * @param int $meta
     */
    public function __construct(int $meta = 0){
        parent::__construct(self::PUFFERFISH, $meta, "Pufferfish");
    }

    /**
     * @return int
     */
    public function getFoodRestore(): int{
        return 1;
    }

    /**
     * @return float
     */
    public function getSaturationRestore(): float{
        return 0.2;
    }

    public function getAdditionalEffects(): array{
        return [
            Effect::getEffect(Effect::HUNGER)->setDuration(300)->setAmplifier(2),
            Effect::getEffect(Effect::NAUSEA)->setDuration(300)->setAmplifier(1),
            Effect::getEffect(Effect::POISON)->setDuration(1200)->setAmplifier(3)
        ];
    }
}