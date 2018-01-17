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

namespace pocketmine\entity;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{AddEntityPacket, UpdateAttributesPacket, BossEventPacket, RemoveEntityPacket, SetEntityDataPacket};
use pocketmine\Player;

/*
 * This a Helper class for simple Bossbar create
 * Note: This not a entity
 */

class BossbarUtils extends Vector3{

    /** @var string */
	protected $title = "Turanic";
	/** @var float */
	protected $healthPercent = 0;
	/** @var float */
	protected $maxHealthPercent = 1;
	/** @var int */
	protected $entityId;
	/** @var array */
	protected $metadata = [];
	
	public function __construct(){
		parent::__construct(0,0,0);
		
		$flags = (
				(1 << Entity::DATA_FLAG_INVISIBLE) |
				(1 << Entity::DATA_FLAG_IMMOBILE)
			);
		$this->metadata = [
		Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
		Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->title]];
		
		$this->entityId = Entity::$entityCount++;
	}
	
	public function setTitle(string $t){
		$this->title = $t;
		$this->setMetadata(Entity::DATA_NAMETAG, Entity::DATA_TYPE_STRING, $t);
	}
	
	public function getTitle() : string{
		return $this->title;
	}
	
	public function setHealthPercent(float $hp, float $maxHp = null){
		if($maxHp !== null)
			$this->maxHealthPercent = $maxHp;
		
		if($hp > $this->maxHealthPercent){
		    $this->maxHealthPercent = $hp;
		}
		
		$this->healthPercent = $hp;
	}
	
	public function getHealthPercent() : float{
		return $this->healthPercent;
	}
	
	public function getMaxHealthPercent() : float{
		return $this->maxHealthPercent;
	}
	
	public function showTo(Player $player){
		$pk = new AddEntityPacket;
		$pk->entityRuntimeId = $this->entityId;
		$pk->type = EntityIds::SHULKER;
		$pk->metadata = $this->metadata;
		$pk->position = $this;
		
		$player->dataPacket($pk);
		$player->dataPacket($this->getHealthPacket());
		
		$pk2 = new BossEventPacket;
		$pk2->bossEid = $this->entityId;
		$pk2->eventType = BossEventPacket::TYPE_SHOW;
		$pk2->title = $this->title;
		$pk2->healthPercent = $this->healthPercent;
		$pk2->overlay = 0;
		$pk2->unknownShort = 0;
		$pk2->color = 0;
		
		$player->dataPacket($pk2);
	}
	
	public function hideFrom(Player $player){
		$pk = new BossEventPacket;
		$pk->bossEid = $this->entityId;
		$pk->eventType = BossEventPacket::TYPE_HIDE;
		
		$player->dataPacket($pk);
		
		$pk2 = new RemoveEntityPacket;
		$pk2->entityUniqueId = $this->entityId;
		
		$player->dataPacket($pk2);
	}
	
	public function updateFor(Player $player){
		$pk = new BossEventPacket;
		$pk->bossEid = $this->entityId;
		$pk->eventType = BossEventPacket::TYPE_TITLE;
		$pk->healthPercent = $this->getHealthPercent();
		$pk->title = $this->getTitle();
		
		$player->dataPacket($pk);
		$pk->eventType = BossEventPacket::TYPE_HEALTH_PERCENT;
		$player->dataPacket($pk);
		$player->dataPacket($this->getHealthPacket());
		$mpk = new SetEntityDataPacket;
		$mpk->entityRuntimeId = $this->entityId;
		$mpk->metadata = $this->metadata;
		
		$player->dataPacket($mpk);
	}
	
	protected function getHealthPacket() : UpdateAttributesPacket{
		$attr = Attribute::getAttribute(Attribute::HEALTH);
		$attr->setMaxValue($this->maxHealthPercent);
		$attr->setValue($this->healthPercent);
		
		$pk = new UpdateAttributesPacket;
		$pk->entityRuntimeId = $this->entityId;
		$pk->entries = [$attr];
		
		return $pk;
	}
	
	public function setMetadata(int $key, int $dtype, $value){
		$this->metadata[$key] = [$dtype, $value];
	}
	
	public function getMetadata(int $key){
		return isset($this->metadata[$key]) ? $this->metadata[$key][1] : null;
	}
}