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

declare(strict_types=1);

namespace pocketmine\resourcepacks;

use pocketmine\Server;
use pocketmine\utils\Config;

class ResourcePackManager {

	/** @var Server */
	private $server;

	/** @var string */
	private $path;

	/** @var bool */
	private $serverForceResources = false;

	/** @var ResourcePack[] */
	private $resourcePacks = [];

	/** @var ResourcePack[] */
	private $uuidList = [];

	/**
	 * ResourcePackManager constructor.
	 *
	 * @param Server $server
	 * @param string $path
	 */
	public function __construct(Server $server, string $path){
		$this->server = $server;
		$this->path = $path;
		$logger = $this->server->getLogger();

		if(!file_exists($this->path)){
            $logger->debug($this->server->getLanguage()->translateString("pocketmine.resourcepacks.createFolder", [$path]));
			mkdir($this->path);
		}elseif(!is_dir($this->path)){
			throw new \InvalidArgumentException($this->server->getLanguage()->translateString("pocketmine.resourcepacks.notFolder", [$path]));
		}

		if(!file_exists($this->path . "resource_packs.yml")){
			$lang = $this->server->getProperty("settings.language");
			if(file_exists($this->server->getFilePath() . "src/pocketmine/resources/resource_packs_$lang.yml")){
				$content = file_get_contents($file = $this->server->getFilePath() . "src/pocketmine/resources/resource_packs_$lang.yml");
			}else{
				$content = file_get_contents($file = $this->server->getFilePath() . "src/pocketmine/resources/resource_packs_eng.yml");
			}
			file_put_contents($this->path . "resource_packs.yml", $content);
		}

		$resourcePacksConfig = new Config($this->path . "resource_packs.yml", Config::YAML, []);

		$this->serverForceResources = (bool) $resourcePacksConfig->get("force_resources", false);

        $logger->info($this->server->getLanguage()->translateString("pocketmine.resourcepacks.load"));

		foreach($resourcePacksConfig->get("resource_stack", []) as $pos => $pack){
			try{
				$packPath = $this->path . DIRECTORY_SEPARATOR . $pack;
				if(file_exists($packPath)){
					$newPack = null;
					//Detect the type of resource pack.
					if(is_dir($packPath)){
                        $logger->warning($this->server->getLanguage()->translateString("pocketmine.resourcepacks.folderNotSupported", [$path]));
					}else{
						$info = new \SplFileInfo($packPath);
						switch($info->getExtension()){
							case "zip":
								$newPack = new ZippedResourcePack($packPath);
								break;
							case "mcpack":
								$newPack = new ZippedResourcePack($packPath);
								break;
							default:
                                $logger->warning($this->server->getLanguage()->translateString("pocketmine.resourcepacks.unsupportedType", [$path]));
								break;
						}
					}

					if($newPack instanceof ResourcePack){
						$this->resourcePacks[] = $newPack;
						$this->uuidList[$newPack->getPackId()] = $newPack;
					}
				}else{
                    $logger->warning($this->server->getLanguage()->translateString("pocketmine.resourcepacks.packNotFound", [$path]));
				}
			}catch(\Throwable $e){
                $logger->logException($e);
			}
		}

        $logger->debug($this->server->getLanguage()->translateString("pocketmine.resourcepacks.loadFinished", [count($this->resourcePacks)]));
	}

	/**
	 * @return bool
	 */
	public function resourcePacksRequired() : bool{
		return $this->serverForceResources;
	}

	/**
	 * @return ResourcePack[]
	 */
	public function getResourceStack() : array{
		return $this->resourcePacks;
	}

	/**
	 * @param string $id
	 *
	 * @return ResourcePack|null
	 */
	public function getPackById(string $id){
		return $this->uuidList[$id] ?? null;
	}

    /**
     * Returns the directory which resource packs are loaded from.
     * @return string
     */
	public function getPath() : string{
        return $this->path;
    }

	/**
	 * @return string[]
	 */
	public function getPackIdList() : array{
		return array_keys($this->uuidList);
	}
}
