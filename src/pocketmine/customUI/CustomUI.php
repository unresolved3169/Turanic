<?php

namespace pocketmine\customUI;

use pocketmine\Player;

interface CustomUI{

	public function handle($response, Player $player);

	public function jsonSerialize();

	/**
	 * To handle manual closing
	 * @param Player $player
	 */
	public function close(Player $player);

	public function getTitle();

	public function getContent(): array;
}