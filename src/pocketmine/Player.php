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

namespace pocketmine;

use pocketmine\block\Bed;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\CommandBlock;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Attribute;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\object\Item as DroppedItem;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\FishingHook;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\form\{
    FormCloseEvent, FormDataReceiveEvent
};
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerEditBookEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\Timings;
use pocketmine\event\TranslationContainer;
use pocketmine\form\CustomForm;
use pocketmine\form\element\Label;
use pocketmine\form\Form;
use pocketmine\inventory\AnvilInventory;
use pocketmine\inventory\BigCraftingGrid;
use pocketmine\inventory\CraftingGrid;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\AnvilTransaction;
use pocketmine\inventory\transaction\CraftingTransaction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\{
    Bucket, Consumable, Elytra, WritableBook, WrittenBook
};
use pocketmine\item\Item;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\level\WeakPosition;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\EntityFallPacket;
use pocketmine\network\mcpe\protocol\EntityPickRequestPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PingPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerHotbarPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\ServerSettingsRequestPacket;
use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionAttachmentInfo;
use pocketmine\plugin\Plugin;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\tile\CommandBlock as TileCommandBlock;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\tile\VirtualHolder;
use pocketmine\tile\ItemFrame;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

class Player extends Human implements CommandSender, InventoryHolder, ChunkLoader, IPlayer{

    const OS_ANDROID = 1;
    const OS_IOS = 2;
    const OS_OSX = 3;
    const OS_FIREOS = 4;
    const OS_GEARVR = 5;
    const OS_HOLOLENS = 6;
    const OS_WIN10 = 7;
    const OS_WIN32 = 8;
    const OS_DEDICATED = 9;
    const OS_ORBIS = 10;
    const OS_NX = 11;

	const SURVIVAL = 0;
	const CREATIVE = 1;
	const ADVENTURE = 2;
	const SPECTATOR = 3;
	const VIEW = Player::SPECTATOR;

    /**
     * Checks a supplied username and checks it is valid.
     * @param string $name
     *
     * @return bool
     */
    public static function isValidUserName($name) : bool{
        if($name === null){
            return false;
        }

        $lname = strtolower($name);
        $len = strlen($name);
        return $lname !== "rcon" and $lname !== "console" and $len >= 1 and $len <= 16 and preg_match("/[^A-Za-z0-9_ ]/", $name) === 0;
    }

	/** @var SourceInterface */
	protected $interface;

	/** @var bool */
	protected $isTeleporting = false;

	/** @var bool */
	public $playedBefore = false;
	public $spawned = false;
	public $loggedIn = false;
	public $gamemode;

	protected $windowCnt = 2;
	/** @var \SplObjectStorage<Inventory> */
	protected $windows;
	/** @var Inventory[] */
	protected $windowIndex = [];

	/** @var bool[] */
	protected $permanentWindows = [];

	protected $messageCounter = 2;

	private $clientSecret;

	/** @var Vector3 */
	public $speed = null;

	public $achievements = [];

	public $creationTime = 0;

	protected $randomClientId;

	protected $protocol = ProtocolInfo::CURRENT_PROTOCOL;

	protected $connected = true;
	protected $ip;
	protected $removeFormat = false;
	protected $port;
	protected $username = "";
	protected $iusername = "";
	protected $displayName = "";
	protected $startAction = -1;
	/** @var Vector3 */
	protected $sleeping = null;
	protected $clientID = null;

	protected $deviceModel;
	protected $deviceOS;

	private $loaderId = 0;

	public $usedChunks = [];
	protected $chunkLoadCount = 0;
	protected $loadQueue = [];
	protected $nextChunkOrderRun = 5;

	/** @var Player[] */
	protected $hiddenPlayers = [];

	/** @var Vector3|null */
	protected $newPosition = null;

	protected $viewDistance = -1;
	protected $chunksPerTick;
	protected $spawnThreshold;
	/** @var null|WeakPosition */
	private $spawnPosition = null;

	protected $inAirTicks = 0;
	protected $startAirTicks = 5;

	protected $autoJump = true;
	protected $allowFlight = false;
	protected $flying = false;
	protected $muted = false;

	protected $allowMovementCheats = false;

	private $needACK = [];

	private $batchedPackets = [];

	/** @var PermissibleBase */
	private $perm = null;

	/** @var FishingHook */
	public $fishingHook = null;

	/** @var Position[] */
	public $selectedPos = [];
	/** @var Level[] */
	public $selectedLev = [];

	/** @var Item[] */
	protected $personalCreativeItems = [];

	/** @var int */
	public $lastEnderPearlUse = 0;
	/** @var  CraftingGrid */
	protected $craftingGrid;
    /** @var  PlayerCursorInventory */
	protected $cursorInventory;
	public $namedtag;
	public $server;
	public $boundingBox;
	protected $uuid;
	protected $rawUUID;
	protected $modalFormCnt = 0;
	protected $modalForms = [];
	protected $xuid = "";
	/** @var CustomForm */
	protected $defaultServerSettings;
	protected $portalStatus = self::PORTAL_STATUS_OUT;
    private $elytraIsActivated = false;

	const PORTAL_STATUS_OUT = 0;
	const PORTAL_STATUS_IN = 1;

    /** @var string */
    protected $locale = "en_US";

    /**
     * @var int
     * Last measurement of player's latency in milliseconds.
     */
    protected $lastPingMeasure = 1;

    /** @var CraftingTransaction|null */
    public $craftingTransaction = null;

    /**
     * @return TranslationContainer|string
     */
    public function getLeaveMessage(){
        if($this->spawned){
            return new TranslationContainer(TextFormat::YELLOW . "%multiplayer.player.left", [
                $this->getDisplayName()
            ]);
        }

        return "";
    }

    /**
     * This might disappear in the future. Please use getUniqueId() instead.
     * @deprecated
     *
     * @return int
     */
    public function getClientId(){
        return $this->randomClientId;
    }

    public function getClientSecret(){
        return $this->clientSecret;
    }

    public function isBanned() : bool{
        return $this->server->getNameBans()->isBanned($this->iusername);
    }

    public function setBanned(bool $value){
        if($value === true){
            $this->server->getNameBans()->addBan($this->getName(), null, null, null);
            $this->kick("You have been banned");
        }else{
            $this->server->getNameBans()->remove($this->getName());
        }
    }

    public function isWhitelisted() : bool{
        return $this->server->isWhitelisted($this->iusername);
    }

    public function setWhitelisted(bool $value){
        if($value === true){
            $this->server->addWhitelist($this->iusername);
        }else{
            $this->server->removeWhitelist($this->iusername);
        }
    }

    /**
     * If the player is logged into Xbox Live, returns their Xbox user ID (XUID) as a string. Returns an empty string if
     * the player is not logged into Xbox Live.
     *
     * @return string
     */
    public function getXuid() : string{
        return $this->xuid;
    }

    /**
     * Returns the player's UUID. This should be preferred over their Xbox user ID (XUID) because UUID is a standard
     * format which will never change, and all players will have one regardless of whether they are logged into Xbox
     * Live.
     *
     * The UUID is comprised of:
     * - when logged into XBL: a hash of their XUID (and as such will not change for the lifetime of the XBL account)
     * - when NOT logged into XBL: a hash of their name + clientID + secret device ID.
     *
     * WARNING: UUIDs of players **not logged into Xbox Live** CAN BE FAKED and SHOULD NOT be trusted!
     *
     * (In the olden days this method used to return a fake UUID computed by the server, which was used by plugins such
     * as SimpleAuth for authentication. This is NOT SAFE anymore as this UUID is now what was given by the client, NOT
     * a server-computed UUID.)
     *
     * @return UUID|null
     */
    public function getUniqueId(){
        return parent::getUniqueId();
    }

    public function getPlayer(){
        return $this;
    }

    public function getFirstPlayed(){
        return $this->namedtag instanceof CompoundTag ? $this->namedtag->getLong("firstPlayed", 0, true) : null;
    }

    public function getLastPlayed(){
        return $this->namedtag instanceof CompoundTag ? $this->namedtag->getLong("lastPlayed", 0, true) : null;
    }

    public function hasPlayedBefore() : bool{
        return $this->playedBefore;
    }

    public function setAllowFlight(bool $value){
        $this->allowFlight = $value;
        $this->sendSettings();
    }

    public function getAllowFlight() : bool{
        return $this->allowFlight;
    }

    public function setFlying(bool $value){
        $this->flying = $value;
        $this->sendSettings();
    }

    public function isFlying() : bool{
        return $this->flying;
    }

    public function setAutoJump(bool $value){
        $this->autoJump = $value;
        $this->sendSettings();
    }

    public function hasAutoJump() : bool{
        return $this->autoJump;
    }

    public function allowMovementCheats() : bool{
        return $this->allowMovementCheats;
    }

    public function setAllowMovementCheats(bool $value = true){
        $this->allowMovementCheats = $value;
    }

    /**
     * @param Player $player
     */
    public function spawnTo(Player $player){
        if($this->spawned and $player->spawned and $this->isAlive() and $player->isAlive() and $player->getLevel() === $this->level and $player->canSee($this) and !$this->isSpectator()){
            parent::spawnTo($player);
        }
    }

    /**
     * @return Server
     */
    public function getServer(){
        return $this->server;
    }

    /**
     * @return bool
     */
    public function getRemoveFormat() : bool{
        return $this->removeFormat;
    }

    /**
     * @param bool $remove
     */
    public function setRemoveFormat(bool $remove = true){
        $this->removeFormat = $remove;
    }

    public function getScreenLineHeight() : int{
        return $this->lineHeight ?? 7;
    }

    public function setScreenLineHeight(int $height = null){
        if($height !== null and $height < 1){
            throw new \InvalidArgumentException("Line height must be at least 1");
        }
        $this->lineHeight = $height;
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function canSee(Player $player) : bool{
        return !isset($this->hiddenPlayers[$player->getRawUniqueId()]);
    }

    /**
     * @param Player $player
     */
    public function hidePlayer(Player $player){
        if($player === $this){
            return;
        }
        $this->hiddenPlayers[$player->getRawUniqueId()] = true;
        $player->despawnFrom($this);
    }

    /**
     * @param Player $player
     */
    public function showPlayer(Player $player){
        if($player === $this){
            return;
        }
        unset($this->hiddenPlayers[$player->getRawUniqueId()]);
        if($player->isOnline()){
            $player->spawnTo($this);
        }
    }

    public function canCollideWith(Entity $entity) : bool{
        return false;
    }

    public function resetFallDistance(){
        parent::resetFallDistance();
        if($this->inAirTicks !== 0){
            $this->startAirTicks = 5;
        }
        $this->inAirTicks = 0;
    }

    public function getViewDistance() : int{
        return $this->viewDistance;
    }

    public function setViewDistance(int $distance){
        $this->viewDistance = $this->server->getAllowedViewDistance($distance);

        $this->spawnThreshold = (int) (min($this->viewDistance, $this->server->getProperty("chunk-sending.spawn-radius", 4)) ** 2 * M_PI);

        $pk = new ChunkRadiusUpdatedPacket();
        $pk->radius = $this->viewDistance;
        $this->dataPacket($pk);

        $this->server->getLogger()->debug("Setting view distance for " . $this->getName() . " to " . $this->viewDistance . " (requested " . $distance . ")");
    }

    /**
     * @return bool
     */
    public function isOnline() : bool{
        return $this->isConnected() and $this->loggedIn === true;
    }

    /**
     * @return bool
     */
    public function isOp() : bool{
        return $this->server->isOp($this->getName());
    }

    /**
     * @param bool $value
     */
    public function setOp(bool $value){
        if($value === $this->isOp()){
            return;
        }

        if($value === true){
            $this->server->addOp($this->getName());
        }else{
            $this->server->removeOp($this->getName());
        }

        $this->sendSettings();
    }

    /**
     * @param permission\Permission|string $name
     *
     * @return bool
     */
    public function isPermissionSet($name) : bool{
        return $this->perm->isPermissionSet($name);
    }

    /**
     * @param permission\Permission|string $name
     *
     * @return bool
     *
     * @throws \InvalidStateException if the player is closed
     */
    public function hasPermission($name) : bool{
        if($this->closed){
            throw new \InvalidStateException("Trying to get permissions of closed player");
        }
        return $this->perm->hasPermission($name);
    }

    /**
     * @param Plugin $plugin
     * @param string $name
     * @param bool $value
     *
     * @return PermissionAttachment
     */
    public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment{
        return $this->perm->addAttachment($plugin, $name, $value);
    }

    /**
     * @param PermissionAttachment $attachment
     */
    public function removeAttachment(PermissionAttachment $attachment){
        $this->perm->removeAttachment($attachment);
    }

    public function recalculatePermissions(){
        $this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_USERS, $this);
        $this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);

        if($this->perm === null){
            return;
        }

        $this->perm->recalculatePermissions();

