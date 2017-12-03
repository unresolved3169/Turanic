<?php

namespace pocketmine\entity\utility;

use pocketmine\entity\Animal;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class IronGolem extends Animal {
	const NETWORK_ID = 20;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 2.8;

	public $drag = 0.2;
	public $gravity = 0.3;
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth(100);
		parent::initEntity();
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Iron Golem";
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
        $pk->entityRuntimeId = $this->getId();
		$pk->type = self::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		//Not affected by Looting.
		$drops = [ItemItem::get(ItemItem::IRON_INGOT, 0, mt_rand(3, 5))];
		$drops[] = ItemItem::get(ItemItem::POPPY, 0, mt_rand(0, 2));
		return $drops;
	}
}