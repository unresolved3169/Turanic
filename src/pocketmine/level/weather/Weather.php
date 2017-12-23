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

use pocketmine\block\Block;
use pocketmine\event\level\WeatherChangeEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

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
            if ($this->setWeather($this->isRainy() ? self::SUNNY : self::RAINY)) {
                if ($this->isRainy()) {
                    $this->rainTime = (mt_rand(0,12000) + 12000);
                } else {
                    $this->rainTime =  (mt_rand(0,168000) + 12000);
                }
            }
        }

        $this->thunderTime--;
        if ($this->thunderTime <= 0) {
            if ($this->setWeather($this->isThunder() ? self::SUNNY : self::RAINY_THUNDER)) {
                if ($this->isThunder()) {
                    $this->thunderTime = (mt_rand(0, 12000) + 3600);
                } else {
                    $this->thunderTime = (mt_rand(0, 168000) + 12000);
                }
            }
        }

        if($this->isRainyThunder() or $this->isRainy()){
            foreach ($this->level->getChunks() as $chunk){
                if(mt_rand(0,10000) != 0)
                    return;

                $lcg = (new Random())->nextInt() * 3 + 1013904223;
                $lcg = $lcg >> 2;

                $chunkX = $chunk->getX() * 16;
                $chunkZ = $chunk->getZ() * 16;
                $vector = $this->level->adjustPosToNearbyEntity(new Vector3($chunkX + ($lcg & 15), 0, $chunkZ + ($lcg >> 8 & 15)));

                $bId = $this->level->getBlockIdAt($vector->getFloorX(), $vector->getFloorY(), $vector->getFloorZ());
                if ($bId != Block::TALL_GRASS && $bId != Block::WATER)
                    $vector->y += 1;

                $this->level->spawnLightning($vector);
                $this->level->broadcastLevelSoundEvent($vector, LevelSoundEventPacket::SOUND_THUNDER, 93);
                $this->level->broadcastLevelSoundEvent($vector, LevelSoundEventPacket::SOUND_EXPLODE, 93);
            }
        }
    }

    public function isRainy() : bool{
        return $this->getWeather() == self::RAINY;
    }

    public function setWeather(int $weather){
        $this->level->getServer()->getPluginManager()->callEvent($ev = new WeatherChangeEvent($this->level, $weather));

        if ($ev->isCancelled()) {
            return false;
        }
        $weather = $ev->getWeather();

        if($weather >= 0 && $weather <= 3){
            $this->weather = $weather;
            switch ($weather){
                case self::SUNNY:
                    $this->rainTime = (mt_rand(0, 168000) + 12000);
                    $this->thunderTime = (mt_rand(0, 168000) + 12000);
                    break;
                case self::RAINY:
                    $this->rainTime = (mt_rand(0, 12000) + 12000);
                    $this->thunderTime = (mt_rand(0, 168000) + 12000);
                    break;
                case self::RAINY_THUNDER:
                    $this->rainTime = (mt_rand(0, 12000) + 12000);
                    $this->thunderTime = (mt_rand(0, 12000) + 3600);
                    break;
                case self::THUNDER:
                    $this->rainTime = (mt_rand(0, 168000) + 12000);
                    $this->thunderTime = (mt_rand(0, 12000) + 12000);
                    break;
            }

            $this->sendWeatherToAll();
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

    public static function isWeather(int $weather){
        return $weather >= 0 && $weather <= 3;
    }

    /**
     * @param $weather
     *
     * @return int
     */
    public static function getWeatherFromString(string $weather) : int{
        switch(strtolower($weather)){
            case "clear":
            case "sunny":
            case "fine":
                return self::SUNNY;
            case "rain":
            case "rainy":
                return self::RAINY;
            case "thunder":
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

    public function sendWeather(Player $player){
        $pk = new LevelEventPacket();
        $pk->evid = LevelEventPacket::EVENT_STOP_THUNDER;
        $pk2 = new LevelEventPacket();
        $pk2->data = mt_rand(0, 50000) + 10000;
        switch($this->weather){
            case self::SUNNY:
                $pk2->evid = LevelEventPacket::EVENT_STOP_RAIN;
                break;
            case self::RAINY:
                $pk2->evid = LevelEventPacket::EVENT_START_RAIN;
                break;
            case self::RAINY_THUNDER:
            case self::THUNDER:
                $pk2->evid = LevelEventPacket::EVENT_START_RAIN;
                break;
        }

        $player->dataPacket($pk);
        $player->dataPacket($pk2);
    }

    public function sendWeatherToAll(){
        foreach($this->level->getPlayers() as $player){
            $this->sendWeather($player);
        }
    }
}