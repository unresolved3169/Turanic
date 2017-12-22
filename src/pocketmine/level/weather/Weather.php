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

namespace pocketmine\level\weather;

use pocketmine\event\level\WeatherChangeEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Server;

class Weather {

	const CLEAR = 0, SUNNY = 0;
	const RAIN = 1, RAINY = 1;
	const RAINY_THUNDER = 2;
	const THUNDER = 3;

    /** @var Level */
    private $level;
    /** @var int */
    private $rainTime = 0;
    /** @var int */
    private $thunderTime = 0;
    /** @var int */
    private $weather = self::SUNNY;
    /** @var bool */
    private $canCalculate = true;
    /** @var  Vector3 */
    private $temporalVector;

    public function __construct(Level $level){
        $this->level = $level;
        $this->temporalVector = new Vector3(0,0,0);
    }

    public function tick(){
        if(!$this->canCalculate()) return;
        $this->rainTime--;
        if ($this->rainTime <= 0) {
            if ($this->setRainy(!$this->isRainy())) {
                if ($this->isRainy()) {
                    $this->rainTime = (mt_rand(0,12000) + 12000);
                } else {
                    $this->rainTime =  (mt_rand(0,168000) + 12000);
                }
            }
        }

        $this->thunderTime--;
        if ($this->thunderTime <= 0) {
            if ($this->setThunder(!$this->isThunder())) {
                if ($this->isThunder()) {
                    $this->thunderTime = (mt_rand(0, 12000) + 3600);
                } else {
                    $this->thunderTime = (mt_rand(0, 168000) + 12000);
                }
            }
        }

        if(($this->isThunder() or $this->isRainyThunder()) and mt_rand(0,10000) < 20){
            $players = $this->level->getPlayers();
            if(count($players) > 0){
                $p = $players[array_rand($players)];
                $x = (int) $p->x + mt_rand(-64, 64);
                $z = (int) $p->z + mt_rand(-64, 64);
                $y = $this->level->getHighestBlockAt($x, $z);
                $this->level->spawnLightning($this->temporalVector->setComponents($x, $y, $z));
            }
        }
    }

    public function setRainy(bool $rain, bool $event = true){
        if($event) {
            $this->level->getServer()->getPluginManager()->callEvent($ev = new WeatherChangeEvent($this->level, self::RAINY, $this->rainTime));

            if ($ev->isCancelled()) {
                return false;
            }
        }

        $this->weather = self::RAINY;

        $pk = new LevelEventPacket();

        if ($rain) {
            $pk->evid = LevelEventPacket::EVENT_START_RAIN;
            $pk->data = mt_rand(0, 50000) + 10000;
            $this->rainTime = (mt_rand(0, 12000) + 12000);
        } else {
            $pk->evid = LevelEventPacket::EVENT_STOP_RAIN;
            $this->rainTime = (mt_rand(0, 168000) + 12000);
        }

        Server::getInstance()->broadcastPacket($this->level->getPlayers(), $pk);

        return true;
    }

    public function isRainy() : bool{
        return $this->getWeather() == self::RAINY;
    }

    public function setThunder(bool $thunder, $rainy = false) : bool{
        $this->level->getServer()->getPluginManager()->callEvent($ev = new WeatherChangeEvent($this->level, $rainy ? self::RAINY_THUNDER : self::THUNDER, $this->thunderTime));

        if ($ev->isCancelled()) {
            return false;
        }

        if ($thunder && $rainy && !$this->isRainy()) {
            $this->setRainy(true, false);
        }

        $this->weather = $ev->getWeather();

        $pk = new LevelEventPacket();

        if($thunder){
            $pk->evid = LevelEventPacket::EVENT_START_THUNDER;
            $pk->data = mt_rand(0, 50000) + 10000;
            $this->thunderTime = (mt_rand(0, 12000) + 3600);
        }else{
            $pk->evid = LevelEventPacket::EVENT_STOP_THUNDER;
            $this->thunderTime = (mt_rand(0, 168000) + 12000);
        }

        Server::getInstance()->broadcastPacket($this->level->getPlayers(), $pk);

        return true;
    }

    public function setWeather(int $weather){
        switch($weather){
            case self::SUNNY:
                $this->setRainy(false);
                return $this->setThunder(false);
            case self::RAINY:
                return $this->setRainy(true);
            case self::RAINY_THUNDER:
                return $this->setThunder(true, true);
            case self::THUNDER:
                return $this->setThunder(true, true);
        }

        return false;
    }

    public function getWeather() : int{
        return $this->weather;
    }

    /**
     * @return bool
     */
    public function isRainyThunder() : bool{
        return $this->getWeather() === self::RAINY_THUNDER;
    }

    /**
     * @return bool
     */
    public function isThunder() : bool{
        return $this->getWeather() === self::THUNDER;
    }

    /**
     * @param $weather
     *
     * @return int
     */
    public static function getWeatherFromString($weather){
        if(is_int($weather)){
            if($weather <= 3){
                return $weather;
            }
            return self::SUNNY;
        }
        switch(strtolower($weather)){
            case "clear":
            case "sunny":
            case "fine":
                return self::SUNNY;
            case "rain":
            case "rainy":
                return self::RAINY;
            case "thunder":
                return self::THUNDER;
            case "rain_thunder":
            case "rainy_thunder":
            case "storm":
                return self::RAINY_THUNDER;
            default:
                return self::SUNNY;
        }
    }

    /**
     * @return bool
     */
    public function canCalculate() : bool{
        return $this->canCalculate;
    }

    /**
     * @param bool $canCalc
     */
    public function setCanCalculate(bool $canCalc){
        $this->canCalculate = $canCalc;
    }
}