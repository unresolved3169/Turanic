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
use pocketmine\command\overload\CommandParameter;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ClearInventoryCommand extends VanillaCommand {

    /**
     * CaveCommand constructor.
     *
     * @param $name
     */
    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.clearinv.description",
            "%pocketmine.command.clearinv.usage"
        );
        $this->setPermission("pocketmine.command.clearinv");

        $this->getOverload("default")->setParameter(0, new CommandParameter("player", CommandParameter::TYPE_TARGET, true));
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     *
     * @return bool
     */
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$this->canExecute($sender)){
            return true;
        }

        if(count($args) == 0){
            if(!($sender instanceof Player)){
                $sender->sendMessage($sender->getServer()->getLanguage()->translateString("commands.generic.usage", [$this->usageMessage]));
                return true;
            }else{
                $sender->getInventory()->clearAll();
                $sender->sendMessage(new TranslationContainer(TextFormat::GREEN . "%pocketmine.command.clearinv.success"));
                return true;
            }
        }else{
            $player = implode(" ", $args);
            $player = $sender->getServer()->getPlayer($player);
            if($player != null){
                $player->getInventory()->clearAll();
                $player->sendMessage(new TranslationContainer(TextFormat::RED . "%pocketmine.command.clearinv.success"));
                $sender->sendMessage(new TranslationContainer(TextFormat::GREEN . "%pocketmine.command.clearinv.cleared", [$player->getName()]));
            }else{
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
                return true;
            }
        }

        return true;
    }
}