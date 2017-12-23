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

namespace pocketmine\event\level;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\level\weather\Weather;

class WeatherChangeEvent extends LevelEvent implements Cancellable {
	public static $handlerList = null;

	/** @var int */
	private $weather;

    /**
     * WeatherChangeEvent constructor.
     *
     * @param Level $level
     * @param int $weather
     */
	public function __construct(Level $level, int $weather){
		parent::__construct($level);
		$this->weather = $weather;
	}

	/**
	 * @return int
	 */
	public function getWeather() : int{
		return $this->weather;
	}

	/**
	 * @param int $weather
	 */
	public function setWeather(int $weather = Weather::SUNNY){
		$this->weather = $weather;
	}

}