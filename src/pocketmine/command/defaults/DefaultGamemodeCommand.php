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

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;


class DefaultGamemodeCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.defaultgamemode.description",
			"%pocketmine.command.defaultgamemode.usage"
		);
		$this->setPermission("pocketmine.command.defaultgamemode");
	}

	public function execute(CommandSender $sender, string $currentAlias, array $args){
		if(!$this->canExecute($sender)){
			return true;
		}

		if(count($args) === 0){
            throw new InvalidCommandSyntaxException();
		}

		$gameMode = Server::getGamemodeFromString($args[0]);

		if($gameMode !== -1){
			$sender->getServer()->setConfigInt("gamemode", $gameMode);
			$sender->sendMessage(new TranslationContainer("commands.defaultgamemode.success", [Server::getGamemodeString($gameMode)]));
		}else{
			$sender->sendMessage("You entered an unknown gamemode");
		}

		return true;
	}
}