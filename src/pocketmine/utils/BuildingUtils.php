<?php

namespace pocketmine\utils;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;

class BuildingUtils{

    const TO_NOT_OVERWRITE = [
        Block::WATER,
        Block::STILL_WATER,
        Block::STILL_LAVA,
        Block::LAVA,
        Block::BEDROCK,
        Block::CACTUS,
        Block::PLANK
    ];

    /**
     * Fills an area
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @return void
     */
    public static function fill(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block = null){
        if ($block == null) $block = BlockFactory::get(Block::AIR);
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($x = $pos1->x; $x >= $pos2->x; $x--) for ($y = $pos1->y; $y >= $pos2->y; $y--) for ($z = $pos1->z; $z >= $pos2->z; $z--) {
            $level->setBlockIdAt($x, $y, $z, $block->getId());
            $level->setBlockDataAt($x, $y, $z, $block->getDamage());
        }
    }


    /**
     * Fills an area randomly
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @param Random $random
     * @param int $randMax
     * @return void
     */
    public static function fillRandom(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block = null, Random $random = null, $randMax = 3){
        if ($block == null) $block = BlockFactory::get(Block::AIR);
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($x = $pos1->x; $x >= $pos2->x; $x--) for ($y = $pos1->y; $y >= $pos2->y; $y--) for ($z = $pos1->z; $z >= $pos2->z; $z--) if ($random !== null ? $random->nextBoundedInt($randMax) == 0 : rand(0, $randMax) == 0) {
            $level->setBlockIdAt($x, $y, $z, $block->getId());
            $level->setBlockDataAt($x, $y, $z, $block->getDamage());
        }
    }

    /**
     * Custom area filling
     *
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param callable $call
     * @param array $params
     * @return array
     */
    public static function fillCallback(Vector3 $pos1, Vector3 $pos2, callable $call, ...$params): array{
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        $return = [];
        for ($x = $pos1->x; $x >= $pos2->x; $x--) for ($y = $pos1->y; $y >= $pos2->y; $y--) for ($z = $pos1->z; $z >= $pos2->z; $z--) {
            $return[] = call_user_func($call, new Vector3($x, $y, $z), ...$params);
        }
        return $return;
    }

    /**
     * Creates walls
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @return void
     */
    public static function walls(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block){
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($y = $pos1->y; $y >= $pos2->y; $y--) {
            for ($x = $pos1->x; $x >= $pos2->x; $x--) {
                $level->setBlockIdAt($x, $y, $pos1->z, $block->getId());
                $level->setBlockDataAt($x, $y, $pos1->z, $block->getDamage());
                $level->setBlockIdAt($x, $y, $pos2->z, $block->getId());
                $level->setBlockDataAt($x, $y, $pos2->z, $block->getDamage());
            }
            for ($z = $pos1->z; $z >= $pos2->z; $z--) {
                $level->setBlockIdAt($pos1->x, $y, $z, $block->getId());
                $level->setBlockDataAt($pos1->x, $y, $z, $block->getDamage());
                $level->setBlockIdAt($pos2->x, $y, $z, $block->getId());
                $level->setBlockDataAt($pos2->x, $y, $z, $block->getDamage());
            }
        }
    }

    /**
     * Creates the top of a structure
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @return void
     */
    public static function top(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block){
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($x = $pos1->x; $x >= $pos2->x; $x--)
            for ($z = $pos1->z; $z >= $pos2->z; $z--) {
                $level->setBlockIdAt($x, $pos1->y, $z, $block->getId());
                $level->setBlockDataAt($x, $pos1->y, $z, $block->getDamage());
            }
    }

    /**
     * Creates the corners of the structures. Used for mineshaft "towers"
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @return void
     */
    public static function corners(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block){
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($y = $pos1->y; $y >= $pos2->y; $y--) {
            $level->setBlockIdAt($pos1->x, $y, $pos1->z, $block->getId());
            $level->setBlockDataAt($pos1->x, $y, $pos1->z, $block->getDamage());
            $level->setBlockIdAt($pos2->x, $y, $pos1->z, $block->getId());
            $level->setBlockDataAt($pos2->x, $y, $pos1->z, $block->getDamage());
            $level->setBlockIdAt($pos1->x, $y, $pos2->z, $block->getId());
            $level->setBlockDataAt($pos1->x, $y, $pos2->z, $block->getDamage());
            $level->setBlockIdAt($pos2->x, $y, $pos2->z, $block->getId());
            $level->setBlockDataAt($pos2->x, $y, $pos2->z, $block->getDamage());
        }
    }

    /**
     * Fills the bottom of a structure
     *
     * @param ChunkManager $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Block $block
     * @return void
     */
    public static function bottom(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block){
        list($pos1, $pos2) = self::minmax($pos1, $pos2);
        for ($x = $pos1->x; $x >= $pos2->x; $x--)
            for ($z = $pos1->z; $z >= $pos2->z; $z--) {
                $level->setBlockIdAt($x, $pos2->y, $z, $block->getId());
                $level->setBlockDataAt($x, $pos2->y, $z, $block->getDamage());
            }
    }

    /**
     * Builds a structure randomly based on a circle algorithm. Used in caves and lakes.
     *
     * @param ChunkManager $level
     * @param Vector3 $pos
     * @param Vector3 $infos
     * @param Random $random
     * @param Block $block
     * @return void
     */
    public static function buildRandom(ChunkManager $level, Vector3 $pos, Vector3 $infos, Random $random, Block $block){
        $xBounded = $random->nextBoundedInt(3) - 1;
        $yBounded = $random->nextBoundedInt(3) - 1;
        $zBounded = $random->nextBoundedInt(3) - 1;
        $pos = $pos->round();
        for ($x = $pos->x - ($infos->x / 2); $x <= $pos->x + ($infos->x / 2); $x++) {
            for ($y = $pos->y - ($infos->y / 2); $y <= $pos->y + ($infos->y / 2); $y++) {
                for ($z = $pos->z - ($infos->z / 2); $z <= $pos->z + ($infos->z / 2); $z++) {
                    // if(abs((abs($x) - abs($pos->x)) ** 2 + ($y - $pos->y) ** 2 + (abs($z) - abs($pos->z)) ** 2) < (abs($infos->x / 2 + $xBounded) + abs($infos->y / 2 + $yBounded) + abs($infos->z / 2 + $zBounded)) ** 2
                    if (abs((abs($x) - abs($pos->x)) ** 2 + ($y - $pos->y) ** 2 + (abs($z) - abs($pos->z)) ** 2) < ((($infos->x / 2 - $xBounded) + ($infos->y / 2 - $yBounded) + ($infos->z / 2 - $zBounded)) / 3) ** 2 && $y > 0 && !in_array($level->getBlockIdAt($x, $y, $z), self::TO_NOT_OVERWRITE) && !in_array($level->getBlockIdAt($x, $y + 1, $z), self::TO_NOT_OVERWRITE)) {
                        $level->setBlockIdAt($x, $y, $z, $block->getId());
                        $level->setBlockDataAt($x, $y, $z, $block->getDamage());
                    }
                }
            }
        }
    }

    /**
     * Returns two Vector three, the biggest and lowest ones based on two provided vectors
     *
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @return array
     */
    protected static function minmax(Vector3 $pos1, Vector3 $pos2): array{
        $v1 = new Vector3(max($pos1->x, $pos2->x), max($pos1->y, $pos2->y), max($pos1->z, $pos2->z));
        $v2 = new Vector3(min($pos1->x, $pos2->x), min($pos1->y, $pos2->y), min($pos1->z, $pos2->z));
        return [
            $v1,
            $v2
        ];
    }
}