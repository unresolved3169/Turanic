<?php

namespace pocketmine\level\generator\normal\object;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\PopulatorObject;
use pocketmine\math\Vector3;
use pocketmine\utils\BuildingUtils;
use pocketmine\utils\Random;

class Temple extends PopulatorObject{

    const DIRECTION_PLUSX = 0;
    const DIRECTION_MINX = 1;
    const DIRECTION_PLUSZ = 2;
    const DIRECTION_MINZ = 3;
    const THREE_DIAGS = [
        [3, 0],
        [0, 3],
        [2, 1],
        [1, 2],
        [-3, 0],
        [-2, 1],
        [-1, 2],
        [0, -3],
        [2, -1],
        [1, -2],
        [-2, -1],
        [-1, -2]
    ];

    private $overridable = [
        Block::AIR => true,
        Block::SAPLING => true,
        Block::LOG => true,
        Block::LEAVES => true,
        Block::STONE => true,
        Block::DANDELION => true,
        Block::POPPY => true,
        Block::SNOW_LAYER => true,
        Block::LOG2 => true,
        Block::LEAVES2 => true,
        Block::CACTUS => true
    ];

    private $directions = [
        [1, 1],
        [1, -1],
        [-1, -1],
        [-1, 1]
    ];

    /** @var ChunkManager */
    private $level;
    private $direction = 0;


