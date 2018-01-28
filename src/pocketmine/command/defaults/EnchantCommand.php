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
use pocketmine\command\overload\CommandParameter;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\TextFormat;

class EnchantCommand extends VanillaCommand {

	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.enchant.description",
			"%pocketmine.command.enchant.usage"
		);
		$this->setPermission("pocketmine.command.enchant");

		$this->getOverload("default")->setParameter(0, new CommandParameter("player", CommandParameter::TYPE_TARGET, false));
        $this->getOverload("default")->setParameter(1, new CommandParameter("id", CommandParameter::TYPE_MIXED, false));
        $this->getOverload("default")->setParameter(2, new CommandParameter("level", CommandParameter::TYPE_INT, false));
	}

	public function execute(CommandSender $sender, string $currentAlias, array $args){
		if(!$this->canExecute($sender)){
			return true;
		}

		if(count($args) < 2){
            throw new InvalidCommandSyntaxException();
		}

		$player = $sender->getServer()->getPlayer($args[0]);

		if($player === null){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			return true;
		}

        $item = $player->getItemInHand();

        if($item->getId() <= 0){
            $sender->sendMessage(new TranslationContainer("commands.enchant.noItem"));
            return true;
        }

        if(is_numeric($args[1])){
            $enchantment = Enchantment::getEnchantment((int) $args[1]);
        }else{
            $enchantment = Enchantment::getEnchantmentByName($args[1]);
        }

        if(!($enchantment instanceof Enchantment)){
            $sender->sendMessage(new TranslationContainer("commands.enchant.notFound", [$args[1]]));
            return true;
        }

        $item->addEnchantment(new EnchantmentInstance($enchantment, (int) ($args[2] ?? 1)));
        $player->getInventory()->setItemInHand($item);


        self::broadcastCommandMessage($sender, new TranslationContainer("%commands.enchant.success", [$player->getName()]));
		return true;
	}
}
