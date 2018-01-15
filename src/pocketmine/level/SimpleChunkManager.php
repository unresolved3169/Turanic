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

namespace pocketmine\level;

use pocketmine\block\BlockFactory;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;

class SimpleChunkManager implements ChunkManager {

    /** @var Chunk[] */
    protected $chunks = [];

    protected $seed;
    protected $worldHeight;

    protected $waterHeight = 0;

    /**
     * SimpleChunkManager constructor.
     *
     * @param int $seed
     * @param int $worldHeight
     */
    public function __construct($seed, int $worldHeight = Level::Y_MAX){
        $this->seed = $seed;
        $this->worldHeight = $worldHeight;
    }

    /**
     * Gets the raw block id.
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return int 0-255
     */
    public function getBlockIdAt(int $x, int $y, int $z) : int{
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            return $chunk->getBlockId($x & 0xf, $y, $z & 0xf);
        }
        return 0;
    }

    /**
     * Sets the raw block id.
     *
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $id 0-255
     */
    public function setBlockIdAt(int $x, int $y, int $z, int $id){
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            $chunk->setBlockId($x & 0xf, $y, $z & 0xf, $id);
        }
    }

    /**
     * Gets the raw block metadata
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return int 0-15
     */
    public function getBlockDataAt(int $x, int $y, int $z) : int{
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            return $chunk->getBlockData($x & 0xf, $y, $z & 0xf);
        }
        return 0;
    }

    /**
     * Sets the raw block metadata.
     *
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $data 0-15
     */
    public function setBlockDataAt(int $x, int $y, int $z, int $data){
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            $chunk->setBlockData($x & 0xf, $y, $z & 0xf, $data);
        }
    }

    public function getBlockLightAt(int $x, int $y, int $z) : int{
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            return $chunk->getBlockLight($x & 0xf, $y, $z & 0xf);
        }

        return 0;
    }

    public function setBlockLightAt(int $x, int $y, int $z, int $level){
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            $chunk->setBlockLight($x & 0xf, $y, $z & 0xf, $level);
        }
    }

    public function getBlockSkyLightAt(int $x, int $y, int $z) : int{
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            return $chunk->getBlockSkyLight($x & 0xf, $y, $z & 0xf);
        }

        return 0;
    }

    public function setBlockSkyLightAt(int $x, int $y, int $z, int $level){
        if($chunk = $this->getChunk($x >> 4, $z >> 4)){
            $chunk->setBlockSkyLight($x & 0xf, $y, $z & 0xf, $level);
        }
    }

	public function updateBlockLight(int $x, int $y, int $z){
		$lightPropagationQueue = new \SplQueue();
		$lightRemovalQueue = new \SplQueue();
		$visited = [];
		$removalVisited = [];

		$oldLevel = $this->getBlockLightAt($x, $y, $z);
		$newLevel = (int) BlockFactory::$light[$this->getBlockIdAt($x, $y, $z)];

		if($oldLevel !== $newLevel){
			$this->setBlockLightAt($x, $y, $z, $newLevel);

			if($newLevel < $oldLevel){
				$removalVisited[Level::blockHash($x, $y, $z)] = true;
				$lightRemovalQueue->enqueue([new Vector3($x, $y, $z), $oldLevel]);
			}else{
				$visited[Level::blockHash($x, $y, $z)] = true;
				$lightPropagationQueue->enqueue(new Vector3($x, $y, $z));
			}
		}

		while(!$lightRemovalQueue->isEmpty()){
			/** @var Vector3 $node */
			$val = $lightRemovalQueue->dequeue();
			$node = $val[0];
			$lightLevel = $val[1];

			$this->computeRemoveBlockLight($node->x - 1, $node->y, $node->z, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
			$this->computeRemoveBlockLight($node->x + 1, $node->y, $node->z, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
			$this->computeRemoveBlockLight($node->x, $node->y - 1, $node->z, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
			$this->computeRemoveBlockLight($node->x, $node->y + 1, $node->z, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
			$this->computeRemoveBlockLight($node->x, $node->y, $node->z - 1, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
			$this->computeRemoveBlockLight($node->x, $node->y, $node->z + 1, $lightLevel, $lightRemovalQueue, $lightPropagationQueue, $removalVisited, $visited);
		}

		while(!$lightPropagationQueue->isEmpty()){
			/** @var Vector3 $node */
			$node = $lightPropagationQueue->dequeue();

			$lightLevel = $this->getBlockLightAt($node->x, $node->y, $node->z) - (int) BlockFactory::$lightFilter[$this->getBlockIdAt($node->x, $node->y, $node->z)];

			if($lightLevel >= 1){
				$this->computeSpreadBlockLight($node->x - 1, $node->y, $node->z, $lightLevel, $lightPropagationQueue, $visited);
				$this->computeSpreadBlockLight($node->x + 1, $node->y, $node->z, $lightLevel, $lightPropagationQueue, $visited);
				$this->computeSpreadBlockLight($node->x, $node->y - 1, $node->z, $lightLevel, $lightPropagationQueue, $visited);
				$this->computeSpreadBlockLight($node->x, $node->y + 1, $node->z, $lightLevel, $lightPropagationQueue, $visited);
				$this->computeSpreadBlockLight($node->x, $node->y, $node->z - 1, $lightLevel, $lightPropagationQueue, $visited);
				$this->computeSpreadBlockLight($node->x, $node->y, $node->z + 1, $lightLevel, $lightPropagationQueue, $visited);
			}
		}
	}

	/**
	 * @param           $x
	 * @param           $y
	 * @param           $z
	 * @param           $currentLight
	 * @param \SplQueue $queue
	 * @param \SplQueue $spreadQueue
	 * @param array     $visited
	 * @param array     $spreadVisited
	 */
	private function computeRemoveBlockLight($x, $y, $z, $currentLight, \SplQueue $queue, \SplQueue $spreadQueue, array &$visited, array &$spreadVisited){
		$current = $this->getBlockLightAt($x, $y, $z);

		if($current !== 0 and $current < $currentLight){
			$this->setBlockLightAt($x, $y, $z, 0);

			if(!isset($visited[$index = Level::blockHash($x, $y, $z)])){
				$visited[$index] = true;
				if($current > 1){
					$queue->enqueue([new Vector3($x, $y, $z), $current]);
				}
			}
		}elseif($current >= $currentLight){
			if(!isset($spreadVisited[$index = Level::blockHash($x, $y, $z)])){
				$spreadVisited[$index] = true;
				$spreadQueue->enqueue(new Vector3($x, $y, $z));
			}
		}
	}

	/**
	 * @param           $x
	 * @param           $y
	 * @param           $z
	 * @param           $currentLight
	 * @param \SplQueue $queue
	 * @param array     $visited
	 */
	private function computeSpreadBlockLight($x, $y, $z, $currentLight, \SplQueue $queue, array &$visited){
		$current = $this->getBlockLightAt($x, $y, $z);

		if($current < $currentLight){
			$this->setBlockLightAt($x, $y, $z, $currentLight);

			if(!isset($visited[$index = Level::blockHash($x, $y, $z)])){
				$visited[$index] = true;
				if($currentLight > 1){
					$queue->enqueue(new Vector3($x, $y, $z));
				}
			}
		}
	}

    /**
     * @param int $chunkX
     * @param int $chunkZ
     *
     * @return Chunk|null
     */
    public function getChunk(int $chunkX, int $chunkZ){
        return $this->chunks[Level::chunkHash($chunkX, $chunkZ)] ?? null;
    }

    /**
     * @param int        $chunkX
     * @param int        $chunkZ
     * @param Chunk|null $chunk
     */
    public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk = null){
        if($chunk === null){
            unset($this->chunks[Level::chunkHash($chunkX, $chunkZ)]);
            return;
        }
        $this->chunks[Level::chunkHash($chunkX, $chunkZ)] = $chunk;
    }

    public function cleanChunks(){
        $this->chunks = [];
    }

    /**
     * Gets the level seed
     *
     * @return int
     */
    public function getSeed() : int{
        return $this->seed;
    }

    public function getWorldHeight() : int{
        return $this->worldHeight;
    }

    public function isInWorld(float $x, float $y, float $z) : bool{
        return (
            $x <= INT32_MAX and $x >= INT32_MIN and
            $y < $this->worldHeight and $y >= 0 and
            $z <= INT32_MAX and $z >= INT32_MIN
        );
    }

	/**
	 * @return int
	 */
	public function getWaterHeight() : int{
		return $this->waterHeight;
	}
}