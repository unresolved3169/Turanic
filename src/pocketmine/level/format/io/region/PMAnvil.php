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

namespace pocketmine\level\format\io\region;

use pocketmine\level\format\SubChunk;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;

/**
 * This format is exactly the same as the PC Anvil format, with the only difference being that the stored data order
 * is XZY instead of YZX for more performance loading and saving worlds.
 */
class PMAnvil extends Anvil{

    const REGION_FILE_EXTENSION = "mcapm";

    protected function serializeSubChunk(SubChunk $subChunk) : CompoundTag{
        return new CompoundTag("", [
            new ByteArrayTag("Blocks",     $subChunk->getBlockIdArray()),
            new ByteArrayTag("Data",       $subChunk->getBlockDataArray()),
            new ByteArrayTag("SkyLight",   $subChunk->getBlockSkyLightArray()),
            new ByteArrayTag("BlockLight", $subChunk->getBlockLightArray())
        ]);
    }

    protected function deserializeSubChunk(CompoundTag $subChunk) : SubChunk{
        return new SubChunk(
            $subChunk->getByteArray("Blocks"),
            $subChunk->getByteArray("Data"),
            $subChunk->getByteArray("SkyLight"),
            $subChunk->getByteArray("BlockLight")
        );
    }

    public static function getProviderName() : string{
        return "pmanvil";
    }

    public static function getPcWorldFormatVersion() : int{
        return -1; //Not a PC format, only PocketMine-MP
    }
}