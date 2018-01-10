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

namespace pocketmine\block;

/**
 * Types of tools that can be used to break blocks
 * Blocks may allow multiple tool types by combining these bitflags
 */
interface BlockToolType{

    const TYPE_NONE = 0;
    const TYPE_SWORD = 1 << 0;
    const TYPE_SHOVEL = 1 << 1;
    const TYPE_PICKAXE = 1 << 2;
    const TYPE_AXE = 1 << 3;
    const TYPE_SHEARS = 1 << 4;

}
