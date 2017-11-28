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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;


class PardonIpCommand extends VanillaCommand {

	/**
	 * PardonIpCommand constructor.
	 *
	 * @param $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.unban.ip.description",
			"%pocketmine.command.unban.ip.usage"
		);
		$this->setPermission("pocketmine.command.unban.ip");
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

		if(count($args) !== 1){
            $sender->sendMessage($sender->getServer()->getLanguage()->translateString("commands.generic.usage", [$this->usageMessage]));
			return false;
		}

		if(preg_match("/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/", $args[0])){
			$sender->getServer()->getIPBans()->remove($args[0]);
			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.unbanip.success", [$args[0]]));
			$sender->getServer()->getNetwork()->unblockAddress($args[0]);
		}else{
			$sender->sendMessage(new TranslationContainer("commands.unbanip.invalid"));
		}

		return true;
	}
}
