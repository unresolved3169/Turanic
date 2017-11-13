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
use pocketmine\command\overload\CommandEnum;
use pocketmine\command\overload\CommandParameter;
use pocketmine\event\TranslationContainer;


class StopCommand extends VanillaCommand {

	/**
	 * StopCommand constructor.
	 *
	 * @param $name
	 */
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.stop.description",
			"%pocketmine.command.stop.usage"
		);
		$this->setPermission("pocketmine.command.stop");

		$this->getOverload("default")->setParameter(0, new CommandParameter("args", CommandParameter::TYPE_STRING, true, CommandParameter::FLAG_ENUM, new CommandEnum("args", ["force"])));
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
		$restart = false;
		if(isset($args[0])){
			if($args[0] == 'force'){
				$restart = true;
				array_shift($args);
			}else{
				$restart = false;
			}
		}
		Command::broadcastCommandMessage($sender, new TranslationContainer("commands.stop.start"));
		$msg = implode(" ", $args);
		$sender->getServer()->shutdown($msg);

		return true;
	}
}
