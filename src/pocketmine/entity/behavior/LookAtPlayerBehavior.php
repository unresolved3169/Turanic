<?php

/*
 *
 * _______  _
 *   |__   __|   (_)
 *   | |_   _ _ __ __ _ _ __  _  ___
 *   | | | | | '__/ _` | '_ \| |/ __|
 *   | | |_| | | | (_| | | | | | (__
 *   |_|\__,_|_|  \__,_|_| |_|_|\___|
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

namespace pocketmine\entity\behavior;

use pocketmine\entity\Mob;

class LookAtPlayerBehavior extends Behavior{

 public $duration = 0;
 public $player;
 public $lookDistance = 0;

 public function getName() : string{
  return "LookAtPlayer";
 }

 public function __construct(Mob $entity, float $lookDistance = 6.0){
  parent::__construct($entity);

  $this->lookDistance = $lookDistance;
 }

 public function shouldStart() : bool{
  $shouldStart = rand(0,50) == 0;
  if(!$shouldStart) return false;

  $players = $this->entity->level->getPlayers();

  foreach($players as $p){
   if($this->entity->distance($p) < $this->lookDistance){
 $this->player = $p;
 break;
   }
  }

  if($this->player == null) return false;

  $this->duration = 40 + rand(0,40);

  return true;
 }

 public function canContinue() : bool{
  return $this->duration-- > 0;
 }

 public function onTick(){
  $dx = $this->player->x - $this->entity->x;
  $dz = $this->player->z - $this->entity->z;

  $tanOutput = 90 - $this->toDegree(atan($dx/$dz));
  $thetaOffset = 270;

  if($dz < 0){
   $thetaOffset = 90;
  }

  $dDiff = sqrt(($dx * $dx) + ($dz * $dz));
  $yaw = $thetaOffset + $tanOutput;
  $dy = ($this->entity->y + $this->entity->getEyeHeight()) - ($this->player->y + $this->player->getEyeHeight());
  $pitch = $this->toDegree(atan($dy/$dDiff));

  $this->entity->yaw = $yaw;
  $this->entity->pitch = $pitch;
 }

 public function onEnd(){
  $this->player = null;
  $this->entity->pitch = 0;
 }

 public function toDegree($angle){
  return $angle * (180 / pi());
 }
}