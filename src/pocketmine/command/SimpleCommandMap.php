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

namespace pocketmine\command;

use pocketmine\command\defaults\BanCidByNameCommand;
use pocketmine\command\defaults\BanCidCommand;
use pocketmine\command\defaults\BanCommand;
use pocketmine\command\defaults\BanIpByNameCommand;
use pocketmine\command\defaults\BanIpCommand;
use pocketmine\command\defaults\BanListCommand;
use pocketmine\command\defaults\BiomeCommand;
use pocketmine\command\defaults\CaveCommand;
use pocketmine\command\defaults\ChunkInfoCommand;
use pocketmine\command\defaults\ClearInventoryCommand;
use pocketmine\command\defaults\DefaultGamemodeCommand;
use pocketmine\command\defaults\DeopCommand;
use pocketmine\command\defaults\DifficultyCommand;
use pocketmine\command\defaults\DumpMemoryCommand;
use pocketmine\command\defaults\EffectCommand;
use pocketmine\command\defaults\EnchantCommand;
use pocketmine\command\defaults\FillCommand;
use pocketmine\command\defaults\GamemodeCommand;
use pocketmine\command\defaults\GarbageCollectorCommand;
use pocketmine\command\defaults\GiveCommand;
use pocketmine\command\defaults\HelpCommand;
use pocketmine\command\defaults\KickCommand;
use pocketmine\command\defaults\KillCommand;
use pocketmine\command\defaults\ListCommand;
use pocketmine\command\defaults\LvdatCommand;
use pocketmine\command\defaults\MeCommand;
use pocketmine\command\defaults\OpCommand;
use pocketmine\command\defaults\PardonCidCommand;
use pocketmine\command\defaults\PardonCommand;
use pocketmine\command\defaults\PardonIpCommand;
use pocketmine\command\defaults\ParticleCommand;
use pocketmine\command\defaults\PingCommand;
use pocketmine\command\defaults\PluginsCommand;
use pocketmine\command\defaults\ReloadCommand;
use pocketmine\command\defaults\SaveCommand;
use pocketmine\command\defaults\SaveOffCommand;
use pocketmine\command\defaults\SaveOnCommand;
use pocketmine\command\defaults\SayCommand;
use pocketmine\command\defaults\SeedCommand;
use pocketmine\command\defaults\SetBlockCommand;
use pocketmine\command\defaults\SetWorldSpawnCommand;
use pocketmine\command\defaults\SpawnpointCommand;
use pocketmine\command\defaults\StatusCommand;
use pocketmine\command\defaults\StopCommand;
use pocketmine\command\defaults\SummonCommand;
use pocketmine\command\defaults\TeleportCommand;

use pocketmine\command\defaults\TransferServerCommand;

use pocketmine\command\defaults\TellCommand;
use pocketmine\command\defaults\TimeCommand;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\defaults\VersionCommand;
use pocketmine\command\defaults\WeatherCommand;
use pocketmine\command\defaults\WhitelistCommand;
use pocketmine\command\defaults\XpCommand;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;

use pocketmine\command\defaults\MakeServerCommand;
use pocketmine\command\defaults\ExtractPluginCommand;
use pocketmine\command\defaults\ExtractPharCommand;
use pocketmine\command\defaults\MakePluginCommand;
use pocketmine\command\defaults\LoadPluginCommand;

class SimpleCommandMap implements CommandMap {

	/**
	 * @var Command[]
	 */
	protected $knownCommands = [];

	/**
	 * @var bool[]
	 */
	protected $commandConfig = [];

	/** @var Server */
	private $server;

	/**
	 * SimpleCommandMap constructor.
	 *
	 * @param Server $server
	 */
	public function __construct(Server $server){
		$this->server = $server;
		/** @var bool[] */
		$this->commandConfig = $this->server->getProperty("commands");
		$this->setDefaultCommands();
	}

