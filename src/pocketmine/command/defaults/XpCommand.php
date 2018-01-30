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
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\overload\CommandParameter;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\level\sound\ExpPickupSound;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class XpCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.xp.description",
			"%pocketmine.command.xp.usage"
		);
		$this->setPermission("pocketmine.command.xp");

		$this->getOverload("default")->setParameter(0, new CommandParameter("xp", CommandParameter::TYPE_INT, false));
		$this->getOverload("default")->setParameter(1, new CommandParameter("player", CommandParameter::TYPE_TARGET, false));
	}

	public function execute(CommandSender $sender, string $currentAlias, array $args){
		if(!$this->canExecute($sender)){
			return true;
		}

		if(count($args) < 2){
			if($sender instanceof ConsoleCommandSender){
				$sender->sendMessage("You must specify a target player in the console");
				return true;
			}
			$player = $sender;
		}else{
			$player = $sender->getServer()->getPlayer($args[1]);
		}
		if($player instanceof Player){
			$name = $player->getName();
			if(count($args) < 1){
                throw new InvalidCommandSyntaxException();
			}
			if(strcasecmp(substr($args[0], -1), "L") == 0){
				$level = (int) rtrim($args[0], "Ll");
				if($level > 0){
					$player->addXpLevels((int) $level);
					$sender->sendMessage(new TranslationContainer("%commands.xp.success.levels", [$level, $name]));
					$player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_LEVELUP);
					return true;
				}elseif($level < 0){
					$player->subtractXpLevels((int) -$level);
					$sender->sendMessage(new TranslationContainer("%commands.xp.success.negative.levels", [-$level, $name]));
					return true;
				}
			}else{
				if(($xp = (int) $args[0]) > 0){ //Set Experience
					$player->addXp((int) $args[0]);
					$player->getLevel()->addSound(new ExpPickupSound($player, mt_rand(0, 1000)));
					$sender->sendMessage(new TranslationContainer("%commands.xp.success", [$name, $args[0]]));
					return true;
				}elseif($xp < 0){
					$sender->sendMessage(new TranslationContainer("%commands.xp.failure.withdrawXp"));
					return true;
				}
			}

            throw new InvalidCommandSyntaxException();
		}else{
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			return false;
		}
	}
}