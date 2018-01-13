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

namespace pocketmine\math;

/**
 * Class representing a ray trace collision with an AxisAlignedBB
 */
class RayTraceResult{

    /**
     * @var AxisAlignedBB
     */
    public $bb;
    /**
     * @var int
     */
    public $hitFace;
    /**
     * @var Vector3
     */
    public $hitVector;

    /**
     * @param AxisAlignedBB $bb
     * @param int           $hitFace one of the Vector3::SIDE_* constants
     * @param Vector3       $hitVector
     */
    public function __construct(AxisAlignedBB $bb, int $hitFace, Vector3 $hitVector){
        $this->bb = $bb;
        $this->hitFace = $hitFace;
        $this->hitVector = $hitVector;
    }

    /**
     * @return AxisAlignedBB
     */
    public function getBoundingBox() : AxisAlignedBB{
        return $this->bb;
    }

    /**
     * @return int
     */
    public function getHitFace() : int{
        return $this->hitFace;
    }

    /**
     * @return Vector3
     */
    public function getHitVector() : Vector3{
        return $this->hitVector;
    }
}