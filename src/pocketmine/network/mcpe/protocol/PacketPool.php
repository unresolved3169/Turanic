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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\Network;

/**
 * Please use Network :D
 *
 * @deprecated
 */
class PacketPool {

    public static function registerPacket(DataPacket $packet){
        Network::registerPacket($packet);
    }

    /**
     * @param int $pid
     * @return DataPacket
     */
    public static function getPacketById(int $pid) : DataPacket{
        return Network::getPacketById($pid);
    }
    /**
     * @param string $buffer
     * @return DataPacket
     */
    public static function getPacket(string $buffer) : DataPacket{
        return Network::getPacket($buffer);
    }
}