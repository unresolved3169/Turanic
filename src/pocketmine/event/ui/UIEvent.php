<?php

namespace pocketmine\event\ui;

use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Player;
use pocketmine\event\Event;

abstract class UIEvent extends Event{

	/** @var DataPacket|ModalFormResponsePacket $packet */
	protected $packet;
	/** @var Player */
	protected $player;

	public function __construct(Player $player, DataPacket $packet){
		$this->packet = $packet;
		$this->player = $player;
	}

	public function getPacket() : DataPacket{
		return $this->packet;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getForm(){
		return $this->player->getModalForm($this->packet->formId);
	}
	
	public function getFormId() : int{
		return $this->packet->formId;
	}
	
	public function getFormData(){
		return @json_decode($this->packet->formData);
	}
}