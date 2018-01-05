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

namespace pocketmine\level\utils;

use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\format\EmptySubChunk;
use pocketmine\level\format\SubChunkInterface;

class SubChunkIteratorManager{
    /** @var ChunkManager */
    public $level;

    /** @var Chunk|null */
    public $currentChunk;
    /** @var SubChunkInterface|null */
    public $currentSubChunk;

    /** @var int */
    protected $currentX;
    /** @var int */
    protected $currentY;
    /** @var int */
    protected $currentZ;
    /** @var bool */
    protected $allocateEmptySubs = true;

    public function __construct(ChunkManager $level, bool $allocateEmptySubs = true){
        $this->level = $level;
        $this->allocateEmptySubs = $allocateEmptySubs;
    }

    public function moveTo(int $x, int $y, int $z) : bool{
        if($this->currentChunk === null or $this->currentX !== ($x >> 4) or $this->currentZ !== ($z >> 4)){
            $this->currentX = $x >> 4;
            $this->currentZ = $z >> 4;
            $this->currentSubChunk = null;

            $this->currentChunk = $this->level->getChunk($this->currentX, $this->currentZ);
            if($this->currentChunk === null){
                return false;
            }
        }

        if($this->currentSubChunk === null or $this->currentY !== ($y >> 4)){
            $this->currentY = $y >> 4;

            $this->currentSubChunk = $this->currentChunk->getSubChunk($y >> 4, $this->allocateEmptySubs);
            if($this->currentSubChunk instanceof EmptySubChunk){
                return false;
            }
        }

        return true;
    }

}
