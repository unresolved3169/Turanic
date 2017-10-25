<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

/**
 * Network-related classes
 */

namespace pocketmine\network;

use pocketmine\network\mcpe\protocol\AddBehaviorTreePacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\AddHangingEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket;
use pocketmine\network\mcpe\protocol\AddPaintingPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\ClientToServerHandshakePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\CommandOutputPacket;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\CameraPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\EntityPickRequestPacket;
use pocketmine\network\mcpe\protocol\EventPacket;
use pocketmine\network\mcpe\protocol\ExplodePacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\GuiDataPickItemPacket;
use pocketmine\network\mcpe\protocol\HurtArmorPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\NpcRequestPacket;
use pocketmine\network\mcpe\protocol\PhotoTransferPacket;
use pocketmine\network\mcpe\protocol\PingPacket;
use pocketmine\network\mcpe\protocol\PlayerHotbarPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\PurchaseReceiptPacket;
use pocketmine\network\mcpe\protocol\ServerSettingsRequestPacket;
use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetLastHurtByPacket;
use pocketmine\network\mcpe\protocol\ShowProfilePacket;
use pocketmine\network\mcpe\protocol\ShowStoreOfferPacket;
use pocketmine\network\mcpe\protocol\SimpleEventPacket;
use pocketmine\network\mcpe\protocol\StructureBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\SubClientLoginPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateEquipPacket;
use pocketmine\network\mcpe\protocol\WSConnectPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\EntityFallPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\RiderJumpPacket;
use pocketmine\network\mcpe\protocol\SetCommandsEnabledPacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetHealthPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\ServerToClientHandshakePacket;
use pocketmine\network\mcpe\protocol\ShowCreditsPacket;
use pocketmine\network\mcpe\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

class Network {

	public static $BATCH_THRESHOLD = 512;

	/** @var \SplFixedArray */
	private $packetPool;

	/** @var Server */
	private $server;

	/** @var SourceInterface[] */
	private $interfaces = [];

	/** @var AdvancedSourceInterface[] */
	private $advancedInterfaces = [];

	private $upload = 0;
	private $download = 0;

	private $name;

	/**
	 * Network constructor.
	 *
	 * @param Server $server
	 */
	public function __construct(Server $server){

		$this->registerPackets();

		$this->server = $server;
	}

	/**
	 * @param $upload
	 * @param $download
	 */
	public function addStatistics($upload, $download){
		$this->upload += $upload;
		$this->download += $download;
	}

	/**
	 * @return int
	 */
	public function getUpload(){
		return $this->upload;
	}

	/**
	 * @return int
	 */
	public function getDownload(){
		return $this->download;
	}

	public function resetStatistics(){
		$this->upload = 0;
		$this->download = 0;
	}

	/**
	 * @return SourceInterface[]
	 */
	public function getInterfaces(){
		return $this->interfaces;
	}

