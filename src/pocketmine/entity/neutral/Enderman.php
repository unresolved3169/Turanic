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

namespace pocketmine\entity\neutral;

use pocketmine\entity\Monster;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Enderman extends Monster {
	const NETWORK_ID = self::ENDERMAN;

	public $width = 0.3;
	public $height = 0;
	public $drag = 0.2;
	public $gravity = 0.3;
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		parent::initEntity();
	}
	/**
	 * @return string
	 */
	public function getName() : string{
		return "Enderman";
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}