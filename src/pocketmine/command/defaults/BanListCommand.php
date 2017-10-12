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

use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;


class BanListCommand extends VanillaCommand {

	/**
	 * BanListCommand constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.banlist.description",
			"%pocketmine.command.banlist.usage"
		);
		$this->setPermission("pocketmine.command.ban.list");
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $currentAlias
	 * @param array         $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		$args[0] = (isset($args[0]) ? strtolower($args[0]) : "");

		switch($args[0]){
			case "ips":
				$list = $sender->getServer()->getIPBans();
				$title = "commands.banlist.ips";
				break;
			case "cids":
				$list = $list = $sender->getServer()->getCIDBans();
				$title = "commands.banlist.cids";
				break;
			case "players":
				$list = $sender->getServer()->getNameBans();
				$title = "commands.banlist.players";
				break;
			default:
				$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
				return false;
		}

		$message = "";
		$list = $list->getEntries();
		foreach($list as $entry){
			$message .= $entry->getName() . ", ";
		}

		$sender->sendMessage(Server::getInstance()->getLanguage()->translateString($title, [count($list)]));
		$sender->sendMessage(\substr($message, 0, -2));

		return true;
	}
}