	public function processInterfaces(){
		foreach($this->interfaces as $interface){
			try{
				$interface->process();
			}catch(\Throwable $e){
				$logger = $this->server->getLogger();
				if(\pocketmine\DEBUG > 1){
					if($logger instanceof MainLogger){
						$logger->logException($e);
					}
				}

				$interface->emergencyShutdown();
				$this->unregisterInterface($interface);
				$logger->critical($this->server->getLanguage()->translateString("pocketmine.server.networkError", [get_class($interface), $e->getMessage()]));
			}
		}
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function registerInterface(SourceInterface $interface){
        $interface->start();
        $this->interfaces[$hash = spl_object_hash($interface)] = $interface;
        if($interface instanceof AdvancedSourceInterface){
			$this->advancedInterfaces[$hash] = $interface;
			$interface->setNetwork($this);
		}
		$interface->setName($this->name);
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function unregisterInterface(SourceInterface $interface){
		unset($this->interfaces[$hash = spl_object_hash($interface)],
			$this->advancedInterfaces[$hash]);
	}

	/**
	 * Sets the server name shown on each interface Query
	 *
	 * @param string $name
	 */
	public function setName($name){
		$this->name = (string) $name;
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	public function getName(){
		return $this->name;
	}

	public function updateName(){
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	/**
	 * @param DataPacket $class
	 */
	public function registerPacket(DataPacket $class){
		$this->packetPool[$class->pid()] = $class;
	}

	/**
	 * @return Server
	 */
	public function getServer(){
		return $this->server;
	}

	/**
	 * @param $id
	 *
	 * @return DataPacket
	 */
	public function getPacket($id){
		/** @var DataPacket $class */
		$class = $this->packetPool[$id] ?? null;
		if($class !== null){
			return clone $class;
		}
		return null;
	}


	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 */
	public function sendPacket($address, $port, $payload){
		foreach($this->advancedInterfaces as $interface){
			$interface->sendRawPacket($address, $port, $payload);
		}
	}

	/**
	 * Blocks an IP address from the main interface. Setting timeout to -1 will block it forever
	 *
	 * @param string $address
	 * @param int    $timeout
	 */
	public function blockAddress($address, $timeout = 300){
		foreach($this->advancedInterfaces as $interface){
			$interface->blockAddress($address, $timeout);
		}
	}

	/**
	 * Unblocks an IP address from the main interface.
	 *
	 * @param string $address
	 */
	public function unblockAddress($address){
		foreach($this->advancedInterfaces as $interface){
			$interface->unblockAddress($address);
		}
	}

    /**
     *
     */
    private function registerPackets(){
		$this->packetPool = new \SplFixedArray(256);

        static::registerPacket(new LoginPacket());
        static::registerPacket(new PlayStatusPacket());
        static::registerPacket(new ServerToClientHandshakePacket());
        static::registerPacket(new ClientToServerHandshakePacket());
        static::registerPacket(new DisconnectPacket());
        static::registerPacket(new ResourcePacksInfoPacket());
        static::registerPacket(new ResourcePackStackPacket());
        static::registerPacket(new ResourcePackClientResponsePacket());
        static::registerPacket(new TextPacket());
        static::registerPacket(new SetTimePacket());
        static::registerPacket(new StartGamePacket());
        static::registerPacket(new AddPlayerPacket());
        static::registerPacket(new AddEntityPacket());
        static::registerPacket(new RemoveEntityPacket());
        static::registerPacket(new AddItemEntityPacket());
        static::registerPacket(new AddHangingEntityPacket());
        static::registerPacket(new TakeItemEntityPacket());
        static::registerPacket(new MoveEntityPacket());
        static::registerPacket(new MovePlayerPacket());
        static::registerPacket(new RiderJumpPacket());
        static::registerPacket(new UpdateBlockPacket());
        static::registerPacket(new AddPaintingPacket());
        static::registerPacket(new ExplodePacket());
        static::registerPacket(new LevelSoundEventPacket());
        static::registerPacket(new LevelEventPacket());
        static::registerPacket(new BlockEventPacket());
        static::registerPacket(new EntityEventPacket());
        static::registerPacket(new MobEffectPacket());
        static::registerPacket(new UpdateAttributesPacket());
        static::registerPacket(new InventoryTransactionPacket());
        static::registerPacket(new MobEquipmentPacket());
        static::registerPacket(new MobArmorEquipmentPacket());
        static::registerPacket(new InteractPacket());
        static::registerPacket(new BlockPickRequestPacket());
        static::registerPacket(new EntityPickRequestPacket());
        static::registerPacket(new PlayerActionPacket());
        static::registerPacket(new EntityFallPacket());
        static::registerPacket(new HurtArmorPacket());
        static::registerPacket(new SetEntityDataPacket());
        static::registerPacket(new SetEntityMotionPacket());
        static::registerPacket(new SetEntityLinkPacket());
        static::registerPacket(new SetHealthPacket());
        static::registerPacket(new SetSpawnPositionPacket());
        static::registerPacket(new AnimatePacket());
        static::registerPacket(new RespawnPacket());
        static::registerPacket(new ContainerOpenPacket());
        static::registerPacket(new ContainerClosePacket());
        static::registerPacket(new PlayerHotbarPacket());
        static::registerPacket(new InventoryContentPacket());
        static::registerPacket(new InventorySlotPacket());
        static::registerPacket(new ContainerSetDataPacket());
        static::registerPacket(new CraftingDataPacket());
        static::registerPacket(new CraftingEventPacket());
        static::registerPacket(new GuiDataPickItemPacket());
        static::registerPacket(new AdventureSettingsPacket());
        static::registerPacket(new BlockEntityDataPacket());
        static::registerPacket(new PlayerInputPacket());
        static::registerPacket(new FullChunkDataPacket());
        static::registerPacket(new SetCommandsEnabledPacket());
        static::registerPacket(new SetDifficultyPacket());
        static::registerPacket(new ChangeDimensionPacket());
        static::registerPacket(new SetPlayerGameTypePacket());
        static::registerPacket(new PlayerListPacket());
        static::registerPacket(new SimpleEventPacket());
        static::registerPacket(new EventPacket());
        static::registerPacket(new SpawnExperienceOrbPacket());
        static::registerPacket(new ClientboundMapItemDataPacket());
        static::registerPacket(new MapInfoRequestPacket());
        static::registerPacket(new RequestChunkRadiusPacket());
        static::registerPacket(new ChunkRadiusUpdatedPacket());
        static::registerPacket(new ItemFrameDropItemPacket());
        static::registerPacket(new GameRulesChangedPacket());
        static::registerPacket(new CameraPacket());
        static::registerPacket(new BossEventPacket());
        static::registerPacket(new ShowCreditsPacket());
        static::registerPacket(new AvailableCommandsPacket());
        static::registerPacket(new CommandRequestPacket());
        static::registerPacket(new CommandBlockUpdatePacket());
        static::registerPacket(new CommandOutputPacket());
        static::registerPacket(new UpdateTradePacket());
        static::registerPacket(new UpdateEquipPacket());
        static::registerPacket(new ResourcePackDataInfoPacket());
        static::registerPacket(new ResourcePackChunkDataPacket());
        static::registerPacket(new ResourcePackChunkRequestPacket());
        static::registerPacket(new TransferPacket());
        static::registerPacket(new PlaySoundPacket());
        static::registerPacket(new StopSoundPacket());
        static::registerPacket(new SetTitlePacket());
        static::registerPacket(new AddBehaviorTreePacket());
        static::registerPacket(new StructureBlockUpdatePacket());
        static::registerPacket(new ShowStoreOfferPacket());
        static::registerPacket(new PurchaseReceiptPacket());
        static::registerPacket(new PlayerSkinPacket());
        static::registerPacket(new SubClientLoginPacket());
        static::registerPacket(new WSConnectPacket());
        static::registerPacket(new SetLastHurtByPacket());
        static::registerPacket(new BookEditPacket());
        static::registerPacket(new NpcRequestPacket());
        static::registerPacket(new PhotoTransferPacket());
        static::registerPacket(new ModalFormRequestPacket());
        static::registerPacket(new ModalFormResponsePacket());
        static::registerPacket(new ServerSettingsRequestPacket());
        static::registerPacket(new ServerSettingsResponsePacket());
        static::registerPacket(new ShowProfilePacket());
        static::registerPacket(new SetDefaultGameTypePacket());
        static::registerPacket(new BatchPacket());
        static::registerPacket(new PingPacket());
	}
}