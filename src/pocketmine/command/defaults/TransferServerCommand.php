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

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\overload\CommandParameter;

class TransferServerCommand extends VanillaCommand {

	/**
	 * TransferServerCommand constructor.
	 *
	 * @param $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"Send the player to another server",
			"/transferserver <player> <address> [port]",
			["transferserver", "transfer"]
		);
		$this->setPermission("pocketmine.command.transfer");
		
		$this->getOverload("default")->setParameter(0, new CommandParameter("address", CommandParameter::TYPE_STRING, false));
		$this->getOverload("default")->setParameter(1, new CommandParameter("port", CommandParameter::TYPE_INT, true));
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $currentAlias
	 * @param array         $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $currentAlias, array $args){
		if($sender instanceof Player){
			if(!$this->canExecute($sender)){
				return true;
			}

			if(count($args) <= 0){
				$sender->sendMessage($this->usageMessage);
				return false;
			}

			$address = strtolower($args[0]);

			$sender->transfer($address, (int) ($args[1] ?? 19132));

			return false;
		}

		if(count($args) <= 1){
			$sender->sendMessage($this->usageMessage);
			return false;
		}

		if(!($player = Server::getInstance()->getPlayer($args[0])) instanceof Player){
			$sender->sendMessage("Player specified not found!");
			return false;
		}

		$address = strtolower($args[1]);
		$port = (isset($args[2]) && is_numeric($args[2]) ? $args[2] : 19132);

		$sender->sendMessage("Sending " . $player->getName() . " to " . $address . ":" . $port);

		$player->transfer($address, (int) ($args[2] ?? 19132));
		return true;
	}
}