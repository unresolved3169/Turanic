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
use pocketmine\command\overload\CommandParameter;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;


class BanIpCommand extends VanillaCommand {

	/**
	 * BanIpCommand constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.banip.description",
			"%pocketmine.command.banip.usage"
		);
		$this->setPermission("pocketmine.command.ban.ip");

        $this->getOverload("default")->setParameter(0, new CommandParameter("player", CommandParameter::TYPE_TARGET, false));
        $this->getOverload("default")->setParameter(0, new CommandParameter("reason", CommandParameter::TYPE_STRING, true));
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

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		$value = array_shift($args);
		$reason = implode(" ", $args);

		if(preg_match("/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/", $value)){
			$this->processIPBan($value, $sender, $reason);

			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.banip.success", [$value]));
		}else{
			if(($player = $sender->getServer()->getPlayer($value)) instanceof Player){
				$this->processIPBan($player->getAddress(), $sender, $reason);

				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.banip.success.players", [$player->getAddress(), $player->getName()]));
			}else{
				$sender->sendMessage(new TranslationContainer("commands.banip.invalid"));

				return false;
			}
		}

		return true;
	}

	/**
	 * @param               $ip
	 * @param CommandSender $sender
	 * @param               $reason
	 */
	private function processIPBan($ip, CommandSender $sender, $reason){
		$sender->getServer()->getIPBans()->addBan($ip, $reason, null, $sender->getName());

		foreach($sender->getServer()->getOnlinePlayers() as $player){
			if($player->getAddress() === $ip){
				$player->kick($reason !== "" ? $reason : "IP banned.");
			}
		}

		$sender->getServer()->getNetwork()->blockAddress($ip, -1);
	}
}