	private function setDefaultCommands(){
	    $this->registerAll("pocketmine", [
            new BanCidByNameCommand("bancidbyname"),
            new BanCidCommand("bancid"),
            new BanCommand("ban"),
            new BanIpByNameCommand("banipbyname"),
            new BanIpCommand("ban-ip"),
            new BanListCommand("banlist"),
            new BiomeCommand("biome"),
            new CaveCommand("cave"),
            new ChunkInfoCommand("chunkinfo"),
            new ClearInventoryCommand("clearinv"),
            new DefaultGamemodeCommand("defaultgamemode"),
            new DeopCommand("deop"),
            new DifficultyCommand("difficulty"),
            new EffectCommand("effect"),
            new EnchantCommand("enchant"),
            new ExtractPharCommand("extractphar"),
            new ExtractPluginCommand("ep"),
            new FillCommand("fill"),
            new GamemodeCommand("gamemode"),
            new GiveCommand("give"),
            new HelpCommand("help"),
            new KickCommand("kick"),
            new KillCommand("kill"),
            new ListCommand("list"),
            new LoadPluginCommand("loadplugin"),
            new LvdatCommand("lvdat"),
            new MakePluginCommand("mp"),
            new MakeServerCommand("ms"),
            new MeCommand("me"),
            new OpCommand("op"),
            new PardonCidCommand("pardoncid"),
            new PardonCommand("pardon"),
            new PardonIpCommand("pardon-ip"),
            new ParticleCommand("particle"),
            new PingCommand("ping"),
            new PluginsCommand("plugins"),
            new ReloadCommand("reload"),
            new SaveCommand("save-all"),
            new SaveOffCommand("save-off"),
            new SaveOnCommand("save-on"),
            new SayCommand("say"),
            new SeedCommand("seed"),
            new SetBlockCommand("setblock"),
            new SetWorldSpawnCommand("setworldspawn"),
            new SpawnpointCommand("spawnpoint"),
            new StopCommand("stop"),
            new SummonCommand("summon"),
            new TeleportCommand("tp"),
            new TellCommand("tell"),
            new TimeCommand("time"),
            new TimingsCommand("timings"),
            new TransferServerCommand("transfer"),
            new VersionCommand("version"),
            new WeatherCommand("weather"),
            new WhitelistCommand("whitelist"),
            new XpCommand("xp"),
        ]);

		if($this->server->getProperty("debug.commands", false)){
			$this->registerAll("pocketmine", [
                new DumpMemoryCommand("dumpmemory"),
                new GarbageCollectorCommand("gc"),
                new StatusCommand("status")
            ]);
		}
	}


	/**
	 * @param string $fallbackPrefix
	 * @param array  $commands
	 */
	public function registerAll($fallbackPrefix, array $commands){
		foreach($commands as $command){
			$this->register($fallbackPrefix, $command);
		}
	}

	/**
	 * @param string  $fallbackPrefix
	 * @param Command $command
	 * @param null    $label
	 *
	 * @return bool
	 */
	public function register($fallbackPrefix, Command $command, $label = null){
		if($label === null){
			$label = $command->getName();
		}
		$label = strtolower(trim($label));

		//Check if command was disabled in config and for override
		if(!(($this->commandConfig[$label] ?? $this->commandConfig["default"] ?? true))){
			return false;
		}
		$fallbackPrefix = strtolower(trim($fallbackPrefix));

		$registered = $this->registerAlias($command, false, $fallbackPrefix, $label);

		$aliases = $command->getAliases();
		foreach($aliases as $index => $alias){
			if(!$this->registerAlias($command, true, $fallbackPrefix, $alias)){
				unset($aliases[$index]);
			}
		}
		$command->setAliases($aliases);

		if(!$registered){
			$command->setLabel($fallbackPrefix . ":" . $label);
		}

		$command->register($this);

		return $registered;
	}

    /**
     * @param Command $command
     *
     * @return bool
     */
    public function unregister(Command $command) : bool{
        foreach($this->knownCommands as $lbl => $cmd){
            if($cmd === $command){
                unset($this->knownCommands[$lbl]);
            }
        }
        $command->unregister($this);
        return true;
    }

	/**
	 * @param Command $command
	 * @param         $isAlias
	 * @param         $fallbackPrefix
	 * @param         $label
	 *
	 * @return bool
	 */
	private function registerAlias(Command $command, $isAlias, $fallbackPrefix, $label){
		$this->knownCommands[$fallbackPrefix . ":" . $label] = $command;
		if(($command instanceof VanillaCommand or $isAlias) and isset($this->knownCommands[$label])){
			return false;
		}

		if(isset($this->knownCommands[$label]) and $this->knownCommands[$label]->getLabel() !== null and $this->knownCommands[$label]->getLabel() === $label){
			return false;
		}

		if(!$isAlias){
			$command->setLabel($label);
		}

		$this->knownCommands[$label] = $command;

		return true;
	}

