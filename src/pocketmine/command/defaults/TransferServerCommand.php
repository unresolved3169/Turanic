<?php

/*
 *
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
 *
*/

namespace pocketmine\command\defaults;

use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
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
		
		$this->getOverload("default")->setParameter(0, new CommandParameter("player", CommandParameter::TYPE_TARGET, false));
		$this->getOverload("default")->setParameter(1, new CommandParameter("address", CommandParameter::TYPE_STRING, false));
		$this->getOverload("default")->setParameter(2, new CommandParameter("port", CommandParameter::TYPE_INT, true));
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $currentAlias
	 * @param array         $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $currentAlias, array $args){
		$address = null;
		$port = null;
		if($sender instanceof Player){
			if(!$this->testPermission($sender)){
				return true;
			}
			if($sender instanceof ConsoleCommandSender){
				$sender->sendMessage(TextFormat::RED . 'A console can not be transferred!');
				return true;
			}
			if(count($args) < 2 || !is_string(($address = $args[0])) || !is_numeric(($port = $args[1]))){
				$sender->sendMessage("Usage: /transferserver <address> <port>");
				return false;
			}
			$pk = new TransferPacket();
			$pk->address = $address;
			$pk->port = $port;
			$sender->dataPacket($pk);
			return false;
		}
	}
}