    /**
     * @param ChunkManager $level
     * @param $x
     * @param $y
     * @param $z
     * @param Random $random
     * @return bool
     */
    public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random){
        $this->level = $level;
        $this->direction = $random->nextBoundedInt(4);
        for ($xx = $x - 10; $xx <= $x + 10; $xx++)
            for ($yy = $y + 1; $yy <= $y + 11; $yy++)
                for ($zz = $z - 10; $zz <= $z + 10; $zz++)
                    if (!isset($this->overridable[$level->getBlockIdAt($xx, $yy, $zz)]))
                        return false;
        return true;
    }

    /**
     * Builds a temple
     *
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     * @return void
     */
    public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
        echo "temple on $x $y $z" . PHP_EOL;
        // Clearing space...
        BuildingUtils::fill($level, new Vector3($x + 10, $y + 1, $z + 10), new Vector3($x - 10, $y + 2, $z - 10), Block::get(Block::AIR));
        // First, build a pyramid.
        $this->level = $level;
        $firstPos = new Vector3($x + 10, $y, $z + 10);
        $sndPos = new Vector3($x - 10, $y, $z - 10);
        for ($i = 0; $i <= 9; $i++) {
            // Building diagonal sides
            BuildingUtils::walls($level, $firstPos->add(-$i, $i, -$i), $sndPos->add($i, $i, $i), Block::get(Block::SANDSTONE));
        }

        // Floor top
        BuildingUtils::fill($level, new Vector3($x - 5, $y + 4, $z - 5), new Vector3($x + 5, $y + 4, $z + 5), Block::get(Block::SANDSTONE));
        // Creating hole
        BuildingUtils::fill($level, new Vector3($x - 1, $y - 11, $z - 1), new Vector3($x + 1, $y + 4, $z + 1), Block::get(Block::AIR));
        // Hole walls
        BuildingUtils::walls($level, new Vector3($x - 2, $y - 1, $z - 2), new Vector3($x + 2, $y - 8, $z + 2), Block::get(Block::SANDSTONE));

        //Floor bottom
        BuildingUtils::fill($level, new Vector3($x - 9, $y, $z - 9), new Vector3($x + 9, $y, $z + 9), Block::get(Block::SANDSTONE));
        // Floor pattern
        for ($i = -2; $i <= 1; $i++) {//straight
            $xextra = ($i + 1) % 2;
            $zextra = ($i) % 2;
            // Orange hardened clay
            $this->placeBlock($x + ($xextra * 3), $y, $z + ($zextra * 3), Block::STAINED_HARDENED_CLAY, 1);//OUTER out
            $this->placeBlock($x + ($xextra * 2), $y, $z + ($zextra * 2), Block::STAINED_HARDENED_CLAY, 1);//OUTER in
        }
        foreach ($this->directions as $direction) {//Diagonals
            // Building pillar
            for ($yy = $y + 1; $yy <= $y + 3; $yy++)
                $this->placeBlock($x + ($direction[0] * 2), $yy, $z + ($direction[1] * 2), Block::SANDSTONE, 2);
            $this->placeBlock($x + $direction[0], $y, $z + $direction[1], Block::STAINED_HARDENED_CLAY, 1);//Diagonal
        }
        // Blue hardened clay (center)
        $this->placeBlock($x, $y, $z, Block::STAINED_HARDENED_CLAY, 11);
        // Floor pattern end

        // Last step like this
        for ($xx = $x - 2; $xx <= $x + 2; $xx++) {
            $this->placeBlock($xx, $y - 9, $z - 2, Block::SANDSTONE, 2);
            $this->placeBlock($xx, $y - 9, $z + 2, Block::SANDSTONE, 2);
        }
        for ($zz = $z - 2; $zz <= $z + 2; $zz++) {
            $this->placeBlock($x - 2, $y - 9, $zz, Block::SANDSTONE, 2);
            $this->placeBlock($x + 2, $y - 9, $zz, Block::SANDSTONE, 2);
        }

        foreach (self::THREE_DIAGS as $diagPos) {
            $this->placeBlock($x + $diagPos[0], $y - 10, $z + $diagPos[1], Block::SANDSTONE, 1);
            $this->placeBlock($x + $diagPos[0], $y - 11, $z + $diagPos[1], Block::SANDSTONE, 2);
        }

        // Floor + TNT
        for ($xx = $x - 2; $xx <= $x + 2; $xx++)
            for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                $this->placeBlock($xx, $y - 12, $zz, Block::SANDSTONE, 2);
        for ($xx = $x - 1; $xx <= $x + 1; $xx++)
            for ($zz = $z - 1; $zz <= $z + 1; $zz++)
                $this->placeBlock($xx, $y - 13, $zz, Block::TNT);
        $this->placeBlock($x, $y - 11, $z, Block::STONE_PRESSURE_PLATE);

        //TODO TILES
        $this->placeBlock($x, $y - 11, $z + 2, Block::CHEST, 4);
        $this->placeBlock($x, $y - 11, $z - 2, Block::CHEST, 2);
        $this->placeBlock($x + 2, $y - 11, $z, Block::CHEST, 5);
        $this->placeBlock($x - 2, $y - 11, $z, Block::CHEST, 3);
        $this->placeBlock($x, $y - 10, $z + 2, Block::AIR);
        $this->placeBlock($x, $y - 10, $z - 2, Block::AIR);
        $this->placeBlock($x + 2, $y - 10, $z, Block::AIR);
        $this->placeBlock($x - 2, $y - 10, $z, Block::AIR);
        switch ($this->direction) {
            case self::DIRECTION_PLUSX : // x+ (0)
                // Building towers.
                $this->placeTower($x + 8, $y, $z + 8, self::DIRECTION_PLUSX, self::DIRECTION_PLUSZ);
                $this->placeTower($x + 8, $y, $z - 8, self::DIRECTION_PLUSX, self::DIRECTION_MINZ);
                // Creating rectangular parallelepiped of sandstone.
                BuildingUtils::fill($level, new Vector3($x + 6, $y + 1, $z - 6), new Vector3($x + 9, $y + 4, $z + 6), Block::get(Block::SANDSTONE));
                // Creating a path to the entrance
                BuildingUtils::fill($level, new Vector3($x + 6, $y + 1, $z - 1), new Vector3($x + 9, $y + 4, $z + 1), Block::get(Block::AIR));//this clears the entrance
                // Creating path to towers.
                for ($yy = $y + 1; $yy <= $y + 2; $yy++)
                    for ($zz = $z - 6; $zz <= $z + 6; $zz++)
                        $this->placeBlock($x + 8, $yy, $zz, 0);
                // Door additional blocks
                for ($yy = $y + 1; $yy <= $y + 4; $yy++) {
                    $this->placeBlock($x + 6, $yy, $z - 2);
                    $this->placeBlock($x + 6, $yy, $z + 2);
                    // Polished entrance
                    $this->placeBlock($x + 9, $yy, $z + 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x + 9, $yy, $z - 1, Block::SANDSTONE, 2);
                    // Starting entrance structure
                    $this->placeBlock($x + 10, $yy, $z - 2);
                    $this->placeBlock($x + 10, $yy, $z + 2);
                }
                // Finishing entrance structure
                $this->placeBlock($x + 9, $y + 3, $z, Block::SANDSTONE, 2);
                for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                    $this->placeBlock($x + 10, $y + 4, $zz, Block::SANDSTONE, 2);
                $this->placeBlock($x + 10, $y + 5, $z, Block::SANDSTONE, 1);
                $this->placeBlock($x + 10, $y + 5, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 10, $y + 5, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 10, $y + 5, $z - 2, Block::SANDSTONE, 2);
                $this->placeBlock($x + 10, $y + 5, $z + 2, Block::SANDSTONE, 2);
                for ($zz = $z - 1; $zz <= $z + 1; $zz++)
                    $this->placeBlock($x + 10, $y + 6, $zz, Block::SANDSTONE, 2);
                for ($xx = $x + 6; $xx <= $x + 9; $xx++)
                    for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                        $this->placeBlock($xx, $y + 4, $zz);
                break;

            case self::DIRECTION_MINX : // x- (1)
                // Building towers.
                $this->placeTower($x - 8, $y, $z + 8, self::DIRECTION_MINX, self::DIRECTION_PLUSZ);
                $this->placeTower($x - 8, $y, $z - 8, self::DIRECTION_MINX, self::DIRECTION_MINZ);
                // Creating rectangular parallelepiped of sandstone.
                for ($xx = $x - 6; $xx >= $x - 9; $xx--)
                    for ($yy = $y + 1; $yy <= $y + 4; $yy++)
                        for ($zz = $z - 6; $zz <= $z + 6; $zz++)
                            $this->placeBlock($xx, $yy, $zz);
                // Creating a path to the entrance
                for ($xx = $x - 6; $xx >= $x - 9; $xx--)
                    for ($yy = $y + 1; $yy <= $y + 4; $yy++)
                        for ($zz = $z - 1; $zz <= $z + 1; $zz++)
                            $this->placeBlock($xx, $yy, $zz, 0);
                // Creating path to towers.
                for ($yy = $y + 1; $yy <= $y + 2; $yy++)
                    for ($zz = $z - 6; $zz <= $z + 6; $zz++)
                        $this->placeBlock($x - 8, $yy, $zz, 0);
                // Door additional blocks
                for ($yy = $y + 1; $yy <= $y + 4; $yy++) {
                    $this->placeBlock($x - 6, $yy, $z - 2);
                    $this->placeBlock($x - 6, $yy, $z + 2);
                    // Polished entrance
                    $this->placeBlock($x - 9, $yy, $z + 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 9, $yy, $z - 1, Block::SANDSTONE, 2);
                    // Starting entrance structure
                    $this->placeBlock($x - 10, $yy, $z - 2);
                    $this->placeBlock($x - 10, $yy, $z + 2);
                }
                // Finishing entrance structure
                $this->placeBlock($x - 9, $y + 3, $z, Block::SANDSTONE, 2);
                for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                    $this->placeBlock($x - 10, $y + 4, $zz, Block::SANDSTONE, 2);
                $this->placeBlock($x - 10, $y + 5, $z, Block::SANDSTONE, 1);
                $this->placeBlock($x - 10, $y + 5, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 10, $y + 5, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 10, $y + 5, $z - 2, Block::SANDSTONE, 2);
                $this->placeBlock($x - 10, $y + 5, $z + 2, Block::SANDSTONE, 2);
                for ($zz = $z - 1; $zz <= $z + 1; $zz++)
                    $this->placeBlock($x - 10, $y + 6, $zz, Block::SANDSTONE, 2);
                for ($xx = $x - 6; $xx >= $x - 9; $xx--)
                    for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                        $this->placeBlock($xx, $y + 4, $zz);
                break;

            case self::DIRECTION_PLUSZ : // z+ (2)
                // Building towers.
                $this->placeTower($x + 8, $y, $z + 8, self::DIRECTION_PLUSZ, self::DIRECTION_PLUSX);
                $this->placeTower($x - 8, $y, $z + 8, self::DIRECTION_PLUSZ, self::DIRECTION_MINX);
                // Creating rectangular parallelepiped of sandstone.
                BuildingUtils::fill($level, new Vector3($x - 6, $y + 1, $z + 6), new Vector3($x + 6, $y + 4, $z + 9), Block::get(Block::SANDSTONE));
                // Creating a path to the entrance
                for ($xx = $x - 1; $xx <= $x + 1; $xx++)
                    for ($yy = $y + 1; $yy <= $y + 4; $yy++)
                        for ($zz = $z + 6; $zz <= $z + 9; $zz++)
                            $this->placeBlock($xx, $yy, $zz, 0);
                // Creating path to towers.
                BuildingUtils::fill($level, new Vector3($x - 1, $y + 1, $z + 6), new Vector3($x + 1, $y + 4, $z + 9), Block::get(Block::AIR));
                // Door additional blocks
                for ($yy = $y + 1; $yy <= $y + 4; $yy++) {
                    $this->placeBlock($x - 2, $yy, $z + 6);
                    $this->placeBlock($x + 2, $yy, $z + 6);
                    // Polished entrance
                    $this->placeBlock($x + 1, $yy, $z + 9, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 1, $yy, $z + 9, Block::SANDSTONE, 2);
                    // Starting entrance structure
                    $this->placeBlock($x + 2, $yy, $z + 10);
                    $this->placeBlock($x - 2, $yy, $z + 10);
                }
                // Finishing entrance structure
                $this->placeBlock($x, $y + 3, $z + 9, Block::SANDSTONE, 2);
                for ($xx = $x - 2; $xx <= $x + 2; $xx++)
                    $this->placeBlock($xx, $y + 4, $z + 10, Block::SANDSTONE, 2);
                $this->placeBlock($x, $y + 5, $z + 10, Block::SANDSTONE, 1);
                $this->placeBlock($x - 1, $y + 5, $z + 10, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 1, $y + 5, $z + 10, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 2, $y + 5, $z + 10, Block::SANDSTONE, 2);
                $this->placeBlock($x + 2, $y + 5, $z + 10, Block::SANDSTONE, 2);
                for ($xx = $x - 1; $xx <= $x + 1; $xx++)
                    $this->placeBlock($xx, $y + 6, $z + 10, Block::SANDSTONE, 2);
                for ($zz = $z + 6; $zz <= $z + 9; $zz++)
                    for ($xx = $x - 2; $xx <= $x + 2; $xx++)
                        $this->placeBlock($xx, $y + 4, $zz);
                break;

            case self::DIRECTION_MINZ : // z- (3)
                // Building towers.
                $this->placeTower($x + 8, $y, $z - 8, self::DIRECTION_MINZ, self::DIRECTION_PLUSX);
                $this->placeTower($x - 8, $y, $z - 8, self::DIRECTION_MINZ, self::DIRECTION_MINX);
                // Creating rectangular parallelepiped of sandstone.
                BuildingUtils::fill($level, new Vector3($x - 6, $y + 1, $z - 6), new Vector3($x + 6, $y + 4, $z - 9), Block::get(Block::SANDSTONE));
                // Creating a path to the entrance
                BuildingUtils::fill($level, new Vector3($x - 1, $y + 1, $z - 6), new Vector3($x + 1, $y + 4, $z - 9), Block::get(Block::AIR));
                // Creating path to towers.
                for ($yy = $y + 1; $yy <= $y + 2; $yy++)
                    for ($xx = $x - 6; $xx <= $x + 6; $xx++)
                        $this->placeBlock($xx, $yy, $z - 8, 0);
                // Door additional blocks
                for ($yy = $y + 1; $yy <= $y + 4; $yy++) {
                    $this->placeBlock($x - 2, $yy, $z - 6);
                    $this->placeBlock($x + 2, $yy, $z - 6);
                    // Polished entrance
                    $this->placeBlock($x + 1, $yy, $z - 9, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 1, $yy, $z - 9, Block::SANDSTONE, 2);
                    // Starting entrance structure
                    $this->placeBlock($x + 2, $yy, $z - 10);
                    $this->placeBlock($x - 2, $yy, $z - 10);
                }
                // Finishing entrance structure
                $this->placeBlock($x, $y + 3, $z - 9, Block::SANDSTONE, 2);
                for ($xx = $x - 2; $xx <= $x + 2; $xx++)
                    $this->placeBlock($xx, $y + 4, $z - 10, Block::SANDSTONE, 2);
                $this->placeBlock($x, $y + 5, $z - 10, Block::SANDSTONE, 1);
                $this->placeBlock($x - 1, $y + 5, $z - 10, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 1, $y + 5, $z - 10, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 2, $y + 5, $z - 10, Block::SANDSTONE, 2);
                $this->placeBlock($x + 2, $y + 5, $z - 10, Block::SANDSTONE, 2);
                for ($xx = $x - 1; $xx <= $x + 1; $xx++)
                    $this->placeBlock($xx, $y + 6, $z - 10, Block::SANDSTONE, 2);
                for ($zz = $z - 6; $zz >= $z - 9; $zz--)
                    for ($xx = $x - 2; $xx <= $x + 2; $xx++)
                        $this->placeBlock($xx, $y + 4, $zz);
                break;
        }
    }

    /**
     * Places a block
     * @param $x int
     * @param $y int
     * @param $z int
     * @param int $id
     * @param int $meta
     */
    protected function placeBlock($x, $y, $z, $id = Block::SANDSTONE, $meta = 0){
        $this->level->setBlockIdAt($x, $y, $z, $id);
        $this->level->setBlockDataAt($x, $y, $z, $meta);
    }


    /**
     * Places one of the towers. Out is inversed $direction1, stairs come from inversed $direction2 to $direction2, patterns are on $direction1 and $direction2
     * @param $x int
     * @param $y int
     * @param $z int
     * @param $direction1 int
     * @param $direction2 int
     * @return void
     */
    public function placeTower($x, $y, $z, $direction1 = self::DIRECTION_PLUSX, $direction2 = self::DIRECTION_PLUSZ){
        BuildingUtils::walls($this->level, new Vector3($x + 2, $y, $z + 2), new Vector3($x - 2, $y + 8, $z - 2), Block::get(Block::SANDSTONE));
        //Clear insides
        BuildingUtils::fill($this->level, new Vector3($x + 1, $y + 1, $z + 1), new Vector3($x - 1, $y + 7, $z - 1), Block::get(Block::AIR));
        switch ($direction1) {
            case self::DIRECTION_PLUSX : // x+ (0)
                // Stairs
                switch ($direction2) {
                    case self::DIRECTION_PLUSZ :
                        for ($zz = $z + 1; $zz >= $z; $zz--) {
                            $this->placeBlock($x - 1, $y + 1, $zz);
                            $this->placeBlock($x - 1, $y + 2, $zz);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 2);
                        $this->placeBlock($x, $y + 1, $z + 1);
                        $this->placeSlab($x, $y + 2, $z + 1);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x - 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x - 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x, $y + $h, $z + 2, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 1, $y + 7, $z + 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x, $y + 7, $z + 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 1, $y + 7, $z + 2, Block::SANDSTONE, 2);

                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x - 9, $y + 5, $z - 4), new Vector3($x - 7, $y + 7, $z - 5), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x - 8, $y + 5, $z - 4), new Vector3($x - 8, $y + 6, $z - 5), Block::get(Block::AIR));
                        break;
                    case self::DIRECTION_MINZ :
                        for ($zz = $z - 1; $zz <= $z; $zz++) {
                            $this->placeBlock($x - 1, $y + 1, $zz);
                            $this->placeBlock($x - 1, $y + 2, $zz);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 3);
                        $this->placeBlock($x, $y + 1, $z - 1);
                        $this->placeSlab($x, $y + 2, $z - 1);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x - 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x - 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x, $y + $h, $z - 2, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 1, $y + 7, $z - 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x, $y + 7, $z - 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 1, $y + 7, $z - 2, Block::SANDSTONE, 2);
                        break;
                }

                // Building entrance to second floor.
                BuildingUtils::fill($this->level, new Vector3($x - 9, $y + 5, $z + 4), new Vector3($x - 7, $y + 7, $z + 5), Block::get(Block::SANDSTONE, 2));
                BuildingUtils::fill($this->level, new Vector3($x - 8, $y + 5, $z + 4), new Vector3($x - 8, $y + 6, $z + 5), Block::get(Block::AIR));

                // Finishing stairs system
                $this->placeBlock($x - 2, $y + 3, $z, Block::SANDSTONE_STAIRS, 1);
                $this->placeBlock($x - 3, $y + 4, $z, Block::SANDSTONE_STAIRS, 1);
                $this->placeBlock($x - 2, $y + 4, $z, Block::AIR);
                $this->placeBlock($x - 2, $y + 5, $z, Block::AIR);
                $this->placeBlock($x - 2, $y + 6, $z, Block::AIR);
                // Making path from stairs to first floor.
                BuildingUtils::fill($this->level, new Vector3($x - 3, $y, $z + 1 + ($direction2 === self::DIRECTION_PLUSZ ? 2 : 0)), new Vector3($x - 8, $y + 4, $z - 1 + ($direction2 === self::DIRECTION_MINZ ? -2 : 0)), Block::get(Block::SANDSTONE));

                // Other side pattern
                foreach ([
                             1,
                             2,
                             4
                         ] as $h) {
                    $this->placeBlock($x + 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x + 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x + 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                }
                foreach ([
                             3,
                             5
                         ] as $h) {
                    $this->placeBlock($x + 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x + 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x + 2, $y + $h, $z, Block::SANDSTONE, 1);
                }
                $this->placeBlock($x + 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                $this->placeBlock($x + 2, $y + 7, $z, Block::SANDSTONE, 2);
                $this->placeBlock($x + 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                break;

            case self::DIRECTION_MINX : // x- (1)
                // Stairs
                switch ($direction2) {
                    case self::DIRECTION_PLUSZ :
                        for ($zz = $z + 1; $zz >= $z; $zz--) {
                            $this->placeBlock($x + 1, $y + 1, $zz);
                            $this->placeBlock($x + 1, $y + 2, $zz);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 2);
                        $this->placeBlock($x, $y + 1, $z + 1);
                        $this->placeSlab($x, $y + 2, $z + 1);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x + 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x + 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x, $y + $h, $z + 2, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 1, $y + 7, $z + 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x, $y + 7, $z + 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 1, $y + 7, $z + 2, Block::SANDSTONE, 2);

                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x + 9, $y + 5, $z - 4), new Vector3($x + 7, $y + 7, $z - 5), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x + 8, $y + 5, $z - 4), new Vector3($x + 8, $y + 6, $z - 5), Block::get(Block::AIR));
                        break;
                    case self::DIRECTION_MINZ :
                        for ($zz = $z - 1; $zz <= $z; $zz++) {
                            $this->placeBlock($x + 1, $y + 1, $zz);
                            $this->placeBlock($x + 1, $y + 2, $zz);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 3);
                        $this->placeBlock($x, $y + 1, $z - 1);
                        $this->placeSlab($x, $y + 2, $z - 1);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x + 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                            $this->placeBlock($x, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x + 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x, $y + $h, $z - 2, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 1, $y + 6, $z - 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x, $y + 6, $z - 2, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 1, $y + 6, $z - 2, Block::SANDSTONE, 2);

                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x + 9, $y + 5, $z + 4), new Vector3($x + 7, $y + 7, $z + 5), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x + 8, $y + 5, $z + 4), new Vector3($x + 8, $y + 6, $z + 5), Block::get(Block::AIR));
                        break;
                }

                // Finishing stairs system
                $this->placeBlock($x + 2, $y + 3, $z, Block::SANDSTONE_STAIRS, 0);
                $this->placeBlock($x + 3, $y + 4, $z, Block::SANDSTONE_STAIRS, 0);
                $this->placeBlock($x + 2, $y + 4, $z, Block::AIR);
                $this->placeBlock($x + 2, $y + 5, $z, Block::AIR);
                $this->placeBlock($x + 2, $y + 6, $z, Block::AIR);
                // Making path from stairs to first floor.
                BuildingUtils::fill($this->level, new Vector3($x + 3, $y, $z + 1 + ($direction2 === self::DIRECTION_PLUSZ ? 2 : 0)), new Vector3($x + 8, $y + 4, $z - 1 + ($direction2 === self::DIRECTION_MINZ ? -2 : 0)), Block::get(Block::SANDSTONE));

                // Other side pattern
                foreach ([
                             1,
                             2,
                             4
                         ] as $h) {
                    $this->placeBlock($x - 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                }
                foreach ([
                             3,
                             5
                         ] as $h) {
                    $this->placeBlock($x - 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x - 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x - 2, $y + $h, $z, Block::SANDSTONE, 1);
                }
                $this->placeBlock($x - 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                $this->placeBlock($x - 2, $y + 7, $z, Block::SANDSTONE, 2);
                $this->placeBlock($x - 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                break;

            case self::DIRECTION_PLUSZ : // z+ (2)
                // Stairs
                switch ($direction2) {
                    case self::DIRECTION_PLUSX :
                        for ($xx = $x + 1; $xx >= $x; $xx--) {
                            $this->placeBlock($xx, $y + 1, $z - 1);
                            $this->placeBlock($xx, $y + 2, $z - 1);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 0);
                        $this->placeBlock($x + 1, $y + 1, $z);
                        $this->placeSlab($x + 1, $y + 2, $z);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x + 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x + 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 2, $y + $h, $z, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x + 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 2, $y + 7, $z, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x - 4, $y + 5, $z - 9), new Vector3($x - 5, $y + 7, $z - 7), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x - 4, $y + 5, $z - 8), new Vector3($x - 5, $y + 6, $z - 8), Block::get(Block::AIR));
                        break;
                    case self::DIRECTION_MINX :
                        for ($xx = $x - 1; $xx <= $x; $xx++) {
                            $this->placeBlock($xx, $y + 1, $z - 1);
                            $this->placeBlock($xx, $y + 2, $z - 1);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 1);
                        $this->placeBlock($x - 1, $y + 1, $z);
                        $this->placeSlab($x - 1, $y + 2, $z);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x - 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x - 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 2, $y + $h, $z, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                        $this->placeBlock($x - 2, $y + 7, $z, Block::SANDSTONE, 2);
                        $this->placeBlock($x - 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x + 4, $y + 5, $z - 9), new Vector3($x + 5, $y + 7, $z - 7), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x + 4, $y + 5, $z - 8), new Vector3($x + 5, $y + 6, $z - 8), Block::get(Block::AIR));
                        break;
                }

                // Finishing stairs system
                $this->placeBlock($x, $y + 3, $z - 2, Block::SANDSTONE_STAIRS, 3);
                $this->placeBlock($x, $y + 4, $z - 3, Block::SANDSTONE_STAIRS, 3);
                $this->placeBlock($x, $y + 4, $z - 2, Block::AIR);
                $this->placeBlock($x, $y + 5, $z - 2, Block::AIR);
                $this->placeBlock($x, $y + 6, $z - 2, Block::AIR);
                // Making path from stairs to first floor.
                BuildingUtils::fill($this->level, new Vector3($x + 1 + ($direction2 === self::DIRECTION_PLUSX ? 2 : 0), $y, $z - 3), new Vector3($x - 1 + ($direction2 === self::DIRECTION_MINX ? -2 : 0), $y + 4, $z - 8), Block::get(Block::SANDSTONE));

                // Other side pattern
                foreach ([
                             1,
                             2,
                             4
                         ] as $h) {
                    $this->placeBlock($x + 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 1, $y + $h, $z + 2, Block::SANDSTONE, 2);
                    $this->placeBlock($x, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                }
                foreach ([
                             3,
                             5
                         ] as $h) {
                    $this->placeBlock($x + 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x - 1, $y + $h, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x, $y + $h, $z + 2, Block::SANDSTONE, 1);
                }
                $this->placeBlock($x - 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 1, $y + 6, $z + 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 1, $y + 7, $z + 2, Block::SANDSTONE, 2);
                $this->placeBlock($x, $y + 7, $z + 2, Block::SANDSTONE, 2);
                $this->placeBlock($x + 1, $y + 7, $z + 2, Block::SANDSTONE, 2);
                break;

            case self::DIRECTION_MINZ : // z- (3)
                // Stairs
                switch ($direction2) {
                    case self::DIRECTION_PLUSX :
                        for ($xx = $x + 1; $xx >= $x; $xx--) {
                            $this->placeBlock($xx, $y + 1, $z + 1);
                            $this->placeBlock($xx, $y + 2, $z + 1);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 0);
                        $this->placeBlock($x + 1, $y + 1, $z);
                        $this->placeSlab($x + 1, $y + 2, $z);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x + 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x + 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x + 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x + 2, $y + $h, $z, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x + 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x + 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 2, $y + 7, $z, Block::SANDSTONE, 2);
                        $this->placeBlock($x + 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x - 4, $y + 5, $z + 9), new Vector3($x - 5, $y + 7, $z + 7), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x - 4, $y + 5, $z + 8), new Vector3($x - 5, $y + 6, $z + 8), Block::get(Block::AIR));
                        break;
                    case self::DIRECTION_MINX :
                        for ($xx = $x - 1; $xx <= $x; $xx++) {
                            $this->placeBlock($xx, $y + 1, $z + 1);
                            $this->placeBlock($xx, $y + 2, $z + 1);
                        }
                        $this->placeBlock($x, $y + 1, $z, Block::SANDSTONE_STAIRS, 1);
                        $this->placeBlock($x - 1, $y + 1, $z);
                        $this->placeSlab($x - 1, $y + 2, $z);
                        // Pattern
                        foreach ([
                                     1,
                                     2,
                                     4
                                 ] as $h) {
                            $this->placeBlock($x - 2, $y + $h, $z - 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 2, $y + $h, $z + 1, Block::SANDSTONE, 2);
                            $this->placeBlock($x - 2, $y + $h, $z, Block::STAINED_HARDENED_CLAY, 1);
                        }
                        foreach ([
                                     3,
                                     5
                                 ] as $h) {
                            $this->placeBlock($x - 2, $y + $h, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 2, $y + $h, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                            $this->placeBlock($x - 2, $y + $h, $z, Block::SANDSTONE, 1);
                        }
                        $this->placeBlock($x - 2, $y + 6, $z - 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 6, $z, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 6, $z + 1, Block::STAINED_HARDENED_CLAY, 1);
                        $this->placeBlock($x - 2, $y + 7, $z - 1, Block::SANDSTONE, 2);
                        $this->placeBlock($x - 2, $y + 7, $z, Block::SANDSTONE, 2);
                        $this->placeBlock($x - 2, $y + 7, $z + 1, Block::SANDSTONE, 2);
                        // Building entrance to second floor.
                        BuildingUtils::fill($this->level, new Vector3($x + 4, $y + 5, $z + 9), new Vector3($x + 5, $y + 7, $z + 7), Block::get(Block::SANDSTONE, 2));
                        BuildingUtils::fill($this->level, new Vector3($x + 4, $y + 5, $z + 8), new Vector3($x + 5, $y + 6, $z + 8), Block::get(Block::AIR));
                        break;
                }

                // Finishing stairs system
                $this->placeBlock($x, $y + 3, $z + 2, Block::SANDSTONE_STAIRS, 2);
                $this->placeBlock($x, $y + 4, $z + 3, Block::SANDSTONE_STAIRS, 2);
                $this->placeBlock($x, $y + 4, $z + 2, Block::AIR);
                $this->placeBlock($x, $y + 5, $z + 2, Block::AIR);
                $this->placeBlock($x, $y + 6, $z + 2, Block::AIR);
                // Making path from stairs to first floor.
                BuildingUtils::fill($this->level, new Vector3($x + 1 + ($direction2 === self::DIRECTION_PLUSX ? 2 : 0), $y, $z + 3), new Vector3($x - 1 + ($direction2 === self::DIRECTION_MINX ? -2 : 0), $y + 4, $z + 8), Block::get(Block::SANDSTONE));

                // Other side pattern
                foreach ([
                             1,
                             2,
                             4
                         ] as $h) {
                    $this->placeBlock($x + 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                    $this->placeBlock($x - 1, $y + $h, $z - 2, Block::SANDSTONE, 2);
                    $this->placeBlock($x, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                }
                foreach ([
                             3,
                             5
                         ] as $h) {
                    $this->placeBlock($x + 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x - 1, $y + $h, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                    $this->placeBlock($x, $y + $h, $z - 2, Block::SANDSTONE, 1);
                }
                $this->placeBlock($x - 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x + 1, $y + 6, $z - 2, Block::STAINED_HARDENED_CLAY, 1);
                $this->placeBlock($x - 1, $y + 7, $z - 2, Block::SANDSTONE, 2);
                $this->placeBlock($x, $y + 7, $z - 2, Block::SANDSTONE, 2);
                $this->placeBlock($x + 1, $y + 7, $z - 2, Block::SANDSTONE, 2);
                break;
        }

        // Making top
        BuildingUtils::top($this->level, new Vector3($x - 1, $y + 9, $z - 1), new Vector3($x + 1, $y, $z + 1), Block::get(Block::SANDSTONE));
        $this->placeBlock($x - 2, $y + 9, $z, Block::SANDSTONE_STAIRS, 0);
        $this->placeBlock($x + 2, $y + 9, $z, Block::SANDSTONE_STAIRS, 1);
        $this->placeBlock($x, $y + 9, $z - 2, Block::SANDSTONE_STAIRS, 2);
        $this->placeBlock($x, $y + 9, $z + 2, Block::SANDSTONE_STAIRS, 3);
    }


    /**
     * Places a slab
     * @param $x int
     * @param $y int
     * @param $z int
     * @param $id int
     * @param $meta int
     * @param bool $top
     * @return void
     */
    protected function placeSlab($x, $y, $z, $id = 44, $meta = 1, $top = false){
        if ($top) $meta &= 0x08;
        $this->placeBlock($x, $y, $z, $id, $meta);
    }

    /**
     * Inverts a direction
     * @param $direction int
     * @return int
     */
    protected function getInvertedDirection(int $direction): int
    {
        switch ($direction) {
            case self::DIRECTION_PLUSX : // x+ (0)
                return self::DIRECTION_MINX;
                break;
            case self::DIRECTION_MINX : // x- (1)
                return self::DIRECTION_PLUSX;
                break;
            case self::DIRECTION_PLUSZ : // z+ (2)
                return self::DIRECTION_MINZ;
                break;
            case self::DIRECTION_MINZ : // z- (3)
                return self::DIRECTION_PLUSZ;
                break;
            default :
                return -1;
                break;
        }
    }

}