	/**
	 * @param CommandSender $sender
	 * @param Command       $command
	 * @param               $label
	 * @param array         $args
	 * @param int           $offset
	 */
	private function dispatchAdvanced(CommandSender $sender, Command $command, $label, array $args, $offset = 0){
		if(isset($args[$offset])){
			$argsTemp = $args;
			switch($args[$offset]){
				case "@a":
					$p = $this->server->getOnlinePlayers();
					if(count($p) <= 0){
						$sender->sendMessage(TextFormat::RED . "No players online"); //TODO: add language
					}else{
						foreach($p as $player){
							$argsTemp[$offset] = $player->getName();
							$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
						}
					}
					break;
				case "@r":
					$players = $this->server->getOnlinePlayers();
					if(count($players) > 0){
						$argsTemp[$offset] = $players[array_rand($players)]->getName();
						$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
					}
					break;
				case "@p":
					if($sender instanceof Player){
						$argsTemp[$offset] = $sender->getName();
						$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
					}else{
						$sender->sendMessage(TextFormat::RED . "You must be a player!"); //TODO: add language
					}
					break;
				default:
					$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
			}
		}else $command->execute($sender, $label, $args);
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLine
	 *
	 * @return bool
	 */
	public function dispatch(CommandSender $sender, string $commandLine){
        $args = array_map("stripslashes", str_getcsv($commandLine, " "));

		if(count($args) === 0){
			return false;
		}

		$sentCommandLabel = strtolower(array_shift($args));
		$target = $this->getCommand($sentCommandLabel);

		if($target === null){
			return false;
		}

		$target->timings->startTiming();
		try{
			if($this->server->advancedCommandSelector){
				$this->dispatchAdvanced($sender, $target, $sentCommandLabel, $args);
			}else{
				$target->execute($sender, $sentCommandLabel, $args);
			}
		}catch(\Throwable $e){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.exception"));
			$this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.command.exception", [$commandLine, (string) $target, $e->getMessage()]));
			$logger = $sender->getServer()->getLogger();
			if($logger instanceof MainLogger){
				$logger->logException($e);
			}
		}
		$target->timings->stopTiming();

		return true;
	}

	public function clearCommands(){
		foreach($this->knownCommands as $command){
			$command->unregister($this);
		}
		$this->knownCommands = [];
		$this->setDefaultCommands();
	}

	/**
	 * @param string $name
	 *
	 * @return null|Command
	 */
	public function getCommand($name){
		if(isset($this->knownCommands[$name])){
			return $this->knownCommands[$name];
		}

		return null;
	}

	/**
	 * @return Command[]
	 */
	public function getCommands(){
		return $this->knownCommands;
	}


	/**
	 * @return void
	 */
	public function registerServerAliases(){
		$values = $this->server->getCommandAliases();

		foreach($values as $alias => $commandStrings){
			if(strpos($alias, ":") !== false or strpos($alias, " ") !== false){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.illegal", [$alias]));
				continue;
			}

			$targets = [];

			$bad = "";
			foreach($commandStrings as $commandString){
				$args = explode(" ", $commandString);
				$command = $this->getCommand($args[0]);

				if($command === null){
					if(strlen($bad) > 0){
						$bad .= ", ";
					}
					$bad .= $commandString;
				}else{
					$targets[] = $commandString;
				}
			}

			if(strlen($bad) > 0){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.notFound", [$alias, $bad]));
				continue;
			}

			//These registered commands have absolute priority
			if(count($targets) > 0){
				$this->knownCommands[strtolower($alias)] = new FormattedCommandAlias(strtolower($alias), $targets);
			}else{
				unset($this->knownCommands[strtolower($alias)]);
			}

		}
	}
	
	public function getAvailableCommands(CommandSender $sender) : array{
		$available = [];
		foreach($this->knownCommands as $i => $cmd){
			if($cmd->scanPermission($sender) and $cmd instanceof Command){
				$available[$cmd->getName()] = $cmd;
			}
		}
		
		return $available;
	}
}