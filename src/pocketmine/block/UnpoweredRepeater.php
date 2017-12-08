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

namespace pocketmine\block;

class UnpoweredRepeater extends PoweredRepeater {
	protected $id = self::UNPOWERED_REPEATER_BLOCK;

	public function __construct($meta = 0){
        parent::__construct($meta);
        $this->isPowered = false;
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Unpowered Repeater";
	}

	protected function getUnpowered(): Block{
        return $this;
    }

    protected function getPowered(): Block{
        return new PoweredRepeater($this->meta);
    }

    /**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return false;
	}

	public function getLightLevel(){
        return 0;
    }
}