        if($this->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
            $this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_USERS, $this);
        }
        if($this->hasPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE)){
            $this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);
        }

        $this->sendCommandData();
    }

    /**
     * @return PermissionAttachmentInfo[]
     */
    public function getEffectivePermissions() : array{
        return $this->perm->getEffectivePermissions();
    }

	public function sendCommandData(){
		$pk = new AvailableCommandsPacket;
		$pk->commands = $this->server->getCommandMap()->getAvailableCommands($this);
		$this->dataPacket($pk);
	}

	/**
	 * @param SourceInterface $interface
	 * @param null $clientID
	 * @param string $ip
	 * @param int $port
	 */
	public function __construct(SourceInterface $interface, $clientID, string $ip, int $port){
		$this->interface = $interface;
		$this->windows = new \SplObjectStorage();
		$this->perm = new PermissibleBase($this);
		$this->namedtag = new CompoundTag();
		$this->server = Server::getInstance();
		$this->ip = $ip;
		$this->port = $port;
		$this->clientID = $clientID;
		$this->loaderId = Level::generateChunkLoaderId($this);
		$this->chunksPerTick = (int)$this->server->getProperty("chunk-sending.per-tick", 4);
		$this->spawnThreshold = (int)(($this->server->getProperty("chunk-sending.spawn-radius", 4) ** 2) * M_PI);
		$this->gamemode = $this->server->getGamemode();
		$this->setLevel($this->server->getDefaultLevel());
		$this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);

		$this->uuid = null;
		$this->rawUUID = null;

		$this->creationTime = microtime(true);

		$this->allowMovementCheats = (bool)$this->server->getProperty("player.anti-cheat.allow-movement-cheats", false);

		/**
		 * A CustomForm about Turanic
		 * You can edit this with Player::setDefaultServerSettings function
		 */
		$form = new CustomForm("Turanic Server Software");
		$form->setIconUrl("https://avatars2.githubusercontent.com/u/31800317?s=400&v=4"); // turanic logo
		$form->addElement(new Label("Turanic is a MC:BE Server Software\nYou can download it from github: https://github.com/TuranicTeam/Turanic"));

		$this->defaultServerSettings = $form;
	}

    /**
     * @return bool
     */
    public function isConnected(): bool{
        return $this->connected === true;
    }

    /**
     * Gets the username
     * @return string
     */
    public function getName() : string{
        return $this->username;
    }

    /**
     * @return string
     */
    public function getLowerCaseName() : string{
        return $this->iusername;
    }

    /**
     * Gets the "friendly" name to display of this player to use in the chat.
     *
     * @return string
     */
    public function getDisplayName() : string{
        return $this->displayName;
    }

    /**
     * @param string $name
     */
    public function setDisplayName(string $name){
        $this->displayName = $name;
        if($this->spawned){
            $this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->getDisplayName(), $this->getSkin());
        }
    }

    /**
     * Returns the player's locale, e.g. en_US.
     * @return string
     */
    public function getLocale() : string{
        return $this->locale;
    }

    /**
     * Called when a player changes their skin.
     * Plugin developers should not use this, use setSkin() and sendSkin() instead.
     *
     * @param Skin   $skin
     * @param string $newSkinName
     * @param string $oldSkinName
     *
     * @return bool
     */
    public function changeSkin(Skin $skin, string $newSkinName, string $oldSkinName) : bool{
        if(!$skin->isValid()){
            return false;
        }

        $ev = new PlayerChangeSkinEvent($this, $this->getSkin(), $skin);
        $this->server->getPluginManager()->callEvent($ev);

        if($ev->isCancelled()){
            $this->sendSkin([$this]);
            return true;
        }

        $this->setSkin($ev->getNewSkin());
        $this->sendSkin($this->server->getOnlinePlayers());
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * If null is given, will additionally send the skin to the player itself as well as its viewers.
     */
    public function sendSkin(array $targets = null){
        parent::sendSkin($targets ?? $this->server->getOnlinePlayers());
    }

    /**
     * Gets the player IP address
     *
     * @return string
     */
    public function getAddress() : string{
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort() : int{
        return $this->port;
    }

    /**
     * Returns the last measured latency for this player, in milliseconds. This is measured automatically and reported
     * back by the network interface.
     *
     * @return int
     */
    public function getPing() : int{
        return $this->lastPingMeasure;
    }

    /**
     * Updates the player's last ping measurement.
     *
     * @internal Plugins should not use this method.
     *
     * @param int $pingMS
     */
	public function updatePing(int $pingMS){
        $this->lastPingMeasure = $pingMS;
    }

    /**
     * @return Position
     */
    public function getNextPosition() : Position{
        return $this->newPosition !== null ? Position::fromObject($this->newPosition, $this->level) : $this->getPosition();
    }

    public function getInAirTicks() : int{
        return $this->inAirTicks;
    }

    /**
     * Returns whether the player is currently using an item (right-click and hold).
     * @return bool
     */
    public function isUsingItem() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_ACTION) and $this->startAction > -1;
    }

    public function setUsingItem(bool $value){
        $this->startAction = $value ? $this->server->getTick() : -1;
        $this->setGenericFlag(self::DATA_FLAG_ACTION, $value);
    }

    /**
     * Returns how long the player has been using their currently-held item for. Used for determining arrow shoot force
     * for bows.
     *
     * @return int
     */
    public function getItemUseDuration() : int{
        return $this->startAction === -1 ? -1 : ($this->server->getTick() - $this->startAction);
    }

    protected function switchLevel(Level $targetLevel) : bool{
        $oldLevel = $this->level;
        if(parent::switchLevel($targetLevel)){
            foreach($this->usedChunks as $index => $d){
                Level::getXZ($index, $X, $Z);
                $this->unloadChunk($X, $Z, $oldLevel);
            }

            $this->usedChunks = [];
            $this->level->sendTime($this);
            $this->level->sendDifficulty($this);

            return true;
        }

        return false;
    }

    protected function unloadChunk(int $x, int $z, Level $level = null){
        $level = $level ?? $this->level;
        $index = Level::chunkHash($x, $z);
        if(isset($this->usedChunks[$index])){
            foreach($level->getChunkEntities($x, $z) as $entity){
                if($entity !== $this){
                    $entity->despawnFrom($this);
                }
            }

            unset($this->usedChunks[$index]);
        }
        $level->unregisterChunkLoader($this, $x, $z);
        unset($this->loadQueue[$index]);
    }

    public function sendChunk(int $x, int $z, BatchPacket $payload){
        if(!$this->isConnected()){
            return;
        }

        $this->usedChunks[Level::chunkHash($x, $z)] = true;
        $this->chunkLoadCount++;

        $this->dataPacket($payload);

        if($this->spawned){
            foreach($this->level->getChunkEntities($x, $z) as $entity){
                if($entity !== $this and !$entity->isClosed() and $entity->isAlive()){
                    $entity->spawnTo($this);
                }
            }
        }

        if($this->chunkLoadCount >= $this->spawnThreshold and $this->spawned === false){
            $this->doFirstSpawn();
        }
    }

    protected function sendNextChunk(){
        if(!$this->isConnected()){
            return;
        }

        Timings::$playerChunkSendTimer->startTiming();

        $count = 0;
        foreach($this->loadQueue as $index => $distance){
            if($count >= $this->chunksPerTick){
                break;
            }

            Level::getXZ($index, $X, $Z);
            assert(is_int($X) and is_int($Z));

            ++$count;

            $this->usedChunks[$index] = false;
            $this->level->registerChunkLoader($this, $X, $Z, false);

            if(!$this->level->populateChunk($X, $Z)){
                continue;
            }

            unset($this->loadQueue[$index]);
            $this->level->requestChunk($X, $Z, $this);
        }

        Timings::$playerChunkSendTimer->stopTiming();
    }

	protected function doFirstSpawn(){
		$this->spawned = true;

        $this->sendPlayStatus(PlayStatusPacket::PLAYER_SPAWN);

        if($this->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_USERS, $this);
		}
		if($this->hasPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);
		}

		$this->server->getPluginManager()->callEvent($ev = new PlayerJoinEvent($this,
			new TranslationContainer(TextFormat::YELLOW . "%multiplayer.player.joined", [
				$this->getDisplayName()
			])
		));

        switch($this->server->playerMsgType){
            case Server::PLAYER_MSG_TYPE_MESSAGE:
                if(strlen(trim((string) $ev->getJoinMessage())) > 0) $this->server->broadcastMessage($ev->getJoinMessage());
                break;
            case Server::PLAYER_MSG_TYPE_TIP:
                $this->server->broadcastTip(str_replace("@player", $this->getDisplayName(), $this->server->playerLoginMsg));
                break;
            case Server::PLAYER_MSG_TYPE_POPUP:
                $this->server->broadcastPopup(str_replace("@player", $this->getDisplayName(), $this->server->playerLoginMsg));
                break;
        }

		$this->noDamageTicks = 60;

		foreach($this->usedChunks as $index => $c){
			Level::getXZ($index, $chunkX, $chunkZ);
			foreach($this->level->getChunkEntities($chunkX, $chunkZ) as $entity){
				if($entity !== $this and !$entity->isClosed() and $entity->isAlive() and !$entity->isFlaggedForDespawn()){
					$entity->spawnTo($this);
				}
			}
		}

		$this->spawnToAll();

		if($this->getHealth() <= 0){
			$this->sendRespawnPacket($this->getSpawn());
		}
	}

	protected function sendRespawnPacket(Vector3 $pos){
		$pk = new RespawnPacket();
		$pk->position = $pos->add(0, $this->getEyeHeight(), 0);

		$this->dataPacket($pk);
	}

    protected function orderChunks(){
        if(!$this->isConnected() or $this->viewDistance === -1){
            return false;
        }

        Timings::$playerChunkOrderTimer->startTiming();

        $this->nextChunkOrderRun = 200;

        $radius = $this->server->getAllowedViewDistance($this->viewDistance);
        $radiusSquared = $radius ** 2;

        $newOrder = [];
        $unloadChunks = $this->usedChunks;

        $centerX = $this->x >> 4;
        $centerZ = $this->z >> 4;

        for($x = 0; $x < $radius; ++$x){
            for($z = 0; $z <= $x; ++$z){
                if(($x ** 2 + $z ** 2) > $radiusSquared){
                    break; //skip to next band
                }

                //If the chunk is in the radius, others at the same offsets in different quadrants are also guaranteed to be.

                /* Top right quadrant */
                if(!isset($this->usedChunks[$index = Level::chunkHash($centerX + $x, $centerZ + $z)]) or $this->usedChunks[$index] === false){
                    $newOrder[$index] = true;
                }
                unset($unloadChunks[$index]);

                /* Top left quadrant */
                if(!isset($this->usedChunks[$index = Level::chunkHash($centerX - $x - 1, $centerZ + $z)]) or $this->usedChunks[$index] === false){
                    $newOrder[$index] = true;
                }
                unset($unloadChunks[$index]);

                /* Bottom right quadrant */
                if(!isset($this->usedChunks[$index = Level::chunkHash($centerX + $x, $centerZ - $z - 1)]) or $this->usedChunks[$index] === false){
                    $newOrder[$index] = true;
                }
                unset($unloadChunks[$index]);


                /* Bottom left quadrant */
                if(!isset($this->usedChunks[$index = Level::chunkHash($centerX - $x - 1, $centerZ - $z - 1)]) or $this->usedChunks[$index] === false){
                    $newOrder[$index] = true;
                }
                unset($unloadChunks[$index]);

                if($x !== $z){
                    /* Top right quadrant mirror */
                    if(!isset($this->usedChunks[$index = Level::chunkHash($centerX + $z, $centerZ + $x)]) or $this->usedChunks[$index] === false){
                        $newOrder[$index] = true;
                    }
                    unset($unloadChunks[$index]);

                    /* Top left quadrant mirror */
                    if(!isset($this->usedChunks[$index = Level::chunkHash($centerX - $z - 1, $centerZ + $x)]) or $this->usedChunks[$index] === false){
                        $newOrder[$index] = true;
                    }
                    unset($unloadChunks[$index]);

                    /* Bottom right quadrant mirror */
                    if(!isset($this->usedChunks[$index = Level::chunkHash($centerX + $z, $centerZ - $x - 1)]) or $this->usedChunks[$index] === false){
                        $newOrder[$index] = true;
                    }
                    unset($unloadChunks[$index]);

                    /* Bottom left quadrant mirror */
                    if(!isset($this->usedChunks[$index = Level::chunkHash($centerX - $z - 1, $centerZ - $x - 1)]) or $this->usedChunks[$index] === false){
                        $newOrder[$index] = true;
                    }
                    unset($unloadChunks[$index]);
                }
            }
        }

        foreach($unloadChunks as $index => $bool){
            Level::getXZ($index, $X, $Z);
            $this->unloadChunk($X, $Z);
        }

        $this->loadQueue = $newOrder;

        Timings::$playerChunkOrderTimer->stopTiming();

        return true;
    }

    /**
     * @return Position
     */
    public function getSpawn(){
        if($this->hasValidSpawnPosition()){
            return $this->spawnPosition;
        }else{
            $level = $this->server->getDefaultLevel();

            return $level->getSafeSpawn();
        }
    }

    /**
     * @return bool
     */
    public function hasValidSpawnPosition() : bool{
        return $this->spawnPosition instanceof WeakPosition and $this->spawnPosition->isValid();
    }

    /**
     * Sets the spawnpoint of the player (and the compass direction) to a Vector3, or set it on another world with a
     * Position object
     *
     * @param Vector3|Position $pos
     */
    public function setSpawn(Vector3 $pos){
        if(!($pos instanceof Position)){
            $level = $this->level;
        }else{
            $level = $pos->getLevel();
        }
        $this->spawnPosition = new WeakPosition($pos->x, $pos->y, $pos->z, $level);
        $pk = new SetSpawnPositionPacket();
        $pk->x = (int) $this->spawnPosition->x;
        $pk->y = (int) $this->spawnPosition->y;
        $pk->z = (int) $this->spawnPosition->z;
        $pk->spawnType = SetSpawnPositionPacket::TYPE_PLAYER_SPAWN;
        $pk->spawnForced = false;
        $this->dataPacket($pk);
    }

    /**
     * @return bool
     */
    public function isSleeping() : bool{
        return $this->sleeping !== null;
    }

    /**
     * @param Vector3 $pos
     *
     * @return bool
     */
    public function sleepOn(Vector3 $pos) : bool{
        if(!$this->isOnline()){
            return false;
        }

        $pos = $pos->floor();
        $b = $this->level->getBlock($pos);

        $this->server->getPluginManager()->callEvent($ev = new PlayerBedEnterEvent($this, $b));
        if($ev->isCancelled()){
            return false;
        }

        if($b instanceof Bed){
            $b->setOccupied();
        }

        $this->sleeping = clone $pos;

        $this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [$pos->x, $pos->y, $pos->z]);
        $this->setPlayerFlag(self::DATA_PLAYER_FLAG_SLEEP, true);

        $this->setSpawn($pos);

        $this->level->setSleepTicks(60);

        return true;
    }

    public function stopSleep(){
        if($this->sleeping instanceof Vector3){
            $b = $this->level->getBlock($this->sleeping);
            if($b instanceof Bed){
                $b->setOccupied(false);
            }
            $this->server->getPluginManager()->callEvent($ev = new PlayerBedLeaveEvent($this, $b));

            $this->sleeping = null;
            $this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [0, 0, 0]);
            $this->setPlayerFlag(self::DATA_PLAYER_FLAG_SLEEP, false);

            $this->level->setSleepTicks(0);

            $pk = new AnimatePacket();
            $pk->entityRuntimeId = $this->id;
            $pk->action = AnimatePacket::ACTION_STOP_SLEEP;
            $this->dataPacket($pk);
        }
    }

    /**
     * @param string $achievementId
     *
     * @return bool
     */
    public function hasAchievement(string $achievementId) : bool{
        if(!isset(Achievement::$list[$achievementId])){
            return false;
        }

        return isset($this->achievements[$achievementId]) and $this->achievements[$achievementId] !== false;
    }

    /**
     * @param string $achievementId
     *
     * @return bool
     */
    public function awardAchievement(string $achievementId) : bool{
        if(isset(Achievement::$list[$achievementId]) and !$this->hasAchievement($achievementId)){
            foreach(Achievement::$list[$achievementId]["requires"] as $requirementId){
                if(!$this->hasAchievement($requirementId)){
                    return false;
                }
            }
            $this->server->getPluginManager()->callEvent($ev = new PlayerAchievementAwardedEvent($this, $achievementId));
            if(!$ev->isCancelled()){
                $this->achievements[$achievementId] = true;
                Achievement::broadcast($this, $achievementId);

                return true;
            }else{
                return false;
            }
        }

        return false;
    }

    /**
     * @param string $achievementId
     */
    public function removeAchievement(string $achievementId){
        if($this->hasAchievement($achievementId)){
            $this->achievements[$achievementId] = false;
        }
    }

    /**
     * @return int
     */
    public function getGamemode() : int{
        return $this->gamemode;
    }

    /**
     * @internal
     *
     * Returns a client-friendly gamemode of the specified real gamemode
     * This function takes care of handling gamemodes known to MCPE (as of 1.1.0.3, that includes Survival, Creative and Adventure)
     *
     * TODO: remove this when Spectator Mode gets added properly to MCPE
     *
     * @param int $gamemode
     * @return int
     */
    public static function getClientFriendlyGamemode(int $gamemode) : int{
        $gamemode &= 0x03;
        if($gamemode === Player::SPECTATOR){
            return Player::CREATIVE;
        }

        return $gamemode;
    }

    /**
     * Sets the gamemode, and if needed, kicks the Player.
     *
     * @param int  $gm
     * @param bool $client if the client made this change in their GUI
     *
     * @return bool
     */
    public function setGamemode(int $gm, bool $client = false) : bool{
        if($gm < 0 or $gm > 3 or $this->gamemode === $gm){
            return false;
        }

        $this->server->getPluginManager()->callEvent($ev = new PlayerGameModeChangeEvent($this, $gm));
        if($ev->isCancelled()){
            if($client){ //gamemode change by client in the GUI
                $this->sendGamemode();
            }
            return false;
        }

        $this->gamemode = $gm;

        $this->allowFlight = $this->isCreative();
        if($this->isSpectator()){
            $this->flying = true;
            $this->keepMovement = true;
            $this->despawnFromAll();
        }else{
            $this->keepMovement = $this->allowMovementCheats;
            if($this->isSurvival()){
                $this->flying = false;
            }
            $this->spawnToAll();
        }

        $this->resetFallDistance();

        $this->namedtag->setInt("playerGameType", $this->gamemode);
        if(!$client){ //Gamemode changed by server, do not send for client changes
            $this->sendGamemode();
        }else{
            Command::broadcastCommandMessage($this, new TranslationContainer("commands.gamemode.success.self", [Server::getGamemodeString($gm)]));
        }

        $this->sendSettings();
        $this->inventory->sendCreativeContents();

        return true;
    }

    /**
     * @internal
     * Sends the player's gamemode to the client.
     */
    public function sendGamemode(){
        $pk = new SetPlayerGameTypePacket();
        $pk->gamemode = Player::getClientFriendlyGamemode($this->gamemode);
        $this->dataPacket($pk);
    }

	/**
	 * Sends all the option flags
	 */
	public function sendSettings(){
		$pk = new AdventureSettingsPacket();
		$pk->setPlayerFlag(AdventureSettingsPacket::WORLD_IMMUTABLE, $this->isSpectator());
		$pk->setPlayerFlag(AdventureSettingsPacket::NO_PVP, $this->isSpectator());
		$pk->setPlayerFlag(AdventureSettingsPacket::AUTO_JUMP, $this->autoJump);
		$pk->setPlayerFlag(AdventureSettingsPacket::ALLOW_FLIGHT, $this->allowFlight);
		$pk->setPlayerFlag(AdventureSettingsPacket::NO_CLIP, $this->isSpectator());
		$pk->setPlayerFlag(AdventureSettingsPacket::FLYING, $this->flying);

		$pk->commandPermission = ($this->isOp() ? AdventureSettingsPacket::PERMISSION_OPERATOR : AdventureSettingsPacket::PERMISSION_NORMAL);
		$pk->playerPermission = ($this->isOp() ? PlayerPermissions::OPERATOR : PlayerPermissions::MEMBER);
		$pk->entityUniqueId = $this->getId();

		$this->dataPacket($pk);
	}

    /**
     * NOTE: Because Survival and Adventure Mode share some similar behaviour, this method will also return true if the player is
     * in Adventure Mode. Supply the $literal parameter as true to force a literal Survival Mode check.
     *
     * @param bool $literal whether a literal check should be performed
     *
     * @return bool
     */
    public function isSurvival(bool $literal = false) : bool{
        if($literal){
            return $this->gamemode === Player::SURVIVAL;
        }else{
            return ($this->gamemode & 0x01) === 0;
        }
    }

    /**
     * NOTE: Because Creative and Spectator Mode share some similar behaviour, this method will also return true if the player is
     * in Spectator Mode. Supply the $literal parameter as true to force a literal Creative Mode check.
     *
     * @param bool $literal whether a literal check should be performed
     *
     * @return bool
     */
    public function isCreative(bool $literal = false) : bool{
        if($literal){
            return $this->gamemode === Player::CREATIVE;
        }else{
            return ($this->gamemode & 0x01) === 1;
        }
    }

    /**
     * NOTE: Because Adventure and Spectator Mode share some similar behaviour, this method will also return true if the player is
     * in Spectator Mode. Supply the $literal parameter as true to force a literal Adventure Mode check.
     *
     * @param bool $literal whether a literal check should be performed
     *
     * @return bool
     */
    public function isAdventure(bool $literal = false) : bool{
        if($literal){
            return $this->gamemode === Player::ADVENTURE;
        }else{
            return ($this->gamemode & 0x02) > 0;
        }
    }

    /**
     * @return bool
     */
    public function isSpectator() : bool{
        return $this->gamemode === Player::SPECTATOR;
    }

    /**
	 * @return bool
	 */
	public function isFireProof(): bool{
		return $this->isCreative();
	}

	/**
	 * @return array
	 */
	public function getDrops() : array{
        if (!$this->isCreative()) {
            return parent::getDrops();
        }

        return [];
    }

    public function getXpDropAmount() : int{
        if(!$this->isCreative()){
            return parent::getXpDropAmount();
        }

        return 0;
    }

    protected function checkGroundState(float $movX, float $movY, float $movZ, float $dx, float $dy, float $dz){
        if(!$this->onGround or $movY != 0){
            $bb = clone $this->boundingBox;
            $bb->minY = $this->y - 0.2;
            $bb->maxY = $this->y + 0.2;

            $this->onGround = count($this->level->getCollisionBlocks($bb, true)) > 0;
        }
        $this->isCollided = $this->onGround;
    }

    protected function checkBlockCollision(){
        foreach($this->getBlocksAround() as $block){
            $block->onEntityCollide($this);
        }
    }

    protected function checkNearEntities(int $tickDiff){
        foreach($this->level->getNearbyEntities($this->boundingBox->grow(1, 0.5, 1), $this) as $entity){
            $entity->scheduleUpdate();

            if(!$entity->isAlive() or $entity->isFlaggedForDespawn()){
                continue;
            }

            $entity->onCollideWithPlayer($this);
        }
    }

    /**
     * @param int $tickDiff
     */
    protected function processMovement(int $tickDiff){
        if(!$this->isAlive() or !$this->spawned or $this->newPosition === null or $this->isSleeping()){
            return;
        }

        assert($this->x !== null and $this->y !== null and $this->z !== null);
        assert($this->newPosition->x !== null and $this->newPosition->y !== null and $this->newPosition->z !== null);

        $newPos = $this->newPosition;
        $distanceSquared = $newPos->distanceSquared($this);

        $revert = false;

        if(!$this->allowMovementCheats && ($distanceSquared / ($tickDiff ** 2)) > 100){
            $this->server->getLogger()->warning($this->getName() . " moved too fast, reverting movement");
            $this->server->getLogger()->debug("Old position: " . $this->asVector3() . ", new position: " . $this->newPosition);
            $revert = true;
        }else{
            if($this->chunk === null or !$this->chunk->isGenerated()){
                $chunk = $this->level->getChunk($newPos->x >> 4, $newPos->z >> 4, false);
                if($chunk === null or !$chunk->isGenerated()){
                    $revert = true;
                    $this->nextChunkOrderRun = 0;
                }else{
                    if($this->chunk !== null){
                        $this->chunk->removeEntity($this);
                    }
                    $this->chunk = $chunk;
                }
            }
        }

        if(!$revert and $distanceSquared != 0){
            $dx = $newPos->x - $this->x;
            $dy = $newPos->y - $this->y;
            $dz = $newPos->z - $this->z;

            $this->move($dx, $dy, $dz);

            if ($this->newPosition == null) {
                return;
            }

            $diffX = $this->x - $newPos->x;
            $diffY = $this->y - $newPos->y;
            $diffZ = $this->z - $newPos->z;

            $diff = ($diffX ** 2 + $diffY ** 2 + $diffZ ** 2) / ($tickDiff ** 2);

            if($this->isSurvival() and !$revert and $diff > 0.0625){
                $ev = new PlayerIllegalMoveEvent($this, $newPos);
                $ev->setCancelled($this->allowMovementCheats);

                $this->server->getPluginManager()->callEvent($ev);

                if(!$ev->isCancelled()){
                    $revert = true;
                    $this->server->getLogger()->warning($this->getServer()->getLanguage()->translateString("pocketmine.player.invalidMove", [$this->getName()]));
                    $this->server->getLogger()->debug("Old position: " . $this->asVector3() . ", new position: " . $this->newPosition);
                }
            }

            if($diff > 0){
                $this->x = $newPos->x;
                $this->y = $newPos->y;
                $this->z = $newPos->z;
                $radius = $this->width / 2;
                $this->boundingBox->setBounds($this->x - $radius, $this->y, $this->z - $radius, $this->x + $radius, $this->y + $this->height, $this->z + $radius);
            }
        }

        $from = new Location($this->lastX, $this->lastY, $this->lastZ, $this->lastYaw, $this->lastPitch, $this->level);
        $to = $this->getLocation();

        $delta = (($this->lastX - $to->x) ** 2) + (($this->lastY - $to->y) ** 2) + (($this->lastZ - $to->z) ** 2);
        $deltaAngle = abs($this->lastYaw - $to->yaw) + abs($this->lastPitch - $to->pitch);

        if(!$revert and ($delta > 0.0001 or $deltaAngle > 1.0)){

            $isFirst = ($this->lastX === null or $this->lastY === null or $this->lastZ === null);

            $this->lastX = $to->x;
            $this->lastY = $to->y;
            $this->lastZ = $to->z;

            $this->lastYaw = $to->yaw;
            $this->lastPitch = $to->pitch;

            if(!$isFirst){
                $ev = new PlayerMoveEvent($this, $from, $to);

                $this->server->getPluginManager()->callEvent($ev);

                if(!($revert = $ev->isCancelled())){ //Yes, this is intended
                    if($to->distanceSquared($ev->getTo()) > 0.01){ //If plugins modify the destination
                        $this->teleport($ev->getTo());
                    }else{
                        $this->broadcastMovement();

                        $distance = $from->distance($to);
                        if($this->isSprinting()){
                            $this->exhaust(0.1 * $distance, PlayerExhaustEvent::CAUSE_SPRINTING);
                        }else{
                            $this->exhaust(0.01 * $distance, PlayerExhaustEvent::CAUSE_WALKING);
                        }
                    }
                }
            }

            $this->speed = $to->subtract($from)->divide($tickDiff);
        }elseif($distanceSquared == 0){
            $this->speed = new Vector3(0, 0, 0);
        }

        if($revert){

            $this->lastX = $from->x;
            $this->lastY = $from->y;
            $this->lastZ = $from->z;

            $this->lastYaw = $from->yaw;
            $this->lastPitch = $from->pitch;

            $this->setPosition($from);
            $this->sendPosition($from, $from->yaw, $from->pitch, MovePlayerPacket::MODE_RESET);
        }else{
            if($distanceSquared != 0 and $this->nextChunkOrderRun > 20){
                $this->nextChunkOrderRun = 20;
            }
        }

        $this->newPosition = null;
    }

    public function jump(){
        $this->server->getPluginManager()->callEvent(new PlayerJumpEvent($this));
        parent::jump();
    }

    public function setMotion(Vector3 $mot){
        if(parent::setMotion($mot)){
            if($this->chunk !== null){
                $this->broadcastMotion();
            }

            if($this->motionY > 0){
                $this->startAirTicks = (-log($this->gravity / ($this->gravity + $this->drag * $this->motionY)) / $this->drag) * 2 + 5;
            }

            return true;
        }
        return false;
    }

    protected function updateMovement(){

    }

    protected function tryChangeMovement(){

    }

    public function sendAttributes(bool $sendAll = false){
        $entries = $sendAll ? $this->attributeMap->getAll() : $this->attributeMap->needSend();
        if(count($entries) > 0){
            $pk = new UpdateAttributesPacket();
            $pk->entityRuntimeId = $this->id;
            $pk->entries = $entries;
            $this->dataPacket($pk);
            foreach($entries as $entry){
                $entry->markSynchronized();
            }
        }
    }

	/**
	 * @param int $id
	 * @param int $type
	 * @param mixed $value
	 *
	 * @param bool $send
	 * @return bool
	 */
	public function setDataProperty(int $id, int $type, $value, bool $send = true) : bool{
		if (parent::setDataProperty($id, $type, $value, $send)) {
			if($send) $this->sendData($this, [$id => $this->dataProperties[$id]]);
			return true;
		}

		return false;
	}

	public function move($dx, $dy, $dz){
		$this->checkGroundState(0,0,0,0,0,0);
		if($dx == 0 and $dz == 0 and $dy == 0){
			return true;
		}

		if($this->keepMovement){
			$this->boundingBox->offset($dx, $dy, $dz);
			$this->setPosition(new Vector3(($this->boundingBox->minX + $this->boundingBox->maxX) / 2, $this->boundingBox->minY, ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2));
			$this->onGround = true;
			return true;
		}else{
			$pos = new Vector3($this->x + $dx, $this->y + $dy, $this->z + $dz);
			if(!$this->setPosition($pos)){
				return false;
			}else{
				$this->checkChunks();
				$this->updateFallState($dy, $this->onGround);
			}
			return true;
		}
	}

    /**
     * @param $currentTick
     *
     * @return bool
     */
    public function onUpdate(int $currentTick){
        if(!$this->loggedIn){
            return false;
        }

        $tickDiff = $currentTick - $this->lastUpdate;

        if($tickDiff <= 0){
            return true;
        }

        $this->messageCounter = 2;

        $this->lastUpdate = $currentTick;

        $this->sendAttributes();

        if(!$this->isAlive() and $this->spawned){
            $this->onDeathUpdate($tickDiff);
            return true;
        }

        $this->timings->startTiming();

        if ($this->spawned) {
            $this->processMovement($tickDiff);

            Timings::$timerEntityBaseTick->startTiming();
            $this->entityBaseTick($tickDiff);
            Timings::$timerEntityBaseTick->stopTiming();

            if ($this->isOnFire() or $this->lastUpdate % 10 == 0) {
                if ($this->isCreative() and !$this->isInsideOfFire()) {
                    $this->extinguish();
                } elseif ($this->getLevel()->getWeather()->isRainy()) {
                    if ($this->getLevel()->canBlockSeeSky($this)) {
                        $this->extinguish();
                    }
                }
            }

            if (!$this->isSpectator() and $this->isAlive()) {
                if ($currentTick % 20 == 0) {
                    if ($portalType = $this->isInsideOfPortal()) {
                        if ($this->portalStatus === self::PORTAL_STATUS_OUT) {
                            $to = $this->level->getFolderName() == $portalType ? $this->server->getDefaultLevel()->getFolderName() : $portalType;
                            if ($targetLevel = $this->server->getLevelByName($to)) {
                                $this->teleport($targetLevel->getSafeSpawn());
                            }
                            $this->portalStatus = self::PORTAL_STATUS_IN;
                        }
                    } else {
                        $this->portalStatus = self::PORTAL_STATUS_OUT;
                    }
                }

                $this->checkNearEntities($tickDiff);
                if ($this->hasEffect(Effect::LEVITATION)) {
                    $this->inAirTicks = 0;
                }
                if ($this->onGround) {
                    if ($this->inAirTicks !== 0) {
                        $this->startAirTicks = 5;
                    }
                    if ($this->elytraIsActivated) {
                        $this->elytraIsActivated = false;
                    }
                    $this->inAirTicks = 0;
                } else {
                    if (!$this->isUseElytra() and !$this->flying and $this->inAirTicks > 10 and !$this->isSleeping() and $this->speed instanceof Vector3) {
                        $expectedVelocity = (-$this->gravity) / $this->drag - ((-$this->gravity) / $this->drag) * exp(-$this->drag * ($this->inAirTicks - $this->startAirTicks));
                        $diff = ($this->speed->y - $expectedVelocity) ** 2;

                        if (!$this->hasEffect(Effect::JUMP) and $diff > 0.6 and $expectedVelocity < $this->speed->y and !$this->server->getAllowFlight()) {
                            if (!(PHP_INT_SIZE === 8 && $this->allowFlight) && $this->inAirTicks < 1000) {
                                $this->setMotion(new Vector3(0, $expectedVelocity, 0));
                            } elseif (!$this->allowFlight) {
                                $this->kick($this->server->getLanguage()->translateString("kick.reason.cheat", ["%ability.flight"]), false);
                                $this->timings->stopTiming();
                                return false;
                            }
                        }
                    }

                    $this->inAirTicks += $tickDiff;
                }
            }
        }

        $this->timings->stopTiming();

        return true;
    }

    public function doFoodTick(int $tickDiff = 1){
        if($this->isSurvival()){
            parent::doFoodTick($tickDiff);
        }
    }

    public function exhaust(float $amount, int $cause = PlayerExhaustEvent::CAUSE_CUSTOM) : float{
        if($this->isSurvival()){
            return parent::exhaust($amount, $cause);
        }

        return 0.0;
    }

    public function canBreathe() : bool{
        return $this->isCreative() or parent::canBreathe();
    }

    public function checkNetwork(){
        if(!$this->isOnline()){
            return;
        }

        if($this->nextChunkOrderRun-- <= 0 or $this->chunk === null){
            $this->orderChunks();
        }

        if(count($this->loadQueue) > 0){
            $this->sendNextChunk();
        }

        if(count($this->batchedPackets) > 0){
            $this->server->batchPackets([$this], $this->batchedPackets, false);
            $this->batchedPackets = [];
        }
    }

    /**
     * Returns whether the player can interact with the specified position. This checks distance and direction.
     *
     * @param Vector3 $pos
     * @param float   $maxDistance
     * @param float   $maxDiff defaults to half of the 3D diagonal width of a block
     *
     * @return bool
     */
    public function canInteract(Vector3 $pos, float $maxDistance, float $maxDiff = M_SQRT3 / 2) : bool{
        $eyePos = $this->getPosition()->add(0, $this->getEyeHeight(), 0);
        if($eyePos->distanceSquared($pos) > $maxDistance ** 2){
            return false;
        }

        $dV = $this->getDirectionVector();
        $eyeDot = $dV->dot($eyePos);
        $targetDot = $dV->dot($pos);
        return ($targetDot - $eyeDot) >= -$maxDiff;
    }

    protected function initHumanData(){
        $this->setNameTag($this->username);
    }

    protected function initEntity(){
        parent::initEntity();
        $this->addDefaultWindows();
    }

    public function handleLogin(LoginPacket $packet) : bool{
        if ($this->loggedIn)
            return false;

        $this->protocol = $packet->protocol;

        if (!in_array($this->protocol, ProtocolInfo::ACCEPTED_PROTOCOLS)) {
            if ($packet->protocol < ProtocolInfo::CURRENT_PROTOCOL) {
                $this->sendPlayStatus(PlayStatusPacket::LOGIN_FAILED_CLIENT, true);
            } else {
                $this->sendPlayStatus(PlayStatusPacket::LOGIN_FAILED_SERVER, true);
            }

            //This turanic disconnect message will only be seen by the console (PlayStatusPacket causes the messages to be shown for the client)
            $this->close("", $this->server->getLanguage()->translateString("pocketmine.disconnect.incompatibleProtocol", [$packet->protocol]), false);

            return true;
        }

        if(!self::isValidUserName($packet->username)){
            $this->close("", "disconnectionScreen.invalidName");
            return true;
        }

        $this->username = TextFormat::clean($packet->username);
        $this->displayName = $this->username;
        $this->iusername = strtolower($this->username);

        if($packet->locale !== null){
            $this->locale = $packet->locale;
        }

        if (count($this->server->getOnlinePlayers()) >= $this->server->getMaxPlayers() and $this->kick("disconnectionScreen.serverFull", false)) {
            return true;
        }

        $this->randomClientId = $packet->clientId;

        $this->uuid = UUID::fromString($packet->clientUUID);
        $this->rawUUID = $this->uuid->toBinary();
        $this->xuid = $packet->xuid;
        $this->deviceOS = $packet->clientData["DeviceOS"];
        $this->deviceModel = $packet->clientData["DeviceModel"];

        $skin = new Skin(
            $packet->clientData["SkinId"],
            base64_decode($packet->clientData["SkinData"] ?? ""),
            base64_decode($packet->clientData["CapeData"] ?? ""),
            $packet->clientData["SkinGeometryName"],
            base64_decode($packet->clientData["SkinGeometry"] ?? "")
        );
        $skin->debloatGeometryData();

        if (!$skin->isValid()) {
            $this->close("", "disconnectionScreen.invalidSkin");
            return true;
        }

        $this->setSkin($skin);

        $this->server->getPluginManager()->callEvent($ev = new PlayerPreLoginEvent($this, "Plugin reason"));
        if ($ev->isCancelled()) {
            $this->close("", $ev->getKickMessage());

            return true;
        }

        if (!$this->server->isWhitelisted($this->iusername) and $this->kick("Server is white-listed", false)) {
            return true;
        }

        if (
            ($this->server->getNameBans()->isBanned($this->iusername) or $this->server->getIPBans()->isBanned($this->getAddress())) and
            $this->kick("You are banned", false)
        ) {
            return true;
        }

        if ($packet->xuid != "") {
            $this->processLogin();
        }

        return true;
    }

    public function sendPlayStatus(int $status, bool $immediate = false){
        $pk = new PlayStatusPacket();
        $pk->status = $status;
        $pk->protocol = $this->protocol;
        $this->sendDataPacket($pk, false, $immediate);
    }

    protected function processLogin(){
        foreach($this->server->getLoggedInPlayers() as $p){
            if($p !== $this and ($p->iusername === $this->iusername or $this->getUniqueId()->equals($p->getUniqueId()))){
                if($p->kick("logged in from another location") === false){
                    $this->close($this->getLeaveMessage(), "Logged in from another location");

                    return;
                }
            }
        }

        $this->namedtag = $this->server->getOfflinePlayerData($this->username);

        $this->playedBefore = ($this->getLastPlayed() - $this->getFirstPlayed()) > 1; // microtime(true) - microtime(true) may have less than one millisecond difference
        $this->namedtag->setString("NameTag", $this->username);

        $this->gamemode = $this->namedtag->getInt("playerGameType", self::SURVIVAL) & 0x03;
        if($this->server->getForceGamemode()){
            $this->gamemode = $this->server->getGamemode();
            $this->namedtag->setInt("playerGameType", $this->gamemode);
        }

        $this->allowFlight = $this->isCreative();

        if(($level = $this->server->getLevelByName($this->namedtag->getString("Level", "", true))) === null){
            $this->setLevel($this->server->getDefaultLevel());
            $this->namedtag->setString("Level", $this->level->getName());
            $spawnLocation = $this->level->getSpawnLocation();
            $this->namedtag->setTag(new ListTag("Pos", [
                new DoubleTag("", $spawnLocation->x),
                new DoubleTag("", $spawnLocation->y),
                new DoubleTag("", $spawnLocation->z)
            ]));
        }else{
            $this->setLevel($level);
        }

        $this->achievements = [];

        $achievements = $this->namedtag->getCompoundTag("Achievements") ?? [];
        /** @var ByteTag $achievement */
        foreach($achievements as $achievement){
            $this->achievements[$achievement->getName()] = $achievement->getValue() !== 0;
        }

        $this->namedtag->setLong("lastPlayed", (int) floor(microtime(true) * 1000));
        if($this->server->getAutoSave()){
            $this->server->saveOfflinePlayerData($this->username, $this->namedtag, true);
        }

        $this->sendPlayStatus(PlayStatusPacket::LOGIN_SUCCESS);

        $this->loggedIn = true;
        $this->server->onPlayerLogin($this);

        $pk = new ResourcePacksInfoPacket();
        $manager = $this->server->getResourceManager();
        $pk->resourcePackEntries = $manager->getResourceStack();
        $pk->mustAccept = $manager->resourcePacksRequired();
        $this->dataPacket($pk);
    }

    public function handleResourcePackClientResponse(ResourcePackClientResponsePacket $packet) : bool{
        switch($packet->status){
            case ResourcePackClientResponsePacket::STATUS_REFUSED:
                $this->close("", "You must accept resource packs to join this server.", true);
                break;
            case ResourcePackClientResponsePacket::STATUS_SEND_PACKS:
                $manager = $this->server->getResourceManager();
                foreach($packet->packIds as $uuid){
                    $pack = $manager->getPackById($uuid);
                    if(!($pack instanceof ResourcePack)){
                        //Client requested a resource pack but we don't have it available on the server
                        $this->close("", "disconnectionScreen.resourcePack", true);
                        $this->server->getLogger()->debug("Got a resource pack request for unknown pack with UUID " . $uuid . ", available packs: " . implode(", ", $manager->getPackIdList()));
                        return false;
                    }

                    $pk = new ResourcePackDataInfoPacket();
                    $pk->packId = $pack->getPackId();
                    $pk->maxChunkSize = 1048576; //1MB
                    $pk->chunkCount = (int) ceil($pack->getPackSize() / $pk->maxChunkSize);
                    $pk->compressedPackSize = $pack->getPackSize();
                    $pk->sha256 = $pack->getSha256();
                    $this->dataPacket($pk);
                }

                break;
            case ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS:
                $pk = new ResourcePackStackPacket();
                $manager = $this->server->getResourceManager();
                $pk->resourcePackStack = $manager->getResourceStack();
                $pk->mustAccept = $manager->resourcePacksRequired();
                $this->dataPacket($pk);
                break;
            case ResourcePackClientResponsePacket::STATUS_COMPLETED:
                $this->completeLoginSequence();
                break;
            default:
                return false;
        }

        return true;
    }

    protected function completeLoginSequence(){
        parent::__construct($this->level, $this->namedtag);
        $this->server->getPluginManager()->callEvent($ev = new PlayerLoginEvent($this, "Plugin reason"));
        if($ev->isCancelled()){
            $this->close($this->getLeaveMessage(), $ev->getKickMessage());

            return;
        }

        if(!$this->hasValidSpawnPosition()){
            if(($level = $this->server->getLevelByName($this->namedtag->getString("SpawnLevel", ""))) instanceof Level){
                $this->spawnPosition = new WeakPosition($this->namedtag->getInt("SpawnX"), $this->namedtag->getInt("SpawnY"), $this->namedtag->getInt("SpawnZ"), $level);
            }else{
                $this->spawnPosition = WeakPosition::fromObject($this->level->getSafeSpawn());
            }
        }

        $spawnPosition = $this->getSpawn();

        $pk = new StartGamePacket();
        $pk->entityUniqueId = $this->id;
        $pk->entityRuntimeId = $this->id;
        $pk->playerGamemode = Player::getClientFriendlyGamemode($this->gamemode);

        $pk->playerPosition = $this->getOffsetPosition($this);

        $pk->pitch = $this->pitch;
        $pk->yaw = $this->yaw;
        $pk->seed = -1;
        $pk->dimension = $this->level->getDimension();
        $pk->worldGamemode = Player::getClientFriendlyGamemode($this->server->getGamemode());
        $pk->difficulty = $this->level->getDifficulty();
        $pk->spawnX = $spawnPosition->getFloorX();
        $pk->spawnY = $spawnPosition->getFloorY();
        $pk->spawnZ = $spawnPosition->getFloorZ();
        $pk->hasAchievementsDisabled = true;
        $pk->time = $this->level->getTime();
        $pk->eduMode = false;
        $pk->rainLevel = 0;
        $pk->lightningLevel = 0;
        $pk->commandsEnabled = true;
        $pk->levelId = "";
        $pk->worldName = $this->server->getMotd();
        $this->dataPacket($pk);

        $this->level->sendTime($this);
        $this->level->getWeather()->sendWeather($this);

        $this->sendAttributes(true);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->setCanClimb();

        $this->server->getLogger()->info($this->getServer()->getLanguage()->translateString("pocketmine.player.logIn", [
            TextFormat::AQUA . $this->username . TextFormat::WHITE,
            $this->ip,
            $this->port,
            $this->id,
            $this->level->getName(),
            round($this->x, 4),
            round($this->y, 4),
            round($this->z, 4   )
        ]));

        if($this->isOp()){
            $this->setRemoveFormat(false);
        }

        $this->sendCommandData();
        $this->sendSettings();
        $this->sendPotionEffects($this);
        $this->sendData($this);

        $this->inventory->sendContents($this);
        $this->inventory->sendArmorContents($this);
        $this->inventory->sendCreativeContents();
        $this->inventory->sendHeldItem($this);

        $this->server->addOnlinePlayer($this);
        $this->server->onPlayerCompleteLoginSequence($this);
    }

    /**
     * Sends a chat message as this player. If the message begins with a / (forward-slash) it will be treated
     * as a command.
     *
     * @param string $message
     *
     * @return bool
     */
    public function chat(string $message) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return false;
        }

        $this->resetCraftingGridType();

        $message = TextFormat::clean($message, $this->removeFormat);
        foreach(explode("\n", $message) as $messagePart){
            if(trim($messagePart) !== "" and strlen($messagePart) <= 255 and $this->messageCounter-- > 0){
                $this->server->getPluginManager()->callEvent($ev = new PlayerChatEvent($this, $messagePart));
                if(!$ev->isCancelled()){
                    $this->server->broadcastMessage($this->getServer()->getLanguage()->translateString($ev->getFormat(), [$ev->getPlayer()->getDisplayName(), $ev->getMessage()]), $ev->getRecipients());
                }
            }
        }

        return true;
    }

    public function handleMovePlayer(MovePlayerPacket $packet) : bool{
        $newPos = $packet->position->subtract(0, $this->baseOffset, 0);

        if($this->isTeleporting and $newPos->distanceSquared($this) > 1){  //Tolerate up to 1 block to avoid problems with client-sided physics when spawning in blocks
            $this->sendPosition($this, null, null, MovePlayerPacket::MODE_RESET);
            $this->server->getLogger()->debug("Got outdated pre-teleport movement from " . $this->getName() . ", received " . $newPos . ", expected " . $this->asVector3());
            //Still getting movements from before teleport, ignore them
        }elseif((!$this->isAlive() or $this->spawned !== true) and $newPos->distanceSquared($this) > 0.01){
            $this->sendPosition($this, null, null, MovePlayerPacket::MODE_RESET);
            $this->server->getLogger()->debug("Reverted movement of " . $this->getName() . " due to not alive or not spawned, received " . $newPos . ", locked at " . $this->asVector3());
        }else{
            // Once we get a movement within a reasonable distance, treat it as a teleport ACK and remove position lock
            if($this->isTeleporting){
                $this->isTeleporting = false;
            }

            $packet->yaw = fmod($packet->yaw, 360);
            $packet->pitch = fmod($packet->pitch, 360);

            if($packet->yaw < 0){
                $packet->yaw += 360;
            }

            $this->setRotation($packet->yaw, $packet->pitch);
            $this->newPosition = $newPos;
        }

        return true;
    }

    public function handleLevelSoundEvent(LevelSoundEventPacket $packet) : bool{
        //TODO: add events so plugins can change this
        if($this->chunk !== null){
            $this->getLevel()->addChunkPacket($this->chunk->getX(), $this->chunk->getZ(), $packet);
        }
        return true;
    }

    public function handleEntityEvent(EntityEventPacket $packet) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return true;
        }
        $this->resetCraftingGridType();

        switch($packet->event){
            case EntityEventPacket::EATING_ITEM:
                if($packet->data === 0){
                    return false;
                }

                $this->dataPacket($packet);
                $this->server->broadcastPacket($this->getViewers(), $packet);
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * Don't expect much from this handler. Most of it is roughly hacked and duct-taped together.
     *
     * @param InventoryTransactionPacket $packet
     * @return bool
     */
    public function handleInventoryTransaction(InventoryTransactionPacket $packet) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return false;
        }

        if($this->isSpectator()){
            $this->sendAllInventories();
            return true;
        }

        /** @var InventoryAction[] $actions */
        $actions = [];
        foreach($packet->actions as $networkInventoryAction){
            try{
                $action = $networkInventoryAction->createInventoryAction($this);
                if($action !== null){
                    $actions[] = $action;
                }
            }catch(\Throwable $e){
                $this->server->getLogger()->debug("Unhandled inventory action from " . $this->getName() . ": " . $e->getMessage());
                $this->sendAllInventories();
                return false;
            }
        }

        switch($packet->inventoryType){
            case "Crafting":
                if($this->craftingTransaction === null){
                    $this->craftingTransaction = new CraftingTransaction($this, $actions);
                }else{
                    foreach($actions as $action){
                        $this->craftingTransaction->addAction($action);
                    }
                }

                if($this->craftingTransaction->getPrimaryOutput() !== null){
                    //we get the actions for this in several packets, so we can't execute it until we get the result

                    $this->craftingTransaction->execute();
                    $this->craftingTransaction = null;
                }

                return true;
            case "Anvil":
                $anvilTransaction = new AnvilTransaction($this, $actions);
                if(!$anvilTransaction->execute()){
                    $this->sendAllInventories();
                }
                return true;
            default:
                if($this->craftingTransaction !== null){
                    $this->server->getLogger()->debug("Got unexpected normal inventory action with incomplete crafting transaction from " . $this->getName() . ", refusing to execute crafting");
                    $this->craftingTransaction = null;
                }
                break;

        }

        switch($packet->transactionType){
            case InventoryTransactionPacket::TYPE_NORMAL:
                $transaction = new InventoryTransaction($this, $actions);

                if(!$transaction->execute()){
                    $this->server->getLogger()->debug("Failed to execute inventory transaction from " . $this->getName() . " with actions: " . json_encode($packet->actions));

                    return false; //oops!
                }

                return true;
            case InventoryTransactionPacket::TYPE_MISMATCH:
                if(count($packet->actions) > 0){
                    $this->server->getLogger()->debug("Expected 0 actions for mismatch, got " . count($packet->actions) . ", " . json_encode($packet->actions));
                }
                $this->sendAllInventories();

                return true;
            case InventoryTransactionPacket::TYPE_USE_ITEM:
                $blockVector = new Vector3($packet->trData->x, $packet->trData->y, $packet->trData->z);
                $face = $packet->trData->face;

                $type = $packet->trData->actionType;
                switch($type){
                    case InventoryTransactionPacket::USE_ITEM_ACTION_CLICK_BLOCK:
                        $this->setUsingItem(false);

                        if(!$this->canInteract($blockVector->add(0.5, 0.5, 0.5), 13) or $this->isSpectator()){
                        }elseif($this->isCreative()){
                            $item = $this->inventory->getItemInHand();
                            if($this->level->useItemOn($blockVector, $item, $face, $packet->trData->clickPos, $this, true) === true){
                                return true;
                            }
                        }elseif(!$this->inventory->getItemInHand()->equals($packet->trData->itemInHand)){
                            $this->inventory->sendHeldItem($this);
                        }else{
                            $item = $this->inventory->getItemInHand();
                            $oldItem = clone $item;
                            if($this->level->useItemOn($blockVector, $item, $face, $packet->trData->clickPos, $this, true)){
                                if(!$item->equalsExact($oldItem)){
                                    $this->inventory->setItemInHand($item);
                                    $this->inventory->sendHeldItem($this->hasSpawned);
                                }

                                return true;
                            }
                        }

                        $this->inventory->sendHeldItem($this);

                        if($blockVector->distanceSquared($this) > 10000){
                            return true;
                        }

                        $target = $this->level->getBlock($blockVector);
                        $block = $target->getSide($face);

                        /** @var Block[] $blocks */
                        $blocks = array_merge($target->getAllSides(), $block->getAllSides()); //getAllSides() on each of these will include $target and $block because they are next to each other

                        $this->level->sendBlocks([$this], $blocks, UpdateBlockPacket::FLAG_ALL_PRIORITY);

                        return true;
                    case InventoryTransactionPacket::USE_ITEM_ACTION_BREAK_BLOCK:
                        $this->resetCraftingGridType();

                        $item = $this->inventory->getItemInHand();
                        $oldItem = clone $item;

                        if($this->canInteract($blockVector->add(0.5, 0.5, 0.5), $this->isCreative() ? 13 : 7) and $this->level->useBreakOn($blockVector, $item, $this, true)){
                            if($this->isSurvival()){
                                if(!$item->equalsExact($oldItem)){
                                    $this->inventory->setItemInHand($item);
                                    $this->inventory->sendHeldItem($this->hasSpawned);
                                }

                                $this->exhaust(0.025, PlayerExhaustEvent::CAUSE_MINING);
                            }
                            return true;
                        }

                        $this->inventory->sendContents($this);
                        $this->inventory->sendHeldItem($this);

                        $target = $this->level->getBlockAt($blockVector->x, $blockVector->y, $blockVector->z);
                        /** @var Block[] $blocks */
                        $blocks = $target->getAllSides();
                        $blocks[] = $target;

                        $this->level->sendBlocks([$this], $blocks, UpdateBlockPacket::FLAG_ALL_PRIORITY);

                        foreach($blocks as $b){
                            $tile = $this->level->getTile($b);
                            if($tile instanceof Spawnable){
                                $tile->spawnTo($this);
                            }
                        }

                        return true;
                    case InventoryTransactionPacket::USE_ITEM_ACTION_CLICK_AIR:
                        $directionVector = $this->getDirectionVector();

                        if($this->isCreative()){
                            $item = $this->inventory->getItemInHand();
                        }elseif(!$this->inventory->getItemInHand()->equals($packet->trData->itemInHand)){
                            $this->inventory->sendHeldItem($this);
                            return true;
                        }else{
                            $item = $this->inventory->getItemInHand();
                        }

                        $ev = new PlayerInteractEvent($this, $item, null, $directionVector, $face, PlayerInteractEvent::RIGHT_CLICK_AIR);

                        $this->server->getPluginManager()->callEvent($ev);

                        if($ev->isCancelled()){
                            $this->inventory->sendHeldItem($this);
                            return true;
                        }

                        if($item->onClickAir($this, $directionVector) and $this->isSurvival()){
                            $this->inventory->setItemInHand($item);
                        }

                        $this->setUsingItem(true);

                        return true;
                    default:
                        //unknown
                        break;
                }
                break;
            case InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY:
                $target = $this->level->getEntity($packet->trData->entityRuntimeId);
                if($target === null){
                    return false;
                }

                $type = $packet->trData->actionType;

                switch($type){
                    case InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT:
                        $this->server->getPluginManager()->callEvent($ev = new PlayerEntityInteractEvent($this, $target));
                        if(!$ev->isCancelled()){
                            $target->onInteract($this, $this->getItemInHand());
                        }
                        break;
                    case InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_ATTACK:
                        if(!$target->isAlive()){
                            return true;
                        }
                        if($target instanceof DroppedItem or $target instanceof Arrow){
                            $this->kick("Attempting to attack an invalid entity");
                            $this->server->getLogger()->warning($this->getServer()->getLanguage()->translateString("pocketmine.player.invalidEntity", [$this->getName()]));
                            return false;
                        }

                        $cancelled = false;

                        $heldItem = $this->inventory->getItemInHand();

                        if(!$this->canInteract($target, 8)){
                            $cancelled = true;
                        }elseif($target instanceof Player){
                            if($this->server->getConfigBool("pvp") !== true or $this->level->getDifficulty() === Level::DIFFICULTY_PEACEFUL){
                                $cancelled = true;
                            }
                        }

                        $ev = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $heldItem->getAttackPoints());
                        if($cancelled){
                            $ev->setCancelled();
                        }

                        $target->attack($ev);

                        if($ev->isCancelled()){
                            if($heldItem->isTool() and $this->isSurvival()){
                                $this->inventory->sendContents($this);
                            }
                            return true;
                        }

                        if($this->isSurvival()){
                            if($heldItem->useOn($target)){
                                $this->inventory->setItemInHand($heldItem);
                            }

                            $this->exhaust(0.3, PlayerExhaustEvent::CAUSE_ATTACK);
                        }

                        return true;
                    default:
                        break; //unknown
                }

                break;
            case InventoryTransactionPacket::TYPE_RELEASE_ITEM:
                try{
                    $type = $packet->trData->actionType;
                    switch($type){
                        case InventoryTransactionPacket::RELEASE_ITEM_ACTION_RELEASE:
                            if($this->isUsingItem()){
                                $item = $this->inventory->getItemInHand();
                                if($item->onReleaseUsing($this)){
                                    $this->inventory->setItemInHand($item);
                                }
                            }else{
                                break;
                            }

                            return true;
                        case InventoryTransactionPacket::RELEASE_ITEM_ACTION_CONSUME:
                            $slot = $this->inventory->getItemInHand();

                            if($slot instanceof Consumable){
                                if($slot->getId() === Item::BUCKET and $slot->getDamage() != Bucket::TYPE_MILK){
                                    return false;
                                }
                                $ev = new PlayerItemConsumeEvent($this, $slot);
                                $this->server->getPluginManager()->callEvent($ev);

                                if($ev->isCancelled() or !$this->consumeObject($slot)){
                                    $this->inventory->sendContents($this);
                                    return true;
                                }

                                if($this->isSurvival()){
                                    $slot->pop();
                                    $this->inventory->setItemInHand($slot);
                                    $this->inventory->addItem($slot->getResidue());
                                }

                                return true;
                            }

                            return false;
                        default:
                            break;
                    }
                }finally{
                    $this->setUsingItem(false);
                }

                $this->inventory->sendContents($this);
                break;
            default:
                $this->inventory->sendContents($this);
                break;

        }

        return false;
    }

    public function handleMobEquipment(MobEquipmentPacket $packet) : bool{
        if($this->spawned === false or !$this->isAlive()){
            return true;
        }
        $item = $this->inventory->getItem($packet->hotbarSlot);
        if(!$item->equals($packet->item)){
            $this->server->getLogger()->debug("Tried to equip " . $packet->item . " but have " . $item . " in target slot");
            $this->inventory->sendContents($this);
            return false;
        }

        $this->inventory->equipItem($packet->hotbarSlot);

        $this->setUsingItem(false);

        return true;
    }

    public function handleInteract(InteractPacket $packet) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return true;
        }

        $this->resetCraftingGridType();

        $target = $this->level->getEntity($packet->target);
        if($target === null){
            return false;
        }

        switch($packet->action){
            case InteractPacket::ACTION_LEAVE_VEHICLE:
            case InteractPacket::ACTION_MOUSEOVER:
                break;
            default:
                $this->server->getLogger()->debug("Unhandled/unknown interaction type " . $packet->action . "received from " . $this->getName());

                return false;
        }

        return true;
    }

    public function handleBlockPickRequest(BlockPickRequestPacket $packet) : bool{
        $block = $this->level->getBlockAt($packet->blockX, $packet->blockY, $packet->blockZ);

        $item = $block->getPickedItem();

        if($packet->addUserData){
            $tile = $this->getLevel()->getTile($block);
            if($tile instanceof Tile){
                $nbt = $tile->getCleanedNBT();
                if($nbt instanceof CompoundTag){
                    $item->setCustomBlockData($nbt);
                    $item->setLore(["+(DATA)"]);
                }
            }
        }

        $ev = new PlayerBlockPickEvent($this, $block, $item);
        if(!$this->isCreative(true)){
            $this->server->getLogger()->debug("Got block-pick request from " . $this->getName() . " when not in creative mode (gamemode " . $this->getGamemode() . ")");
            $ev->setCancelled();
        }

        $this->server->getPluginManager()->callEvent($ev);
        if(!$ev->isCancelled()){
            $this->inventory->setItemInHand($ev->getResultItem());
        }

        return true;

    }

    public function handlePlayerAction(PlayerActionPacket $packet) : bool{
        if (!$this->spawned or (!$this->isAlive() and $packet->action !== PlayerActionPacket::ACTION_RESPAWN and $packet->action !== PlayerActionPacket::ACTION_DIMENSION_CHANGE_REQUEST)) {
            return true;
        }

        $packet->entityRuntimeId = $this->id;
        $pos = new Vector3($packet->x, $packet->y, $packet->z);

        switch ($packet->action) {
            case PlayerActionPacket::ACTION_START_BREAK:
                if ($pos->distanceSquared($this) > 10000) {
                    break;
                }
                $target = $this->level->getBlockAt($pos->x, $pos->y, $pos->z);
                $ev = new PlayerInteractEvent($this, $this->inventory->getItemInHand(), $target, null, $packet->face, $target->getId() === 0 ? PlayerInteractEvent::LEFT_CLICK_AIR : PlayerInteractEvent::LEFT_CLICK_BLOCK);
                if ($this->level->checkSpawnProtection($this, $target)) {
                    $ev->setCancelled();
                }

                $this->getServer()->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->inventory->sendHeldItem($this);
                    break;
                }
                $block = $target->getSide($packet->face);
                if ($block->getId() === Block::FIRE) {
                    $this->level->setBlock($block, BlockFactory::get(Block::AIR));
                    break;
                }

                if (!$this->isCreative()) {
                    $breakTime = ceil($target->getBreakTime($this->inventory->getItemInHand()) * 20);
                    if ($breakTime > 0) {
                        $this->level->broadcastLevelEvent($pos, LevelEventPacket::EVENT_BLOCK_START_BREAK, (int)(65535 / $breakTime));
                    }
                }
                break;
            case PlayerActionPacket::ACTION_ABORT_BREAK:
            case PlayerActionPacket::ACTION_STOP_BREAK:
                $this->level->broadcastLevelEvent($pos, LevelEventPacket::EVENT_BLOCK_STOP_BREAK);
                break;
            case PlayerActionPacket::ACTION_STOP_SLEEPING:
                $this->stopSleep();
                break;
            case PlayerActionPacket::ACTION_RESPAWN:
                if ($this->spawned === false or $this->isAlive() or !$this->isOnline()) {
                    break;
                }

                if ($this->server->isHardcore()) {
                    $this->setBanned(true);
                    break;
                }

                $this->server->getPluginManager()->callEvent($ev = new PlayerRespawnEvent($this, $this->getSpawn()));

                $realSpawn = $ev->getRespawnPosition()->add(0.5, 0, 0.5);

                if ($realSpawn->distanceSquared($this->getSpawn()->add(0.5, 0, 0.5)) > 0.01) {
                    $this->teleport($realSpawn); //If the destination was modified by plugins
                } else {
                    $this->setPosition($realSpawn); //The client will move to the position of its own accord once chunks are sent
                    $this->nextChunkOrderRun = 0;
                    $this->isTeleporting = true;
                    $this->newPosition = null;
                }

                $this->resetLastMovements();
                $this->resetFallDistance();

                $this->setSprinting(false);
                $this->setSneaking(false);

                $this->extinguish();
                $this->setAirSupplyTicks($this->getMaxAirSupplyTicks());
                $this->deadTicks = 0;
                $this->noDamageTicks = 60;

                $this->removeAllEffects();
                $this->setHealth($this->getMaxHealth());

                /** @var Attribute $attr */
                foreach ($this->attributeMap->getAll() as $attr) {
                    $attr->resetToDefault();
                }

                $this->sendData($this);

                $this->sendSettings();
                $this->inventory->sendContents($this);
                $this->inventory->sendArmorContents($this);

                $this->spawnToAll();
                $this->scheduleUpdate();
                break;
            case PlayerActionPacket::ACTION_JUMP:
                $this->jump();
                return true;
            case PlayerActionPacket::ACTION_START_SPRINT:
                $ev = new PlayerToggleSprintEvent($this, true);
                $this->server->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendData($this);
                } else {
                    $this->setSprinting(true);
                }
                return true;
            case PlayerActionPacket::ACTION_STOP_SPRINT:
                $ev = new PlayerToggleSprintEvent($this, false);
                $this->server->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendData($this);
                } else {
                    $this->setSprinting(false);
                }
                return true;
            case PlayerActionPacket::ACTION_START_SNEAK:
                $ev = new PlayerToggleSneakEvent($this, true);
                $this->server->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendData($this);
                } else {
                    $this->setSneaking(true);
                }
                return true;
            case PlayerActionPacket::ACTION_STOP_SNEAK:
                $ev = new PlayerToggleSneakEvent($this, false);
                $this->server->getPluginManager()->callEvent($ev);
                if ($ev->isCancelled()) {
                    $this->sendData($this);
                } else {
                    $this->setSneaking(false);
                }
                return true;
            case PlayerActionPacket::ACTION_START_GLIDE:
            case PlayerActionPacket::ACTION_STOP_GLIDE:
                $glide = $packet->action == PlayerActionPacket::ACTION_START_GLIDE;
                if ($glide && $this->isHaveElytra()) {
                    $this->elytraIsActivated = true;
                } else {
                    $this->elytraIsActivated = false;
                }
                break;
            case PlayerActionPacket::ACTION_CONTINUE_BREAK:
                $block = $this->level->getBlockAt($pos->x, $pos->y, $pos->z);
                $this->level->broadcastLevelEvent($pos, LevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK, $block->getId() | ($block->getDamage() << 8) | ($packet->face << 16));
                break;
            default:
                $this->server->getLogger()->debug("Unhandled/unknown player action type " . $packet->action . " from " . $this->getName());
                return false;
        }

        $this->setUsingItem(false);

        return true;
    }

    public function handleAnimate(AnimatePacket $packet) : bool{
        if($this->spawned === false or !$this->isAlive()){
            return true;
        }

        $this->server->getPluginManager()->callEvent($ev = new PlayerAnimationEvent($this, $packet->action));
        if($ev->isCancelled()){
            return true;
        }

        $pk = new AnimatePacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->action = $ev->getAnimationType();
        $this->server->broadcastPacket($this->getViewers(), $pk);

        return true;
    }

    /**
     * Drops an item on the ground in front of the player. Returns if the item drop was successful.
     *
     * @param Item $item
     * @return bool if the item was dropped or if the item was null
     */
    public function dropItem(Item $item) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return false;
        }

        if($item->isNull()){
            $this->server->getLogger()->debug($this->getName() . " attempted to drop a null item (" . $item . ")");
            return true;
        }

        $motion = $this->getDirectionVector()->multiply(0.4);

        $this->level->dropItem($this->add(0, 1.3, 0), $item, $motion, 40);

        $this->setUsingItem(false);

        return true;
    }

    public function handleContainerClose(ContainerClosePacket $packet) : bool{
        if(!$this->spawned or $packet->windowId === 0){
            return true;
        }

        $this->resetCraftingGridType();

        if(isset($this->windowIndex[$packet->windowId])){
            $this->server->getPluginManager()->callEvent(new InventoryCloseEvent($this->windowIndex[$packet->windowId], $this));
            $this->removeWindow($this->windowIndex[$packet->windowId]);
            return true;
        }elseif($packet->windowId === 255){
            $this->awardAchievement("openInventory");
            return true;
        }

        return false;
    }

    public function handleAdventureSettings(AdventureSettingsPacket $packet) : bool{
        if($packet->entityUniqueId != $this->getId()){
            return false;
        }

        $handled = false;

        $isFlying = $packet->getPlayerFlag(AdventureSettingsPacket::FLYING);
        if($isFlying and !$this->allowFlight and !$this->server->getAllowFlight()){
            $this->kick($this->server->getLanguage()->translateString("kick.reason.cheat", ["%ability.flight"]));
            return true;
        }elseif($isFlying !== $this->isFlying()){
            $this->server->getPluginManager()->callEvent($ev = new PlayerToggleFlightEvent($this, $isFlying));
            if($ev->isCancelled()){
                $this->sendSettings();
            }else{
                $this->flying = $ev->isFlying();
            }

            $handled = true;
        }

        if($packet->getPlayerFlag(AdventureSettingsPacket::NO_CLIP) and !$this->allowMovementCheats and !$this->isSpectator()){
            $this->kick($this->server->getLanguage()->translateString("kick.reason.cheat", ["%ability.noclip"]));
            return true;
        }

        return $handled;
    }

    public function handleBlockEntityData(BlockEntityDataPacket $packet) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return true;
        }
        $this->resetCraftingGridType();

        $pos = new Vector3($packet->x, $packet->y, $packet->z);
        if($pos->distanceSquared($this) > 10000){
            return true;
        }

        $t = $this->level->getTile($pos);
        if($t instanceof Spawnable){
            $nbt = new NetworkLittleEndianNBTStream();
            $nbt->read($packet->namedtag);
            $nbt = $nbt->getData();
            if(!$t->updateCompoundTag($nbt, $this)){
                $t->spawnTo($this);
            }
        }

        return true;
    }

    public function handleSetPlayerGameType(SetPlayerGameTypePacket $packet) : bool{
        if($packet->gamemode !== $this->gamemode){
            $this->sendGamemode();
            $this->sendSettings();
        }
        return true;
    }

    public function handleRequestChunkRadius(RequestChunkRadiusPacket $packet) : bool{
        $this->setViewDistance($packet->radius);

        return true;
    }

    public function handleItemFrameDropItem(ItemFrameDropItemPacket $packet) : bool{
        if(!$this->spawned or !$this->isAlive()){
            return true;
        }

        $tile = $this->level->getTileAt($packet->x, $packet->y, $packet->z);
        if($tile instanceof ItemFrame){
            $ev = new PlayerInteractEvent($this, $this->inventory->getItemInHand(), $tile->getBlock(), null, 5 - $tile->getBlock()->getDamage(), PlayerInteractEvent::LEFT_CLICK_BLOCK);
            $this->server->getPluginManager()->callEvent($ev);

            if($this->isSpectator()){
                $ev->setCancelled();
            }

            if($ev->isCancelled()){
                $tile->spawnTo($this);
                return true;
            }

            if(lcg_value() <= $tile->getItemDropChance()){
                $this->level->dropItem($tile->getBlock(), $tile->getItem());
            }
            $tile->setItem(null);
            $tile->setItemRotation(0);
        }

        return true;
    }

    public function handleResourcePackChunkRequest(ResourcePackChunkRequestPacket $packet) : bool{
        $manager = $this->server->getResourceManager();
        $pack = $manager->getPackById($packet->packId);
        if(!($pack instanceof ResourcePack)){
            $this->close("", "disconnectionScreen.resourcePack", true);
            $this->server->getLogger()->debug("Got a resource pack chunk request for unknown pack with UUID " . $packet->packId . ", available packs: " . implode(", ", $manager->getPackIdList()));

            return false;
        }

        $pk = new ResourcePackChunkDataPacket();
        $pk->packId = $pack->getPackId();
        $pk->chunkIndex = $packet->chunkIndex;
        $pk->data = $pack->getPackChunk(1048576 * $packet->chunkIndex, 1048576);
        $pk->progress = (1048576 * $packet->chunkIndex);
        $this->dataPacket($pk);
        return true;
    }

    public function handleBookEdit(BookEditPacket $packet) : bool{
        /** @var WritableBook $oldBook */
        $oldBook = $this->inventory->getItem($packet->inventorySlot - 9);
        if($oldBook->getId() !== Item::WRITABLE_BOOK){
            return false;
        }

        $newBook = clone $oldBook;
        $modifiedPages = [];

        switch($packet->type){
            case BookEditPacket::TYPE_REPLACE_PAGE:
                $newBook->setPageText($packet->pageNumber, $packet->text);
                $modifiedPages[] = $packet->pageNumber;
                break;
            case BookEditPacket::TYPE_ADD_PAGE:
                $newBook->insertPage($packet->pageNumber, $packet->text);
                $modifiedPages[] = $packet->pageNumber;
                break;
            case BookEditPacket::TYPE_DELETE_PAGE:
                $newBook->deletePage($packet->pageNumber);
                $modifiedPages[] = $packet->pageNumber;
                break;
            case BookEditPacket::TYPE_SWAP_PAGES:
                $newBook->swapPages($packet->pageNumber, $packet->secondaryPageNumber);
                $modifiedPages = [$packet->pageNumber, $packet->secondaryPageNumber];
                break;
            case BookEditPacket::TYPE_SIGN_BOOK:
                /** @var WrittenBook $newBook */
                $newBook = Item::get(Item::WRITTEN_BOOK, 0, 1, $newBook->getNamedTag());
                $newBook->setAuthor($packet->author);
                $newBook->setTitle($packet->title);
                $newBook->setGeneration(WrittenBook::GENERATION_ORIGINAL);
                break;
            default:
                return false;
        }

        $this->getServer()->getPluginManager()->callEvent($event = new PlayerEditBookEvent($this, $oldBook, $newBook, $packet->type, $modifiedPages));
        if($event->isCancelled()){
            return true;
        }

        $this->getInventory()->setItem($packet->inventorySlot - 9, $event->getNewBook());

        return true;
    }

    /**
     * Handles a Minecraft: Bedrock Edition(BE) packet
     *
     * @param DataPacket $packet
     * @return bool
     */
    public function handleDataPacket(DataPacket $packet) : bool{
        if (!$this->connected) {
            return false;
        }

        $timings = Timings::getReceiveDataPacketTimings($packet);
        $timings->startTiming();

        $packet->decode();
        if(!$packet->feof() and !$packet->mayHaveUnreadBytes()){
            $remains = substr($packet->buffer, $packet->offset);
            $this->server->getLogger()->debug("Still " . strlen($remains) . " bytes unread in " . $packet->getName() . ": 0x" . bin2hex($remains));
        }

        $this->server->getPluginManager()->callEvent($ev = new DataPacketReceiveEvent($this, $packet));
        if ($ev->isCancelled()) {
            $timings->stopTiming();
            return false;
        }

        /**
         * A Basic Handler Without NetworkSession
         */
        $handleName = "handle" . str_ireplace("Packet", "", $packet->getName());

        try {
            $this->{$handleName}($packet);
        } catch (\Exception $e) {
            $timings->stopTiming();
            return false;
        }

        $timings->stopTiming();

        return true;
    }

    /**
     * Batch a Data packet into the channel list to send at the end of the tick
     *
     * @param DataPacket $packet
     *
     * @return bool
     */
    public function batchDataPacket(DataPacket $packet){
        if (!$this->connected) {
            return false;
        }

        $timings = Timings::getSendDataPacketTimings($packet);
        $timings->startTiming();
        $this->server->getPluginManager()->callEvent($ev = new DataPacketSendEvent($this, $packet));
        if ($ev->isCancelled()) {
            $timings->stopTiming();
            return false;
        }

        $this->batchedPackets[] = clone $packet;
        $timings->stopTiming();
        return true;
    }

    /**
     * @param DataPacket $packet
     * @param bool $needACK
     * @param bool $immediate
     * @return bool|int
     */
    public function sendDataPacket(DataPacket $packet, bool $needACK = false, bool $immediate = false){
        if(!$this->connected){
            return false;
        }

        if(!$this->loggedIn and !$packet->canBeSentBeforeLogin()){
            throw new \InvalidArgumentException("Attempted to send " . get_class($packet) . " to " . $this->getName() . " too early");
        }

        $timings = Timings::getSendDataPacketTimings($packet);
        $timings->startTiming();
        $this->server->getPluginManager()->callEvent($ev = new DataPacketSendEvent($this, $packet));
        if($ev->isCancelled()){
            $timings->stopTiming();
            return false;
        }

        $identifier = $this->interface->putPacket($this, $packet, $needACK, $immediate);

        if($needACK and $identifier !== null){
            $this->needACK[$identifier] = false;

            $timings->stopTiming();
            return $identifier;
        }

        $timings->stopTiming();
        return true;
    }

    /**
     * @param DataPacket $packet
     * @param bool	   $needACK
     *
     * @return bool|int
     */
    public function dataPacket(DataPacket $packet, bool $needACK = false){
        return $this->sendDataPacket($packet, $needACK, false);
    }

    /**
     * @param DataPacket $packet
     * @param bool	   $needACK
     *
     * @return bool|int
     */
    public function directDataPacket(DataPacket $packet, bool $needACK = false){
        return $this->sendDataPacket($packet, $needACK, true);
    }

    /**
     * Transfers a player to another server.
     *
     * @param string $address The IP address or hostname of the destination server
     * @param int    $port    The destination port, defaults to 19132
     * @param string $message Message to show in the console when closing the player
     *
     * @return bool if transfer was successful.
     */
    public function transfer(string $address, int $port = 19132, string $message = "transfer") : bool{
        $this->server->getPluginManager()->callEvent($ev = new PlayerTransferEvent($this, $address, $port, $message));

        if(!$ev->isCancelled()){
            $pk = new TransferPacket();
            $pk->address = $ev->getAddress();
            $pk->port = $ev->getPort();
            $this->directDataPacket($pk);
            $this->close("", $ev->getMessage(), false);

            return true;
        }

        return false;
    }

    /**
     * Kicks a player from the server
     *
     * @param string $reason
     * @param bool $isAdmin
     *
     * @return bool
     */
    public function kick(string $reason = "", bool $isAdmin = true){
        $this->server->getPluginManager()->callEvent($ev = new PlayerKickEvent($this, $reason, $this->getLeaveMessage()));
        if (!$ev->isCancelled()) {
            if ($isAdmin) {
                $message = "Kicked by admin." . ($reason !== "" ? " Reason: " . $reason : "");
            } else {
                if ($reason === "") {
                    $message = "disconnectionScreen.noReason";
                } else {
                    $message = $reason;
                }
            }
            $this->close($ev->getQuitMessage(), $message);

            return true;
        }

        return false;
    }

    /**
     * Adds a title text to the user's screen, with an optional subtitle.
     *
     * @param string $title
     * @param string $subtitle
     * @param int    $fadeIn Duration in ticks for fade-in. If -1 is given, client-sided defaults will be used.
     * @param int    $stay Duration in ticks to stay on screen for
     * @param int    $fadeOut Duration in ticks for fade-out.
     */
    public function addTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1){
        $this->setTitleDuration($fadeIn, $stay, $fadeOut);
        if($subtitle !== ""){
            $this->addSubTitle($subtitle);
        }
        $this->sendTitleText($title, SetTitlePacket::TYPE_SET_TITLE);
    }

    /**
     * Sets the subtitle message, without sending a title.
     *
     * @param string $subtitle
     */
    public function addSubTitle(string $subtitle){
        $this->sendTitleText($subtitle, SetTitlePacket::TYPE_SET_SUBTITLE);
    }

    /**
     * Adds small text to the user's screen.
     *
     * @param string $message
     */
    public function addActionBarMessage(string $message){
        $this->sendTitleText($message, SetTitlePacket::TYPE_SET_ACTIONBAR_MESSAGE);
    }

    /**
     * Removes the title from the client's screen.
     */
    public function removeTitles(){
        $pk = new SetTitlePacket();
        $pk->type = SetTitlePacket::TYPE_CLEAR_TITLE;
        $this->dataPacket($pk);
    }

    /**
     * Resets the title duration settings.
     */
    public function resetTitles(){
        $pk = new SetTitlePacket();
        $pk->type = SetTitlePacket::TYPE_RESET_TITLE;
        $this->dataPacket($pk);
    }

    /**
     * Sets the title duration.
     *
     * @param int $fadeIn Title fade-in time in ticks.
     * @param int $stay Title stay time in ticks.
     * @param int $fadeOut Title fade-out time in ticks.
     */
    public function setTitleDuration(int $fadeIn, int $stay, int $fadeOut){
        if($fadeIn >= 0 and $stay >= 0 and $fadeOut >= 0){
            $pk = new SetTitlePacket();
            $pk->type = SetTitlePacket::TYPE_SET_ANIMATION_TIMES;
            $pk->fadeInTime = $fadeIn;
            $pk->stayTime = $stay;
            $pk->fadeOutTime = $fadeOut;
            $this->dataPacket($pk);
        }
    }

    /**
     * Internal function used for sending titles.
     *
     * @param string $title
     * @param int $type
     */
    protected function sendTitleText(string $title, int $type){
        $pk = new SetTitlePacket();
        $pk->type = $type;
        $pk->text = $title;
        $this->dataPacket($pk);
    }

    /**
     * Sends a direct chat message to a player
     *
     * @param TextContainer|string $message
     */
    public function sendMessage($message){
        if($message instanceof TextContainer){
            if($message instanceof TranslationContainer){
                $this->sendTranslation($message->getText(), $message->getParameters());
                return;
            }
            $message = $message->getText();
        }

        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_RAW;
        $pk->message = $this->server->getLanguage()->translateString($message);
        $this->dataPacket($pk);
    }

    /**
     * @param string   $message
     * @param string[] $parameters
     */
    public function sendTranslation(string $message, array $parameters = []){
        $pk = new TextPacket();
        if(!$this->server->isLanguageForced()){
            $pk->type = TextPacket::TYPE_TRANSLATION;
            $pk->message = $this->server->getLanguage()->translateString($message, $parameters, "pocketmine.");
            foreach($parameters as $i => $p){
                $parameters[$i] = $this->server->getLanguage()->translateString($p, $parameters, "pocketmine.");
            }
            $pk->parameters = $parameters;
        }else{
            $pk->type = TextPacket::TYPE_RAW;
            $pk->message = $this->server->getLanguage()->translateString($message, $parameters);
        }
        $this->dataPacket($pk);
    }

    /**
     * Sends a popup message to the player
     *
     * TODO: add translation type popups
     *
     * @param string $message
     * @param string $subtitle @deprecated
     */
    public function sendPopup(string $message, string $subtitle = ""){
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_POPUP;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    public function sendTip(string $message){
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_TIP;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    /**
     * @param string $sender
     * @param string $message
     */
    public function sendWhisper(string $sender, string $message){
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_WHISPER;
        $pk->source = $sender;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    /**
     * Note for plugin developers: use kick() with the isAdmin
     * flag set to kick without the "Kicked by admin" part instead of this method.
     *
     * @param string $message Message to be broadcasted
     * @param string $reason Reason showed in console
     * @param bool $notify
     */
    final public function close($message = "", string $reason = "generic reason", bool $notify = true){
        if ($this->connected and !$this->closed) {

            try{
                if ($notify and strlen($reason) > 0) {
                    $pk = new DisconnectPacket();
                    $pk->hideDisconnectionScreen = false;
                    $pk->message = $reason;
                    $this->directDataPacket($pk);
                }

                if ($this->fishingHook instanceof FishingHook) {
                    $this->fishingHook->close();
                    $this->fishingHook = null;
                }

                $this->connected = false;

                $this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_USERS, $this);
                $this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);

                $this->stopSleep();

                if($this->spawned){
                    $this->server->getPluginManager()->callEvent($ev = new PlayerQuitEvent($this, $message, true));
                    if($ev->getQuitMessage() != ""){
                        $this->server->broadcastMessage($ev->getQuitMessage());
                    }

                    if ($this->server->getAutoSave()) {
                        try{
                            $this->save();
                        }catch(\Throwable $e){
                            $this->server->getLogger()->critical("Failed to save player data for " . $this->getName());
                            $this->server->getLogger()->logException($e);
                        }
                    }
                }

                if($this->isValid()){
                    foreach($this->usedChunks as $index => $d){
                        Level::getXZ($index, $chunkX, $chunkZ);
                        $this->level->unregisterChunkLoader($this, $chunkX, $chunkZ);
                        foreach($this->level->getChunkEntities($chunkX, $chunkZ) as $entity){
                            $entity->despawnFrom($this);
                        }
                        unset($this->usedChunks[$index]);
                    }
                }
                $this->usedChunks = [];
                $this->loadQueue = [];

                if($this->loggedIn){
                    $this->server->onPlayerLogout($this);
                    foreach($this->server->getOnlinePlayers() as $player){
                        if(!$player->canSee($this)){
                            $player->showPlayer($this);
                        }
                    }
                    $this->hiddenPlayers = [];
                }

                $this->removeAllWindows(true);
                $this->windows = null;
                $this->windowIndex = [];
                $this->cursorInventory = null;
                $this->craftingGrid = null;

                if($this->constructed){
                    parent::close();
                }
                $this->spawned = false;

                if($this->loggedIn){
                    $this->loggedIn = false;
                    $this->server->removeOnlinePlayer($this);
                }

                $this->server->getLogger()->info($this->getServer()->getLanguage()->translateString("pocketmine.player.logOut", [
                    TextFormat::AQUA . $this->getName() . TextFormat::WHITE,
                    $this->ip,
                    $this->port,
                    $this->getServer()->getLanguage()->translateString($reason)
                ]));

                $this->spawnPosition = null;

                if($this->perm !== null){
                    $this->perm->clearPermissions();
                    $this->perm = null;
                }

                if ($this->server->dserverConfig["enable"] and $this->server->dserverConfig["queryAutoUpdate"]) $this->server->updateQuery();
            }catch(\Throwable $e){
                $this->server->getLogger()->logException($e);
            }finally{
                $this->interface->close($this, $notify ? $reason : "");
                $this->server->removePlayer($this);
            }
        }
    }

    /**
     * @return array
     */
    public function __debugInfo(){
        return [];
    }

    public function canSaveWithChunk(): bool{
        return false;
    }

    public function setCanSaveWithChunk(bool $value){
        throw new \BadMethodCallException("Players can't be saved with chunks");
    }

    /**
     * Handles player data saving
     *
     * @param bool $async
     */
    public function save(bool $async = false){
        if ($this->closed) {
            throw new \InvalidStateException("Tried to save closed player");
        }

        parent::saveNBT();

        if($this->isValid()){
            $this->namedtag->setString("Level", $this->level->getFolderName());
        }

        if($this->hasValidSpawnPosition()){
            $this->namedtag->setString("SpawnLevel", $this->spawnPosition->getLevel()->getFolderName());
            $this->namedtag->setInt("SpawnX", (int) $this->spawnPosition->x);
            $this->namedtag->setInt("SpawnY", (int) $this->spawnPosition->y);
            $this->namedtag->setInt("SpawnZ", (int) $this->spawnPosition->z);
        }

        $achievements = new CompoundTag("Achievements");
        foreach($this->achievements as $achievement => $status){
            $achievements->setByte($achievement, $status === true ? 1 : 0);
        }
        $this->namedtag->setTag($achievements);

        $this->namedtag->setInt("playerGameType", $this->gamemode);
        $this->namedtag->setLong("lastPlayed", (int) floor(microtime(true) * 1000));

        if($this->username != "" and $this->namedtag instanceof CompoundTag){
            $this->server->saveOfflinePlayerData($this->username, $this->namedtag, $async);
        }
    }

    public function kill(){
        if(!$this->spawned){
            return;
        }

        parent::kill();

        $this->sendRespawnPacket($this->getSpawn());
    }

    protected function onDeath(){
        $message = "death.attack.generic";

        $params = [
            $this->getDisplayName()
        ];

        $cause = $this->getLastDamageCause();

        switch($cause === null ? EntityDamageEvent::CAUSE_CUSTOM : $cause->getCause()){
            case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                if($cause instanceof EntityDamageByEntityEvent){
                    $e = $cause->getDamager();
                    if($e instanceof Player){
                        $message = "death.attack.player";
                        $params[] = $e->getDisplayName();
                        break;
                    }elseif($e instanceof Living){
                        $message = "death.attack.mob";
                        $params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
                        break;
                    }else{
                        $params[] = "Unknown";
                    }
                }
                break;
            case EntityDamageEvent::CAUSE_PROJECTILE:
                if($cause instanceof EntityDamageByEntityEvent){
                    $e = $cause->getDamager();
                    if($e instanceof Player){
                        $message = "death.attack.arrow";
                        $params[] = $e->getDisplayName();
                    }elseif($e instanceof Living){
                        $message = "death.attack.arrow";
                        $params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
                        break;
                    }else{
                        $params[] = "Unknown";
                    }
                }
                break;
            case EntityDamageEvent::CAUSE_SUICIDE:
                $message = "death.attack.generic";
                break;
            case EntityDamageEvent::CAUSE_VOID:
                $message = "death.attack.outOfWorld";
                break;
            case EntityDamageEvent::CAUSE_FALL:
                if($cause instanceof EntityDamageEvent){
                    if($cause->getFinalDamage() > 2){
                        $message = "death.fell.accident.generic";
                        break;
                    }
                }
                $message = "death.attack.fall";
                break;

            case EntityDamageEvent::CAUSE_SUFFOCATION:
                $message = "death.attack.inWall";
                break;

            case EntityDamageEvent::CAUSE_LAVA:
                $message = "death.attack.lava";
                break;

            case EntityDamageEvent::CAUSE_FIRE:
                $message = "death.attack.onFire";
                break;

            case EntityDamageEvent::CAUSE_FIRE_TICK:
                $message = "death.attack.inFire";
                break;

            case EntityDamageEvent::CAUSE_DROWNING:
                $message = "death.attack.drown";
                break;

            case EntityDamageEvent::CAUSE_CONTACT:
                if($cause instanceof EntityDamageByBlockEvent){
                    if($cause->getDamager()->getId() === Block::CACTUS){
                        $message = "death.attack.cactus";
                    }
                }
                break;

            case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
            case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
                if($cause instanceof EntityDamageByEntityEvent){
                    $e = $cause->getDamager();
                    if($e instanceof Player){
                        $message = "death.attack.explosion.player";
                        $params[] = $e->getDisplayName();
                    }elseif($e instanceof Living){
                        $message = "death.attack.explosion.player";
                        $params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
                        break;
                    }
                }else{
                    $message = "death.attack.explosion";
                }
                break;

            case EntityDamageEvent::CAUSE_MAGIC:
                $message = "death.attack.magic";
                break;

            case EntityDamageEvent::CAUSE_CUSTOM:
                break;

            default:
                break;
        }

        //Crafting grid must always be evacuated even if keep-inventory is true. This dumps the contents into the
        //main inventory and drops the rest on the ground.
        $this->resetCraftingGridType();

        $ev = new PlayerDeathEvent($this, $this->getDrops(), new TranslationContainer($message, $params));
        $ev->setKeepInventory($this->server->keepInventory);
        $ev->setKeepExperience($this->server->keepExperience);
        $this->server->getPluginManager()->callEvent($ev);

        if (!$ev->getKeepInventory()) {
            foreach ($ev->getDrops() as $item) {
                $this->level->dropItem($this, $item);
            }

            if ($this->inventory !== null) {
                $this->inventory->setHeldItemIndex(0, false);
                $this->inventory->clearAll();
            }
        }

        if ($this->server->expEnabled and !$ev->getKeepExperience()){
            $this->setLifetimeTotalXp(0);
        }

        if ($ev->getDeathMessage() != "") {
            $this->server->broadcast($ev->getDeathMessage(), Server::BROADCAST_CHANNEL_USERS);
        }
    }

    protected function onDeathUpdate(int $tickDiff) : bool{
        if(parent::onDeathUpdate($tickDiff)){
            $this->despawnFromAll(); //non-player entities rely on close() to do this for them
        }

        return false; //never flag players for despawn
    }

    public function getArmorPoints() : int{
        $total = 0;
        foreach($this->inventory->getArmorContents() as $item){
            $total += $item->getDefensePoints();
        }

        return $total;
    }

    protected function applyPostDamageEffects(EntityDamageEvent $source){
        parent::applyPostDamageEffects($source);

        $this->exhaust(0.3, PlayerExhaustEvent::CAUSE_DAMAGE);
    }

    public function attack(EntityDamageEvent $source){
        if(!$this->isAlive()){
            return;
        }

        if($this->isCreative()
            and $source->getCause() !== EntityDamageEvent::CAUSE_SUICIDE
            and $source->getCause() !== EntityDamageEvent::CAUSE_VOID
        ){
            $source->setCancelled();
        }elseif($this->allowFlight and $source->getCause() === EntityDamageEvent::CAUSE_FALL){
            $source->setCancelled();
        }

        parent::attack($source);
    }

    protected function doHitAnimation(){
        parent::doHitAnimation();
        if($this->spawned){
            $this->broadcastEntityEvent(EntityEventPacket::HURT_ANIMATION, null, [$this]);
        }
    }

    public function getOffsetPosition(Vector3 $vector3) : Vector3{
        $result = parent::getOffsetPosition($vector3);
        $result->y += 0.001; //Hack for MCPE falling underground for no good reason (TODO: find out why it's doing this)
        return $result;
    }

    public function sendPosition(Vector3 $pos, float $yaw = null, float $pitch = null, int $mode = MovePlayerPacket::MODE_NORMAL, array $targets = null){
        $yaw = $yaw ?? $this->yaw;
        $pitch = $pitch ?? $this->pitch;

        $pk = new MovePlayerPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->position = $this->getOffsetPosition($pos);
        $pk->pitch = $pitch;
        $pk->headYaw = $yaw;
        $pk->yaw = $yaw;
        $pk->mode = $mode;

        if($targets !== null){
            $this->server->broadcastPacket($targets, $pk);
        }else{
            $this->dataPacket($pk);
        }

        $this->newPosition = null;
    }

    /**
     * {@inheritdoc}
     */
    public function teleport(Vector3 $pos, float $yaw = null, float $pitch = null) : bool{
        if(parent::teleport($pos, $yaw, $pitch)){

            $this->removeAllWindows();

            $this->sendPosition($this, $this->yaw, $this->pitch, MovePlayerPacket::MODE_TELEPORT);
            $this->sendPosition($this, $this->yaw, $this->pitch, MovePlayerPacket::MODE_TELEPORT, $this->getViewers());

            $this->spawnToAll();

            $this->resetFallDistance();
            $this->nextChunkOrderRun = 0;
            $this->newPosition = null;
            $this->stopSleep();

            $this->isTeleporting = true;

            return true;
        }

        return false;
    }

    /**
     * @deprecated This functionality is now performed in {@link Player#teleport}.
     *
     * @param Vector3    $pos
     * @param float|null $yaw
     * @param float|null $pitch
     *
     * @return bool
     */
    public function teleportImmediate(Vector3 $pos, float $yaw = null, float $pitch = null) : bool{
        return $this->teleport($pos, $yaw, $pitch);
    }

    protected function addDefaultWindows(){
        $this->addWindow($this->getInventory(), ContainerIds::INVENTORY, true);

        $this->cursorInventory = new PlayerCursorInventory($this);
        $this->addWindow($this->cursorInventory, ContainerIds::CURSOR, true);

        $this->craftingGrid = new CraftingGrid($this);

        //TODO: more windows
    }

    public function getCursorInventory() : PlayerCursorInventory{
        return $this->cursorInventory;
    }

    public function getCraftingGrid() : CraftingGrid{
        return $this->craftingGrid;
    }

    /**
     * @param CraftingGrid $grid
     */
    public function setCraftingGrid(CraftingGrid $grid){
        $this->craftingGrid = $grid;
    }

    public function resetCraftingGridType(){
        $contents = $this->craftingGrid->getContents();
        if(count($contents) > 0){
            $drops = $this->inventory->addItem(...$contents);
            foreach($drops as $drop){
                $this->dropItem($drop);
            }

            $this->craftingGrid->clearAll();
        }

        if($this->craftingGrid instanceof BigCraftingGrid){
            $this->craftingGrid = new CraftingGrid($this);
        }
    }

    /**
     * Returns the window ID which the inventory has for this player, or -1 if the window is not open to the player.
     *
     * @param Inventory $inventory
     *
     * @return int
     */
    public function getWindowId(Inventory $inventory) : int{
        if($this->windows->contains($inventory)){
            /** @var int $id */
            $id = $this->windows[$inventory];
            return $id;
        }

        return ContainerIds::NONE;
    }

    /**
     * Returns the inventory window open to the player with the specified window ID, or null if no window is open with
     * that ID.
     *
     * @param int $windowId
     *
     * @return Inventory|null
     */
    public function getWindow(int $windowId){
        return $this->windowIndex[$windowId] ?? null;
    }

    /**
     * Opens an inventory window to the player. Returns the ID of the created window, or the existing window ID if the
     * player is already viewing the specified inventory.
     *
     * @param Inventory $inventory
     * @param int|null  $forceId Forces a special ID for the window
     * @param bool      $isPermanent Prevents the window being removed if true.
     *
     * @return int
     */
    public function addWindow(Inventory $inventory, int $forceId = null, bool $isPermanent = false) : int{
        if(($id = $this->getWindowId($inventory)) !== ContainerIds::NONE){
            return $id;
        }

        if($forceId === null){
            $this->windowCnt = $cnt = max(ContainerIds::FIRST, ++$this->windowCnt % ContainerIds::LAST);
        }else{
            $cnt = $forceId;
        }
        $this->windowIndex[$cnt] = $inventory;
        $this->windows->attach($inventory, $cnt);
        if($inventory->open($this)){
            if($isPermanent){
                $this->permanentWindows[$cnt] = true;
            }
            return $cnt;
        }else{
            $this->removeWindow($inventory);

            return -1;
        }
    }

    /**
     * Removes an inventory window from the player.
     *
     * @param Inventory $inventory
     * @param bool      $force Forces removal of permanent windows such as normal inventory, cursor
     *
     * @throws \BadMethodCallException if trying to remove a fixed inventory window without the `force` parameter as true
     */
    public function removeWindow(Inventory $inventory, bool $force = false){
        if($this->windows->contains($inventory)){
            /** @var int $id */
            $id = $this->windows[$inventory];
            if(!$force and isset($this->permanentWindows[$id])){
                throw new \BadMethodCallException("Cannot remove fixed window $id (" . get_class($inventory) . ") from " . $this->getName());
            }
            $this->windows->detach($this->windowIndex[$id]);
            unset($this->windowIndex[$id]);
            unset($this->permanentWindows[$id]);
        }

        $inventory->close($this);
    }

    /**
     * Removes all inventory windows from the player. By default this WILL NOT remove permanent windows.
     *
     * @param bool $removePermanentWindows Whether to remove permanent windows.
     */
    public function removeAllWindows(bool $removePermanentWindows = false){
        foreach($this->windowIndex as $id => $window){
            if(!$removePermanentWindows and isset($this->permanentWindows[$id])){
                continue;
            }

            $this->removeWindow($window, $removePermanentWindows);
        }
    }

    protected function sendAllInventories(){
        foreach($this->windowIndex as $id => $inventory){
            $inventory->sendContents($this);
            if($inventory instanceof PlayerInventory){
                $inventory->sendArmorContents($this);
            }
        }
    }

    /**
     * @param string $metadataKey
     * @param MetadataValue $newMetadataValue
     * @throws \Exception
     */
    public function setMetadata(string $metadataKey, MetadataValue $newMetadataValue){
        $this->server->getPlayerMetadata()->setMetadata($this, $metadataKey, $newMetadataValue);
    }

    /**
     * @param string $metadataKey
     * @return MetadataValue[]
     * @throws \Exception
     */
    public function getMetadata(string $metadataKey){
        return $this->server->getPlayerMetadata()->getMetadata($this, $metadataKey);
    }

    /**
     * @param string $metadataKey
     * @return bool
     * @throws \Exception
     */
    public function hasMetadata(string $metadataKey) : bool{
        return $this->server->getPlayerMetadata()->hasMetadata($this, $metadataKey);
    }

    /**
     * @param string $metadataKey
     * @param Plugin $owningPlugin
     * @throws \Exception
     */
    public function removeMetadata(string $metadataKey, Plugin $owningPlugin){
        $this->server->getPlayerMetadata()->removeMetadata($this, $metadataKey, $owningPlugin);
    }

    public function onChunkChanged(Chunk $chunk){
        if(isset($this->usedChunks[$hash = Level::chunkHash($chunk->getX(), $chunk->getZ())])){
            $this->usedChunks[$hash] = false;
            if(!$this->spawned){
                $this->nextChunkOrderRun = 0;
            }
        }
    }

    public function onChunkLoaded(Chunk $chunk){

    }

    public function onChunkPopulated(Chunk $chunk){

    }

    public function onChunkUnloaded(Chunk $chunk){

    }

    public function onBlockChanged(Vector3 $block){

    }

    public function getLoaderId() : int{
        return $this->loaderId;
    }

    public function isLoaderActive() : bool{
        return $this->isConnected();
    }

    // TURANIC

	/**
	 * @return mixed
	 */
	public function getProtocol(){
		return $this->protocol;
	}

	public function handleCommandRequest(CommandRequestPacket $packet) : bool{
		$cmd = $packet->command;
		if($cmd{0} != '/'){
			return false;
		}
		$line = substr($cmd, 1);
		$this->server->getPluginManager()->callEvent($event = new PlayerCommandPreprocessEvent($this, $cmd));
		if($event->isCancelled()){
			return false;
		}

		$this->server->dispatchCommand($this, $line);

		return true;
	}

	public function handlePlayerHotbar(PlayerHotbarPacket $packet) : bool{
	    return true;
    }

    public function handleEntityFall(EntityFallPacket $packet) : bool{
        return true;
    }

	public function handleText(TextPacket $packet) : bool{
		if($packet->type == TextPacket::TYPE_CHAT){
			return $this->chat($packet->message);
		}
		return false;
	}

    public function handleEntityPickRequest(EntityPickRequestPacket $packet) : bool{
        return true;
    }

	public function handleCraftingEvent(CraftingEventPacket $packet) : bool{
		return true;
	}

	public function handlePlayerSkin(PlayerSkinPacket $packet) : bool{
		return $this->changeSkin($packet->skin, $packet->newSkinName, $packet->oldSkinName);
	}

	public function handlePing(PingPacket $packet) : bool{
		// TODO: Add event
		$this->updatePing($packet->ping);
		return true;
	}

	public function handleModalFormResponse(ModalFormResponsePacket $packet) : bool{
		$id = $packet->formId;
		$data = json_decode($packet->formData, true);

		if(isset($this->modalForms[$id])){
			$form = $this->modalForms[$id];
			if($data === null){
				$form->onClose($this);

				$this->server->getPluginManager()->callEvent($ev = new FormCloseEvent($this, $form));
				if($ev->isCancelled()){
					$this->sendForm($ev->getForm(), $id);
					return false;
				}
			}else{
               $handleData = $form->handleResponse($data, $this);
               $this->server->getPluginManager()->callEvent($ev = new FormDataReceiveEvent($this, $form, $handleData));
			   if($ev->isCancelled()){
				   $this->sendForm($ev->getForm(), $id);
				   return false;
			   }
			}

			unset($this->modalForms[$id]);

			return true;
		}
		return false;
	}

    /**
     * @param ServerSettingsRequestPacket $packet
     * @return bool
     */
    public function handleServerSettingsRequest(ServerSettingsRequestPacket $packet) : bool{
        if ($this->server->getAdvancedProperty("server.show-turanic", true)) {
            $this->sendServerSettings($this->getDefaultServerSettings());
        }
        return true;
    }

    public function handleSpawnExperienceOrb(SpawnExperienceOrbPacket $packet): bool{
        return false;
    }

	public function handleBatch(BatchPacket $packet) : bool{
        if($packet->payload === ""){
            return false;
        }

		foreach($packet->getPackets() as $buf){
			$pk = $this->server->getNetwork()->getPacketById(ord($buf{0}));

            if(!$pk->canBeBatched()){
                throw new \InvalidArgumentException("Received invalid " . get_class($pk) . " inside BatchPacket");
            }

            $pk->setBuffer($buf, 1);
            $this->handleDataPacket($pk);
		}
		return true;
	}

    public function handleBossEvent(BossEventPacket $packet): bool{
	    return false;
    }

	/**
	 * Adds a title text to the user's screen, with an optional subtitle.
	 *
	 * @param string $title
	 * @param string $subtitle
	 * @param int $fadeIn Duration in ticks for fade-in. If -1 is given, client-sided defaults will be used.
	 * @param int $stay Duration in ticks to stay on screen for
	 * @param int $fadeOut Duration in ticks for fade-out.
	 */
	public function sendActionBar(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1){
		$this->setTitleDuration($fadeIn, $stay, $fadeOut);
		if ($subtitle !== "") {
			$this->sendTitleText($subtitle, SetTitlePacket::TYPE_SET_SUBTITLE);
		}
		$this->sendTitleText($title, SetTitlePacket::TYPE_SET_TITLE);
	}

	/**
     * Default Amount is 0
	 * Change Player Movement Speed without effects
	 *
	 * @param $amount
	 */
	public function setMovementSpeed($amount){
		if($this->spawned === true){
			$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue($amount, true);
		}
	}

    /**
    * @return float
    */
    public function getMovementSpeed(){
        return $this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->getValue();
    }


	/**
	 * Send a title text or/and with/without a sub title text to a player
	 *
	 * @param		$title
	 * @param string $subtitle
	 * @param int $fadein
	 * @param int $fadeout
	 * @param int $duration
	 *
	 * @return bool
	 */
	public function sendTitle($title, $subtitle = "", $fadein = 20, $fadeout = 20, $duration = 5){
		return $this->addTitle($title, $subtitle, $fadein, $duration, $fadeout);
	}

	protected function checkChunks(){
		if ($this->chunk === null or ($this->chunk->getX() !== ($this->x >> 4) or $this->chunk->getZ() !== ($this->z >> 4))) {
			if ($this->chunk !== null) {
				$this->chunk->removeEntity($this);
			}
			$this->chunk = $this->level->getChunk($this->x >> 4, $this->z >> 4, true);

			if (!$this->justCreated) {
				$newChunk = $this->level->getChunkPlayers($this->x >> 4, $this->z >> 4);
				unset($newChunk[$this->getLoaderId()]);

				/** @var Player[] $reload */
				$reload = [];
				foreach ($this->hasSpawned as $player) {
					if (!isset($newChunk[$player->getLoaderId()])) {
						$this->despawnFrom($player);
					} else {
						unset($newChunk[$player->getLoaderId()]);
						$reload[] = $player;
					}
				}

				foreach ($newChunk as $player) {
					$this->spawnTo($player);
				}
			}

			if ($this->chunk === null) {
				return;
			}

			$this->chunk->addEntity($this);
		}
	}

	/**
	 * @param Effect $effect
	 *
	 * @return bool
	 * @internal param $Effect
	 */
	public function addEffect(Effect $effect): bool{//Overwrite
		if ($effect->isBad() && $this->isCreative()) {
			return false;
		}

		return parent::addEffect($effect);
	}

	public function sendForm(Form $form, int $forceId = -1){
		if($forceId < 0){
			$forceId = $this->modalFormCnt++;
		}
		$pk = new ModalFormRequestPacket;
		$pk->formId = $forceId;
		$pk->formData = json_encode($form);
		$this->dataPacket($pk);
		$form->setId($forceId);
		$this->modalForms[$forceId] = $form;
	}

	public function sendServerSettings(Form $form){
		$pk = new ServerSettingsResponsePacket;
		$pk->formId = $id = $this->modalFormCnt++;
		$pk->formData = json_encode($form);
		$form->setId($id);
		$this->dataPacket($pk);
		$this->modalForms[$id] = $form;
	}

	public function getDefaultServerSettings() : Form{
		return $this->defaultServerSettings;
	}

	public function setDefaultServerSettings(Form $form){
		$this->defaultServerSettings = $form;
	}

	public function getForm(int $id){
		return $this->modalForms[$id] ?? null;
	}

	public function getForms() : array{
		return $this->modalForms;
	}

    /**
     * @return bool
     */
    public function isTeleporting() : bool{
		return $this->isTeleporting;
	}

    public function handleCommandBlockUpdate(CommandBlockUpdatePacket $packet) : bool{
        if(!$this->isOp() or !$this->isCreative()){
            return false;
        }
        // TODO : Control
        if($packet->isBlock){
            $block = $this->level->getBlockAt($packet->x, $packet->y, $packet->z);
            if($block instanceof CommandBlock){
                $tile = $this->level->getTile($block);
                if(!$tile instanceof TileCommandBlock) return false;
                $replace = BlockFactory::get($tile->getIdByBlockType($packet->commandBlockMode), $block->getDamage());
                if($packet->isConditional){
                    if($replace->getDamage() < 8){
                        $replace->setDamage($replace->getDamage() + 8);
                    }
                }else{
                    if($replace->getDamage() > 8){
                        $replace->setDamage($replace->getDamage() - 8);
                    }
                }
                $this->level->setBlock($block, $replace, false, false);
                $tile->setName($packet->name);
                $tile->setBlockType($packet->commandBlockMode);
                $tile->setCommand($packet->command);
                $tile->setLastOutput($packet->lastOutput);
                $tile->setTrackOutput($packet->shouldTrackOutput);
                $tile->setAuto(!$packet->isRedstoneMode);
                $tile->setConditional($packet->isConditional);
                $tile->spawnToAll();
            }
        }else{
            // Minecart
        }
        return true;
    }

    /**
     * @param string $name
     * @param array $inventory
     * @param CompoundTag|null $nbt
     * @return bool
     */
    public function addVirtualInventory(string $name = "Turanic Virtual Inventory", array $inventory = [], CompoundTag $nbt = null) {
	    if($nbt == null){
	        if($this->y - 2 <= 0){
	            return false;
            }
	        $nbt = VirtualHolder::createNBT($this->subtract(0, 2, 0));
        }
	    $tile = Tile::createTile(Tile::VIRTUAL_HOLDER, $this->level, $nbt);
	    if($tile instanceof VirtualHolder){
	        $tile->setName($name);
	        $inv = $tile->getInventory();
	        $inv->setContents($inventory);
	        $tile->spawnTo($this);
	        $this->addWindow($inv);
	        return true;
        }
        return false;
    }

    /**
     * @param FishingHook $entity
     *
     * @return bool
     */
	public function linkHookToPlayer(FishingHook $entity){
        if ($entity->isAlive()) {
            $this->setFishingHook($entity);
            $pk = new EntityEventPacket();
            $pk->entityRuntimeId = $this->getFishingHook()->getId();
            $pk->event = EntityEventPacket::FISH_HOOK_POSITION;
            $this->server->broadcastPacket($this->level->getPlayers(), $pk);
            return true;
        }
        return false;
    }

	/**
     * @return bool
     */
	public function unlinkHookFromPlayer(){
        if ($this->fishingHook instanceof FishingHook) {
            $pk = new EntityEventPacket();
            $pk->entityRuntimeId = $this->fishingHook->getId();
            $pk->event = EntityEventPacket::FISH_HOOK_TEASE;
            $this->server->broadcastPacket($this->level->getPlayers(), $pk);
            $this->setFishingHook();
            return true;
        }
        return false;
    }

	/**
     * @param string $text
     */
	public function setButtonText(string $text){
        $this->setDataProperty(self::DATA_INTERACTIVE_TAG, self::DATA_TYPE_STRING, $text);
    }

	public function getButtonText(){
        return $this->getDataProperty(self::DATA_INTERACTIVE_TAG);
    }

	/**
     * @return bool
     */
	public function isFishing(){
        return ($this->fishingHook instanceof FishingHook);
    }

	/**
     * @return FishingHook
     */
	public function getFishingHook(){
        return $this->fishingHook;
    }

	/**
     * @param FishingHook|null $entity
     */
	public function setFishingHook(FishingHook $entity = null){
        if ($entity == null and $this->fishingHook instanceof FishingHook) {
            $this->fishingHook->close();
        }
        $this->fishingHook = $entity;
    }

	/**
     * @return mixed
     */
	public function getDeviceModel(){
        return $this->deviceModel;
    }

	/**
     * @return mixed
     */
	public function getDeviceOS(){
        return $this->deviceOS;
    }

	/**
     * @return Item
     */
	public function getItemInHand(){
        return $this->inventory->getItemInHand();
    }

    /**
     * @param bool $value
     */
    public function setMuted(bool $value){
        $this->muted = $value;
        $this->sendSettings();
    }

    /**
     * @return bool
     */
    public function isMuted() : bool{
        return $this->muted;
    }

    public function isUseElytra(){
        return ($this->isHaveElytra() && $this->elytraIsActivated);
    }

    public function isHaveElytra(){
        if ($this->getInventory()->getArmorItem(1) instanceof Elytra) {
            return true;
        }
        return false;
    }

    public function handleMapInfoRequest(MapInfoRequestPacket $packet) : bool{
        return true;
    }

    /**
     * @return AnvilInventory|null
     */
    public function getAnvilInventory(){
        foreach($this->windowIndex as $inventory){
            if($inventory instanceof AnvilInventory){
                return $inventory;
            }
        }
        return null;
    }
}
