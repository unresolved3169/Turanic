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

namespace pocketmine\entity\boss;

use pocketmine\entity\Monster;

class EnderDragon extends Monster {

	const NETWORK_ID = self::ENDER_DRAGON;

	public function initEntity(){
		$this->setMaxHealth(200);
		parent::initEntity();
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Ender Dragon";
	}

	public function getXpDropAmount(): int{
        return 12000;
    }

}