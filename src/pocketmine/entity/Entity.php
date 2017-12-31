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

/**
 * All the entity classes
 */

namespace pocketmine\entity;

use pocketmine\block\Block;
use pocketmine\block\Fire;
use pocketmine\block\Portal;
use pocketmine\block\SlimeBlock;
use pocketmine\block\Water;
use pocketmine\entity\boss\ElderGuardian;
use pocketmine\entity\boss\EnderDragon;
use pocketmine\entity\boss\Wither;
use pocketmine\entity\hostile\Blaze;
use pocketmine\entity\hostile\Creeper;
use pocketmine\entity\hostile\Endermite;
use pocketmine\entity\hostile\Evoker;
use pocketmine\entity\hostile\Ghast;
use pocketmine\entity\hostile\Guardian;
use pocketmine\entity\hostile\Husk;
use pocketmine\entity\hostile\MagmaCube;
use pocketmine\entity\hostile\Shulker;
use pocketmine\entity\hostile\Silverfish;
use pocketmine\entity\hostile\Skeleton;
use pocketmine\entity\hostile\SkeletonHorse;
use pocketmine\entity\hostile\Slime;
use pocketmine\entity\hostile\Stray;
use pocketmine\entity\hostile\Vex;
use pocketmine\entity\hostile\Vindicator;
use pocketmine\entity\hostile\Witch;
use pocketmine\entity\hostile\WitherSkeleton;
use pocketmine\entity\hostile\Zombie;
use pocketmine\entity\hostile\ZombieHorse;
use pocketmine\entity\hostile\ZombieVillager;
use pocketmine\entity\neutral\CaveSpider;
use pocketmine\entity\neutral\Enderman;
use pocketmine\entity\neutral\PolarBear;
use pocketmine\entity\neutral\Spider;
use pocketmine\entity\neutral\ZombiePigman;
use pocketmine\entity\object\AreaEffectCloud;
use pocketmine\entity\object\ArmorStand;
use pocketmine\entity\object\Boat;
use pocketmine\entity\object\FallingSand;
use pocketmine\entity\object\FloatingText;
use pocketmine\entity\object\Lightning;
use pocketmine\entity\object\Minecart;
use pocketmine\entity\object\MinecartChest;
use pocketmine\entity\object\MinecartHopper;
use pocketmine\entity\object\MinecartTNT;
use pocketmine\entity\object\Painting;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\entity\object\XPOrb;
use pocketmine\entity\object\Item as DroppedItem;
use pocketmine\entity\passive\Bat;
use pocketmine\entity\passive\Chicken;
use pocketmine\entity\passive\Cow;
use pocketmine\entity\passive\Mooshroom;
use pocketmine\entity\passive\Pig;
use pocketmine\entity\passive\Rabbit;
use pocketmine\entity\passive\Sheep;
use pocketmine\entity\passive\Squid;
use pocketmine\entity\passive\Villager;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\Egg;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\entity\projectile\FireworkRocket;
use pocketmine\entity\projectile\FishingHook;
use pocketmine\entity\projectile\LingeringPotion;
use pocketmine\entity\projectile\Projectile;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\projectile\ThrownExpBottle;
use pocketmine\entity\projectile\ThrownPotion;
use pocketmine\entity\tameable\Donkey;
use pocketmine\entity\tameable\Horse;
use pocketmine\entity\tameable\Llama;
use pocketmine\entity\tameable\Mule;
use pocketmine\entity\tameable\Ocelot;
use pocketmine\entity\tameable\Parrot;
use pocketmine\entity\tameable\Wolf;
use pocketmine\entity\utility\SnowGolem;
use pocketmine\entity\utility\IronGolem;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Timings;
use pocketmine\item\Elytra;
use pocketmine\item\Item;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Math;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\metadata\Metadatable;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\Color;

abstract class Entity extends Location implements Metadatable, EntityIds {

	const NETWORK_ID = -1;

	const DATA_TYPE_BYTE = 0;
	const DATA_TYPE_SHORT = 1;
	const DATA_TYPE_INT = 2;
	const DATA_TYPE_FLOAT = 3;
	const DATA_TYPE_STRING = 4;
	const DATA_TYPE_SLOT = 5;
	const DATA_TYPE_POS = 6;
	const DATA_TYPE_LONG = 7;
	const DATA_TYPE_VECTOR3F = 8;

	const DATA_FLAGS = 0;
	const DATA_HEALTH = 1; //int (minecart/boat)
	const DATA_VARIANT = 2; //int
	const DATA_COLOR = 3, DATA_COLOUR = 3; //byte
	const DATA_NAMETAG = 4; //string
	const DATA_OWNER_EID = 5; //long
	const DATA_TARGET_EID = 6; //long
	const DATA_AIR = 7; //short
	const DATA_POTION_COLOR = 8; //int (ARGB!)
	const DATA_POTION_AMBIENT = 9; //byte
	/* 10 (byte) */
	const DATA_HURT_TIME = 11; //int (minecart/boat)
	const DATA_HURT_DIRECTION = 12; //int (minecart/boat)
	const DATA_PADDLE_TIME_LEFT = 13; //float
	const DATA_PADDLE_TIME_RIGHT = 14; //float
	const DATA_EXPERIENCE_VALUE = 15; //int (xp orb)
	const DATA_MINECART_DISPLAY_BLOCK = 16; //int (id | (data << 16))
	const DATA_MINECART_DISPLAY_OFFSET = 17; //int
	const DATA_MINECART_HAS_DISPLAY = 18; //byte (must be 1 for minecart to show block inside)

	//TODO: add more properties

	const DATA_ENDERMAN_HELD_ITEM_ID = 23; //short
	const DATA_ENDERMAN_HELD_ITEM_DAMAGE = 24; //short
	const DATA_ENTITY_AGE = 25; //short

	/* 27 (byte) player-specific flags
	 * 28 (int) player "index"?
	 * 29 (block coords) bed position */
	const DATA_FIREBALL_POWER_X = 30; //float
	const DATA_FIREBALL_POWER_Y = 31;
	const DATA_FIREBALL_POWER_Z = 32;
	/* 33 (unknown)
	 * 34 (float) fishing bobber
	 * 35 (float) fishing bobber
	 * 36 (float) fishing bobber */
	const DATA_POTION_AUX_VALUE = 37; //short
	const DATA_LEAD_HOLDER_EID = 38; //long
	const DATA_SCALE = 39; //float
	const DATA_INTERACTIVE_TAG = 40; //string (button text)
	const DATA_NPC_SKIN_ID = 41; //string
	const DATA_URL_TAG = 42; //string
	const DATA_MAX_AIR = 43; //short
	const DATA_MARK_VARIANT = 44; //int
	/* 45 (byte) container stuff
	 * 46 (int) container stuff
	 * 47 (int) container stuff */
	const DATA_BLOCK_TARGET = 48; //block coords (ender crystal)
	const DATA_WITHER_INVULNERABLE_TICKS = 49; //int
	const DATA_WITHER_TARGET_1 = 50; //long
	const DATA_WITHER_TARGET_2 = 51; //long
	const DATA_WITHER_TARGET_3 = 52; //long
	/* 53 (short) */
	const DATA_BOUNDING_BOX_WIDTH = 54; //float
	const DATA_BOUNDING_BOX_HEIGHT = 55; //float
	const DATA_FUSE_LENGTH = 56; //int
	const DATA_RIDER_SEAT_POSITION = 57; //vector3f
	const DATA_RIDER_ROTATION_LOCKED = 58; //byte
	const DATA_RIDER_MAX_ROTATION = 59; //float
	const DATA_RIDER_MIN_ROTATION = 60; //float
	const DATA_AREA_EFFECT_CLOUD_RADIUS = 61; //float
	const DATA_AREA_EFFECT_CLOUD_WAITING = 62; //int
	const DATA_AREA_EFFECT_CLOUD_PARTICLE_ID = 63; //int
	/* 64 (int) shulker-related */
	const DATA_SHULKER_ATTACH_FACE = 65; //byte
	/* 66 (short) shulker-related */
	const DATA_SHULKER_ATTACH_POS = 67; //block coords
	const DATA_TRADING_PLAYER_EID = 68; //long

	/* 70 (byte) command-block */
	const DATA_COMMAND_BLOCK_COMMAND = 71; //string
	const DATA_COMMAND_BLOCK_LAST_OUTPUT = 72; //string
	const DATA_COMMAND_BLOCK_TRACK_OUTPUT = 73; //byte
	const DATA_CONTROLLING_RIDER_SEAT_NUMBER = 74; //byte
	const DATA_STRENGTH = 75; //int
	const DATA_MAX_STRENGTH = 76; //int
	/* 77 (int)
	 * 78 (int) */


	const DATA_FLAG_ONFIRE = 0;
	const DATA_FLAG_SNEAKING = 1;
	const DATA_FLAG_RIDING = 2;
	const DATA_FLAG_SPRINTING = 3;
	const DATA_FLAG_ACTION = 4;
	const DATA_FLAG_INVISIBLE = 5;
	const DATA_FLAG_TEMPTED = 6;
	const DATA_FLAG_INLOVE = 7;
	const DATA_FLAG_SADDLED = 8;
	const DATA_FLAG_POWERED = 9;
	const DATA_FLAG_IGNITED = 10;
	const DATA_FLAG_BABY = 11;
	const DATA_FLAG_CONVERTING = 12;
	const DATA_FLAG_CRITICAL = 13;
	const DATA_FLAG_CAN_SHOW_NAMETAG = 14;
	const DATA_FLAG_ALWAYS_SHOW_NAMETAG = 15;
	const DATA_FLAG_IMMOBILE = 16, DATA_FLAG_NO_AI = 16;
	const DATA_FLAG_SILENT = 17;
	const DATA_FLAG_WALLCLIMBING = 18;
	const DATA_FLAG_CAN_CLIMB = 19;
	const DATA_FLAG_SWIMMER = 20;
	const DATA_FLAG_CAN_FLY = 21;
	const DATA_FLAG_RESTING = 22;
	const DATA_FLAG_SITTING = 23;
	const DATA_FLAG_ANGRY = 24;
	const DATA_FLAG_INTERESTED = 25;
	const DATA_FLAG_CHARGED = 26;
	const DATA_FLAG_TAMED = 27;
	const DATA_FLAG_LEASHED = 28;
	const DATA_FLAG_SHEARED = 29;
	const DATA_FLAG_GLIDING = 30;
	const DATA_FLAG_ELDER = 31;
	const DATA_FLAG_MOVING = 32;
	const DATA_FLAG_BREATHING = 33;
	const DATA_FLAG_CHESTED = 34;
	const DATA_FLAG_STACKABLE = 35;
	const DATA_FLAG_SHOWBASE = 36;
	const DATA_FLAG_REARING = 37;
	const DATA_FLAG_VIBRATING = 38;
	const DATA_FLAG_IDLING = 39;
	const DATA_FLAG_EVOKER_SPELL = 40;
	const DATA_FLAG_CHARGE_ATTACK = 41;
	const DATA_FLAG_WASD_CONTROLLED = 42;
	const DATA_FLAG_CAN_POWER_JUMP = 43;
	const DATA_FLAG_LINGER = 44;
	const DATA_FLAG_HAS_COLLISION = 45;
	const DATA_FLAG_AFFECTED_BY_GRAVITY = 46;
	const DATA_FLAG_FIRE_IMMUNE = 47;
	const DATA_FLAG_DANCING = 48;

	const SOUTH = 0;
	const WEST = 1;
	const NORTH = 2;
	const EAST = 3;

	public static $entityCount = 1;
	/** @var Entity[] */
	private static $knownEntities = [];
	private static $shortNames = [];

    /** @var bool */
    protected $constructed = false;

    public static function getEntityNames(){
        return self::$shortNames;
    }

	public static function init(){
		Entity::registerEntity(AreaEffectCloud::class);
		Entity::registerEntity(ArmorStand::class);
		Entity::registerEntity(Arrow::class);
		Entity::registerEntity(Bat::class);
		Entity::registerEntity(Blaze::class);
		Entity::registerEntity(Boat::class);
		Entity::registerEntity(CaveSpider::class);
		Entity::registerEntity(Chicken::class);
		Entity::registerEntity(Cow::class);
		Entity::registerEntity(Creeper::class);
		Entity::registerEntity(Donkey::class);
		Entity::registerEntity(DroppedItem::class);
		Entity::registerEntity(Egg::class);
		Entity::registerEntity(ElderGuardian::class);
		Entity::registerEntity(Enderman::class);
		Entity::registerEntity(Endermite::class);
		Entity::registerEntity(EnderDragon::class);
		Entity::registerEntity(EnderPearl::class);
		Entity::registerEntity(Evoker::class);
		Entity::registerEntity(FallingSand::class);
		Entity::registerEntity(FireworkRocket::class);
		Entity::registerEntity(FishingHook::class);
		Entity::registerEntity(FloatingText::class);
		Entity::registerEntity(Ghast::class);
		Entity::registerEntity(Guardian::class);
		Entity::registerEntity(Horse::class);
		Entity::registerEntity(Husk::class);
		Entity::registerEntity(IronGolem::class);
		Entity::registerEntity(MagmaCube::class);
		Entity::registerEntity(Lightning::class);
		Entity::registerEntity(LingeringPotion::class);
		Entity::registerEntity(Llama::class);
		Entity::registerEntity(Minecart::class);
		Entity::registerEntity(MinecartChest::class);
		Entity::registerEntity(MinecartHopper::class);
		Entity::registerEntity(MinecartTNT::class);
		Entity::registerEntity(Mooshroom::class);
		Entity::registerEntity(Mule::class);
		Entity::registerEntity(Ocelot::class);
		Entity::registerEntity(Painting::class);
		Entity::registerEntity(Parrot::class);
		Entity::registerEntity(Pig::class);
		Entity::registerEntity(ZombiePigman::class);
		Entity::registerEntity(PolarBear::class);
		Entity::registerEntity(PrimedTNT::class);
		Entity::registerEntity(Rabbit::class);
		Entity::registerEntity(Sheep::class);
		Entity::registerEntity(Shulker::class);
		Entity::registerEntity(Silverfish::class);
		Entity::registerEntity(Skeleton::class);
		Entity::registerEntity(SkeletonHorse::class);
		Entity::registerEntity(Slime::class);
		Entity::registerEntity(Snowball::class);
		Entity::registerEntity(SnowGolem::class);
		Entity::registerEntity(Spider::class);
		Entity::registerEntity(Squid::class);
		Entity::registerEntity(Stray::class);
		Entity::registerEntity(ThrownExpBottle::class);
		Entity::registerEntity(ThrownPotion::class);
		Entity::registerEntity(Vex::class);
		Entity::registerEntity(Villager::class);
		Entity::registerEntity(Vindicator::class);
		Entity::registerEntity(Witch::class);
		Entity::registerEntity(Wither::class);
		Entity::registerEntity(WitherSkeleton::class);
		Entity::registerEntity(Wolf::class);
		Entity::registerEntity(XPOrb::class);
		Entity::registerEntity(Zombie::class);
		Entity::registerEntity(ZombieHorse::class);
		Entity::registerEntity(ZombieVillager::class);

		Entity::registerEntity(Human::class, true);
	}

	/**
	 * @var Player[]
	 */
	protected $hasSpawned = [];

	/** @var Effect[] */
	protected $effects = [];

	protected $id;

	protected $dataFlags = 0;
	protected $dataProperties = [
		self::DATA_FLAGS => [self::DATA_TYPE_LONG, 0],
		self::DATA_AIR => [self::DATA_TYPE_SHORT, 0],
		self::DATA_MAX_AIR => [self::DATA_TYPE_SHORT, 400],
		self::DATA_NAMETAG => [self::DATA_TYPE_STRING, ""],
		self::DATA_LEAD_HOLDER_EID => [self::DATA_TYPE_LONG, -1],
		self::DATA_SCALE => [self::DATA_TYPE_FLOAT, 1],
	];

	public $passenger = null;
	public $vehicle = null;

	/** @var Chunk */
	public $chunk;

	protected $lastDamageCause = null;

	/** @var Block[] */
	private $blocksAround = [];

	public $lastX = null;
	public $lastY = null;
	public $lastZ = null;

	public $motionX = 0;
	public $motionY = 0;
	public $motionZ = 0;
	/** @var Vector3 */
	public $temporalVector;
	public $lastMotionX;
	public $lastMotionY;
	public $lastMotionZ;

	public $lastYaw;
	public $lastPitch;

	/** @var AxisAlignedBB */
	public $boundingBox;
	public $onGround;
	public $inBlock = false;
	public $positionChanged;
	public $motionChanged;
	public $deadTicks = 0;
	protected $age = 0;

    public $width;
	public $height;

	public $eyeHeight = null;
	public $baseOffset = 0;

	/** @var int */
	protected $health = 20;
	private $maxHealth = 20;

	protected $ySize = 0;
	protected $stepHeight = 0;
	public $keepMovement = false;

	public $fallDistance = 0;
	public $ticksLived = 0;
	public $lastUpdate;
	public $fireTicks = 0;
	public $namedtag;
	public $canCollide = true;

	protected $isStatic = false;

	public $isCollided = false;
	public $isCollidedHorizontally = false;
	public $isCollidedVertically = false;

	public $noDamageTicks;
	protected $justCreated;
	private $invulnerable;

	/** @var AttributeMap */
	protected $attributeMap;

	protected $gravity;
	protected $drag;

	/** @var Server */
	protected $server;

	public $closed = false;

	/** @var \pocketmine\event\TimingsHandler */
	protected $timings;
	protected $isPlayer = false;

	/** @var Entity */
	protected $linkedEntity = null;
	/** 0 no linked 1 linked other 2 be linked */
	protected $linkedType = null;


	protected $riding = null;

	public $dropExp = [0, 0];


	/**
	 * Entity constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
	    $this->constructed = true;
		$this->timings = Timings::getEntityTimings($this);

		$this->isPlayer = $this instanceof Player;

		$this->temporalVector = new Vector3();

		if($this->eyeHeight === null){
			$this->eyeHeight = $this->height / 2 + 0.1;
		}

		$this->id = Entity::$entityCount++;
		$this->justCreated = true;
		$this->namedtag = $nbt;

		$this->chunk = $level->getChunk($this->namedtag["Pos"][0] >> 4, $this->namedtag["Pos"][2] >> 4, true);
		assert($this->chunk !== null);
		$this->setLevel($level);
		$this->server = $level->getServer();

		$this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);

		/** @var float[] $pos */
        $pos = $this->namedtag->getListTag("Pos")->getAllValues();
        /** @var float[] $rotation */
        $rotation = $this->namedtag->getListTag("Rotation")->getAllValues();

        $this->setPositionAndRotation($this->temporalVector->setComponents(...$pos), ...$rotation);

        /** @var float[] $motion */
        $motion = [0, 0, 0];
        if($this->namedtag->hasTag("Motion", ListTag::class)){
            $motion = $this->namedtag->getListTag("Motion")->getAllValues();
        }

        $this->setMotion($this->temporalVector->setComponents(...$motion));

        $this->resetLastMovements();

		assert(!is_nan($this->x) and !is_infinite($this->x) and !is_nan($this->y) and !is_infinite($this->y) and !is_nan($this->z) and !is_infinite($this->z));

        $this->fallDistance = $this->namedtag->getFloat("FallDistance", 0);

        $this->fireTicks = $this->namedtag->getShort("Fire", 0);
        if($this->isOnFire()){
            $this->setGenericFlag(self::DATA_FLAG_ONFIRE, true);
        }

        $this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $this->namedtag->getShort("Air", 300), false);
        $this->onGround = $this->namedtag->getByte("OnGround", 0) !== 0;
        $this->invulnerable = $this->namedtag->getByte("Invulnerable", 0) !== 0;

		$this->attributeMap = new AttributeMap();
		$this->addAttributes();

        $this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
        $this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);

		$this->chunk->addEntity($this);
		$this->level->addEntity($this);
		$this->initEntity();
		$this->lastUpdate = $this->server->getTick();
		$this->server->getPluginManager()->callEvent(new EntitySpawnEvent($this));

		$this->scheduleUpdate();

	}

	//add original function (use create AI etc)

	/**
	 * @return mixed
	 */
	public function getHeight(){
		return $this->height;
	}

	/**
	 * @return mixed
	 */
	public function getWidth(){
		return $this->width;
	}

	/**
	 * @param $scale
	 */
	public function setScale($scale){
		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $scale);
	}

	/**
	 * @return mixed
	 */
	public function getScale(){
		return $this->getDataProperty(self::DATA_SCALE);
	}

	/**
	 * @return int
	 */
	public function getDropExpMin() : int{
		return $this->dropExp[0];
	}

	/**
	 * @return int
	 */
	public function getDropExpMax() : int{
		return $this->dropExp[1];
	}

	/**
	 * @return string
	 */
	public function getNameTag(){
		return $this->getDataProperty(self::DATA_NAMETAG);
	}

	/**
	 * @return bool
	 */
	public function isNameTagVisible(){
		return $this->getGenericFlag(self::DATA_FLAG_CAN_SHOW_NAMETAG);
	}

	/**
	 * @return bool
	 */
	public function isNameTagAlwaysVisible(){
		return $this->getGenericFlag(self::DATA_FLAG_ALWAYS_SHOW_NAMETAG);
	}

	/**
	 * @param string $name
	 */
	public function setNameTag($name){
		$this->setDataProperty(self::DATA_NAMETAG, self::DATA_TYPE_STRING, $name);
	}

	/**
	 * @param bool $value
	 */
	public function setNameTagVisible($value = true){
		$this->setGenericFlag(self::DATA_FLAG_CAN_SHOW_NAMETAG, $value);
	}

	/**
	 * @param bool $value
	 */
	public function setNameTagAlwaysVisible($value = true){
		$this->setGenericFlag(self::DATA_FLAG_ALWAYS_SHOW_NAMETAG, $value);
	}

	/**
	 * @return bool
	 */
	public function isSneaking(){
		return $this->getGenericFlag( self::DATA_FLAG_SNEAKING);
	}

	/**
	 * @param bool $value
	 */
	public function setSneaking($value = true){
		$this->setGenericFlag(self::DATA_FLAG_SNEAKING, (bool) $value);
	}

	/**
	 * @return bool
	 */
	public function isSprinting(){
		return $this->getGenericFlag(self::DATA_FLAG_SPRINTING);
	}

	/**
	 * @param bool $value
	 */
	public function setSprinting(bool $value = true){
		if($value !== $this->isSprinting()){
			$this->setGenericFlag(self::DATA_FLAG_SPRINTING, $value);
			$attr = $this->attributeMap->getAttribute(Attribute::MOVEMENT_SPEED);
			$attr->setValue($value ? ($attr->getValue() * 1.3) : ($attr->getValue() / 1.3));
		}
	}

	/**
	 * @return bool
	 */
	public function isGliding(){
		return $this->getGenericFlag(self::DATA_FLAG_IDLING);
	}

	/**
	 * @param bool $value
	 */
	public function setGliding(bool $value = true){
		$this->setGenericFlag(self::DATA_FLAG_GLIDING, $value);
		$this->setGenericFlag(self::DATA_FLAG_IDLING, $value);
	}

	/**
	 * @return bool
	 */
	public function isImmobile() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_IMMOBILE);
	}

	/**
	 * @param bool $value
	 */
	public function setImmobile(bool $value = true){
		$this->setGenericFlag(self::DATA_FLAG_IMMOBILE, $value);
	}

	/**
	 * Returns whether the entity is able to climb blocks such as ladders or vines.
	 *
	 * @return bool
	 */
	public function canClimb() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_CAN_CLIMB);
	}

	/**
	 * Sets whether the entity is able to climb climbable blocks.
	 *
	 * @param bool $value
	 */
	public function setCanClimb(bool $value = true){
		$this->setGenericFlag(self::DATA_FLAG_CAN_CLIMB, $value);
	}

	/**
	 * Returns whether this entity is climbing a block. By default this is only true if the entity is climbing a ladder or vine or similar block.
	 *
	 * @return bool
	 */
	public function canClimbWalls() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_WALLCLIMBING);
	}

	/**
	 * Sets whether the entity is climbing a block. If true, the entity can climb anything.
	 *
	 * @param bool $value
	 */
	public function setCanClimbWalls(bool $value = true){
		$this->setGenericFlag(self::DATA_FLAG_WALLCLIMBING, $value);
	}
	
	/**
	 * Returns the entity ID of the owning entity, or null if the entity doesn't have an owner.
	 * @return int|string|null
	 */
	public function getOwningEntityId(){
		return $this->getDataProperty(self::DATA_OWNER_EID);
	}
	
	/**
	 * Returns the owning entity, or null if the entity was not found.
	 * @return Entity|null
	 */
	public function getOwningEntity(){
		$eid = $this->getOwningEntityId();
		if($eid !== null){
			return $this->server->findEntity($eid, $this->level);
		}
		return null;
	}

    /**
     * Sets the owner of the entity.
     *
     * @param Entity $owner
     *
     * @return bool
     * @throws \InvalidArgumentException if the supplied entity is not valid
     */
	public function setOwningEntity(Entity $owner){
		if($owner->closed){
			throw new \InvalidArgumentException("Supplied owning entity is garbage and cannot be used");
		}
		
		$this->setDataProperty(self::DATA_OWNER_EID, self::DATA_TYPE_LONG, $owner->getId());
		return true;
	}


	/**
	 * @return Effect[]
	 */
	public function getEffects(){
		return $this->effects;
	}

	public function removeAllEffects(){
		foreach($this->effects as $effect){
			$this->removeEffect($effect->getId());
		}
	}

	/**
	 * @param $effectId
	 *
	 * @return bool
	 */
	public function removeEffect($effectId){
		return false;
	}

	/**
	 * @param $effectId
	 *
	 * @return null|Effect
	 */
	public function getEffect($effectId){
		return isset($this->effects[$effectId]) ? $this->effects[$effectId] : null;
	}

	/**
	 * @param $effectId
	 *
	 * @return bool
	 */
	public function hasEffect($effectId){
		return isset($this->effects[$effectId]);
	}

	/**
	 * @param Effect $effect
	 *
	 * @return bool
	 */
	public function addEffect(Effect $effect):bool{
	    return false;
	}

    protected function recalculateEffectColor(){
	    /** @var Color[] $colors */
		$colors = [];
		$ambient = true;
		foreach($this->effects as $effect){
			if($effect->isVisible()){
				$level = $effect->getEffectLevel();
				$color = $effect->getColor();
                for($i = 0; $i < $level; ++$i) {
                    $colors[] = $color;
                }

				if(!$effect->isAmbient()){
					$ambient = false;
				}
			}
		}

		if(!empty($colors)){
			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, Color::mix(...$colors)->toARGB());
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, $ambient ? 1 : 0);
		}else{
			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, 0);
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, 0);
		}
	}

	/**
	 * @param int|string  $type
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param             $args
	 *
	 * @return Entity|Projectile
	 */
	public static function createEntity($type, Level $level, CompoundTag $nbt, ...$args){
		if(isset(self::$knownEntities[$type])){
			$class = self::$knownEntities[$type];

			return new $class($level, $nbt, ...$args);
		}

		return null;
	}

	/**
	 * @param      $className
	 * @param bool $force
	 *
	 * @return bool
	 */
	public static function registerEntity($className, $force = false){
		$class = new \ReflectionClass($className);
		if(is_a($className, Entity::class, true) and !$class->isAbstract()){
			if($className::NETWORK_ID !== -1){
				self::$knownEntities[$className::NETWORK_ID] = $className;
			}elseif(!$force){
				return false;
			}

			self::$knownEntities[$class->getShortName()] = $className;
			self::$shortNames[$className] = $class->getShortName();

			return true;
		}

		return false;
	}

    /**
     * Helper function which creates minimal NBT needed to spawn an entity.
     *
     * @param Vector3      $pos
     * @param Vector3|null $motion
     * @param float        $yaw
     * @param float        $pitch
     *
     * @return CompoundTag
     */
    public static function createBaseNBT(Vector3 $pos, $motion = null , float $yaw = 0.0, float $pitch = 0.0) : CompoundTag{
        if($motion == null)
            $motion = new Vector3(0.0,0.0,0.0);
        return new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $pos->x),
				new DoubleTag("", $pos->y),
				new DoubleTag("", $pos->z)
			]),
			new ListTag("Motion", [
				new DoubleTag("", $motion->x),
				new DoubleTag("", $motion->y),
				new DoubleTag("", $motion->z)
			]),
			new ListTag("Rotation", [
				new FloatTag("", $yaw),
				new FloatTag("", $pitch)
			])
		]);
	}

	/**
	 * Returns the short save name
	 *
	 * @return string
	 */
	public function getSaveId(){
		return self::$shortNames[static::class];
	}

	public function saveNBT(){
		if(!($this instanceof Player)){
			$this->namedtag->setString("id", $this->getSaveId());
			if($this->getNameTag() !== ""){
				$this->namedtag->setString("CustomName", $this->getNameTag());
				$this->namedtag->setByte("CustomNameVisible", +$this->isNameTagVisible());
				$this->namedtag->setByte("CustomNameAlwaysVisible", +$this->isNameTagAlwaysVisible());
			}else{
                $this->namedtag->removeTag("CustomName", "CustomNameVisible", "CustomNameAlwaysVisible");
			}
		}

		$this->namedtag->setTag(new ListTag("Pos", [
			new DoubleTag("", $this->x),
			new DoubleTag("", $this->y),
			new DoubleTag("", $this->z)
		]));

		$this->namedtag->setTag(new ListTag("Motion", [
			new DoubleTag("", $this->motionX),
			new DoubleTag("", $this->motionY),
			new DoubleTag("", $this->motionZ)
		]));

		$this->namedtag->setTag(new ListTag("Rotation", [
			new FloatTag("", $this->yaw),
			new FloatTag("", $this->pitch)
		]));

		$this->namedtag->setFloat("FallDistance", $this->fallDistance);
		$this->namedtag->setShort("Fire", $this->fireTicks);
		$this->namedtag->setShort("Air", $this->getDataProperty(self::DATA_AIR));
		$this->namedtag->setByte("OnGround", +$this->onGround);
		$this->namedtag->setByte("Invulnerable", +$this->invulnerable);

		if(count($this->effects) > 0){
			$effects = [];
			foreach($this->effects as $effect){
				$effects[] = new CompoundTag("", [
					"Id" => new ByteTag("Id", $effect->getId()),
					"Amplifier" => new ByteTag("Amplifier", $effect->getAmplifier()),
					"Duration" => new IntTag("Duration", $effect->getDuration()),
					"Ambient" => new ByteTag("Ambient", 0),
					"ShowParticles" => new ByteTag("ShowParticles", $effect->isVisible() ? 1 : 0)
				]);
			}

			$this->namedtag->ActiveEffects = new ListTag("ActiveEffects", $effects);
		}else{
			unset($this->namedtag->ActiveEffects);
		}
	}

	protected function initEntity(){
		if(!($this->namedtag instanceof CompoundTag)){
			throw new \InvalidArgumentException("Expecting CompoundTag, received " . get_class($this->namedtag));
		}

        if($this->namedtag->hasTag("CustomName", StringTag::class)) {
            $this->setNameTag($this->namedtag->getString("CustomName"));
            $this->setNameTagVisible($this->namedtag->getByte("CustomNameVisible", 1) !== 0);
            $this->setNameTagAlwaysVisible($this->namedtag->getByte("CustomNameAlwaysVisible", 1) !== 0);
        }

		$this->scheduleUpdate();

		if(isset($this->namedtag->ActiveEffects)){
			foreach($this->namedtag->ActiveEffects->getValue() as $e){
				$amplifier = $e["Amplifier"] & 0xff; //0-255 only

				$effect = Effect::getEffect($e["Id"]);
				if($effect === null){
					continue;
				}

				$effect->setAmplifier($amplifier)->setDuration($e["Duration"])->setVisible($e["ShowParticles"] > 0);

				$this->addEffect($effect);
			}
		}

	}

	protected function addAttributes(){
	}

	/**
	 * @return Player[]
	 */
	public function getViewers(){
		return $this->hasSpawned;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		if(!isset($this->hasSpawned[$player->getLoaderId()]) and isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())])){
			$this->hasSpawned[$player->getLoaderId()] = $player;
		}
	}

	/**
	 * @param Player $player
	 */
	public function sendPotionEffects(Player $player){
		foreach($this->effects as $effect){
			$pk = new MobEffectPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->effectId = $effect->getId();
			$pk->amplifier = $effect->getAmplifier();
			$pk->particles = $effect->isVisible();
			$pk->duration = $effect->getDuration();
			$pk->eventId = MobEffectPacket::EVENT_ADD;

			$player->dataPacket($pk);
		}
	}

	/**
	 * @param Player[]|Player $player
	 * @param array           $data Properly formatted entity data, defaults to everything
	 */
	public function sendData($player, array $data = null){
		if(!is_array($player)){
			$player = [$player];
		}

		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data === null ? $this->dataProperties : $data;

		foreach($player as $p){
			if($p === $this){
				continue;
			}
			$p->dataPacket(clone $pk);
		}
		if($this instanceof Player){
			$this->dataPacket($pk);
		}
	}

	/**
	 * @param Player $player
	 * @param bool   $send
	 */
	public function despawnFrom(Player $player, bool $send = true){
		if(isset($this->hasSpawned[$player->getLoaderId()])){
			if($send){
				$pk = new RemoveEntityPacket();
				$pk->entityUniqueId = $this->id;
				$player->dataPacket($pk);
			}
			unset($this->hasSpawned[$player->getLoaderId()]);
		}
	}

	public function onInteract(Player $player, Item $item){
	    return false;
    }

    /**
     * @param EntityDamageEvent $source
     * @return bool
     */
	public function attack(EntityDamageEvent $source){
		if($this->hasEffect(Effect::FIRE_RESISTANCE)
			and ($source->getCause() === EntityDamageEvent::CAUSE_FIRE
				or $source->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK
				or $source->getCause() === EntityDamageEvent::CAUSE_LAVA)
		){
			$source->setCancelled();
		}

		$this->server->getPluginManager()->callEvent($source);
		if($source->isCancelled()){
			return false;
		}
		$this->setLastDamageCause($source);

        $damage = $source->getFinalDamage();
        $absorption = $this->getAbsorption();
        if($absorption > 0){
            if($absorption > $damage){
                //Use absorption health before normal health.
                $this->setAbsorption($absorption - $damage);
                $damage = 0;
            }else{
                $this->setAbsorption(0);
                $damage -= $absorption;
            }
        }
        $this->setHealth($this->getHealth() - $damage);

        return true;
	}

    public function getAbsorption() : float{
        return 0;
    }
    public function setAbsorption(float $absorption){
    }

    /**
     * @param EntityRegainHealthEvent $source
     */
	public function heal(EntityRegainHealthEvent $source){
		$this->server->getPluginManager()->callEvent($source);
		if($source->isCancelled()){
			return;
		}

		$this->setHealth($this->getHealth() + $source->getAmount());
	}

	/**
	 * @return int
	 */
	public function getHealth(){
		return $this->health;
	}

	/**
	 * @return bool
	 */
	public function isAlive(){
		return $this->health > 0;
	}

    /**
     * Sets the health of the Entity. This won't send any update to the players
     *
     * @param float $amount
     */
    public function setHealth(float $amount){
        if($amount == $this->health){
            return;
        }

        if($amount <= 0){
            if($this->isAlive()){
                $this->kill();
            }
        }elseif($amount <= $this->getMaxHealth() or $amount < $this->health){
            $this->health = $amount;
        }else{
            $this->health = $this->getMaxHealth();
        }
    }

    /**
	 * @param EntityDamageEvent $type
	 */
	public function setLastDamageCause(EntityDamageEvent $type){
		$this->lastDamageCause = $type;
	}

	/**
	 * @return EntityDamageEvent|null
	 */
	public function getLastDamageCause(){
		return $this->lastDamageCause;
	}

	/**
	 * @return AttributeMap
	 */
	public function getAttributeMap(){
		return $this->attributeMap;
	}

	/**
	 * @return int
	 */
	public function getMaxHealth(){
		return $this->maxHealth + ($this->hasEffect(Effect::HEALTH_BOOST) ? 4 * ($this->getEffect(Effect::HEALTH_BOOST)->getAmplifier() + 1) : 0);
	}

	/**
	 * @param int $amount
	 */
	public function setMaxHealth($amount){
		$this->maxHealth = (int) $amount;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canCollideWith(Entity $entity){
		return !$this->justCreated and $entity !== $this;
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 *
	 * @return bool
	 */
	protected function checkObstruction($x, $y, $z){
		$i = Math::floorFloat($x);
		$j = Math::floorFloat($y);
		$k = Math::floorFloat($z);

		$diffX = $x - $i;
		$diffY = $y - $j;
		$diffZ = $z - $k;

		if(Block::$solid[$this->level->getBlockIdAt($i, $j, $k)]){
			$flag = !Block::$solid[$this->level->getBlockIdAt($i - 1, $j, $k)];
			$flag1 = !Block::$solid[$this->level->getBlockIdAt($i + 1, $j, $k)];
			$flag2 = !Block::$solid[$this->level->getBlockIdAt($i, $j - 1, $k)];
			$flag3 = !Block::$solid[$this->level->getBlockIdAt($i, $j + 1, $k)];
			$flag4 = !Block::$solid[$this->level->getBlockIdAt($i, $j, $k - 1)];
			$flag5 = !Block::$solid[$this->level->getBlockIdAt($i, $j, $k + 1)];

			$direction = -1;
			$limit = 9999;

			if($flag){
				$limit = $diffX;
				$direction = 0;
			}

			if($flag1 and 1 - $diffX < $limit){
				$limit = 1 - $diffX;
				$direction = 1;
			}

			if($flag2 and $diffY < $limit){
				$limit = $diffY;
				$direction = 2;
			}

			if($flag3 and 1 - $diffY < $limit){
				$limit = 1 - $diffY;
				$direction = 3;
			}

			if($flag4 and $diffZ < $limit){
				$limit = $diffZ;
				$direction = 4;
			}

			if($flag5 and 1 - $diffZ < $limit){
				$direction = 5;
			}

			$force = lcg_value() * 0.2 + 0.1;

			if($direction === 0){
				$this->motionX = -$force;

				return true;
			}

			if($direction === 1){
				$this->motionX = $force;

				return true;
			}

			if($direction === 2){
				$this->motionY = -$force;

				return true;
			}

			if($direction === 3){
				$this->motionY = $force;

				return true;
			}

			if($direction === 4){
				$this->motionZ = -$force;

				return true;
			}

			if($direction === 5){
				$this->motionZ = $force;

				return true;
			}
		}

		return false;
	}

	/**
	 * @param int $tickDiff
	 *
	 * @return bool
	 */
	public function entityBaseTick(int $tickDiff = 1){

		Timings::$timerEntityBaseTick->startTiming();
		//TODO: check vehicles

		$this->blocksAround = null;
		$this->justCreated = false;

		if(!$this->isAlive()){
			$this->removeAllEffects();
			$this->despawnFromAll();
			if(!$this->isPlayer){
				$this->close();
			}

			Timings::$timerEntityBaseTick->stopTiming();

			return false;
		}

        foreach($this->effects as $effect){
            if($effect->canTick()){
                $effect->applyEffect($this);
            }
            $effect->setDuration(max(0, $effect->getDuration() - $tickDiff));
            if($effect->getDuration() <= 0){
                $this->removeEffect($effect->getId());
            }
        }

		$hasUpdate = false;

		$this->checkBlockCollision();

		if($this->y <= -16 and $this->isAlive()){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_VOID, 10);
			$this->attack($ev);
			$hasUpdate = true;
		}

		if($this->fireTicks > 0){
			if($this->isFireProof()){
				if($this->fireTicks > 1){
					$this->fireTicks = 1;
				}else{
					$this->fireTicks -= 1;
				}
			}else{
				if(!$this->hasEffect(Effect::FIRE_RESISTANCE) and (($this->fireTicks % 20) === 0 or $tickDiff > 20)){
					$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FIRE_TICK, 1);
					$this->attack($ev);
				}
				$this->fireTicks -= $tickDiff;
			}

			if($this->fireTicks <= 0 && $this->fireTicks > -10){
				$this->extinguish();
			}else{
				$this->setGenericFlag(self::DATA_FLAG_ONFIRE, true);
				$hasUpdate = true;
			}
		}

		if($this->noDamageTicks > 0){
			$this->noDamageTicks -= $tickDiff;
			if($this->noDamageTicks < 0){
				$this->noDamageTicks = 0;
			}
		}

		$this->age += $tickDiff;
		$this->ticksLived += $tickDiff;

		Timings::$timerEntityBaseTick->stopTiming();

		return $hasUpdate;
	}

    protected function updateMovement(){
        $diffPosition = ($this->x - $this->lastX) ** 2 + ($this->y - $this->lastY) ** 2 + ($this->z - $this->lastZ) ** 2;
        $diffRotation = ($this->yaw - $this->lastYaw) ** 2 + ($this->pitch - $this->lastPitch) ** 2;

        $diffMotion = ($this->motionX - $this->lastMotionX) ** 2 + ($this->motionY - $this->lastMotionY) ** 2 + ($this->motionZ - $this->lastMotionZ) ** 2;

        if($diffPosition > 0.0001 or $diffRotation > 1.0){
            $this->lastX = $this->x;
            $this->lastY = $this->y;
            $this->lastZ = $this->z;

            $this->lastYaw = $this->yaw;
            $this->lastPitch = $this->pitch;

            $this->broadcastMovement();
        }

        if($diffMotion > 0.0025 or ($diffMotion > 0.0001 and $this->getMotion()->lengthSquared() <= 0.0001)){ //0.05 ** 2
            $this->lastMotionX = $this->motionX;
            $this->lastMotionY = $this->motionY;
            $this->lastMotionZ = $this->motionZ;

            $this->broadcastMotion();
        }
    }

    public function getOffsetPosition(Vector3 $vector3) : Vector3{
        return new Vector3($vector3->x, $vector3->y + $this->getEyeHeight(), $vector3->z);
    }

    protected function broadcastMovement(){
        $pk = new MoveEntityPacket();
        $pk->entityRuntimeId = $this->id;
        $pk->position = $this->getOffsetPosition($this);
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->headYaw = $this->yaw; //TODO

        $this->level->addChunkPacket($this->chunk->getX(), $this->chunk->getZ(), $pk);
    }

    protected function broadcastMotion(){
        $pk = new SetEntityMotionPacket();
        $pk->entityRuntimeId = $this->id;
        $pk->motion = $this->getMotion();

        $this->level->addChunkPacket($this->chunk->getX(), $this->chunk->getZ(), $pk);
    }

	/**
	 * @return Vector3
	 */
	public function getDirectionVector(){
		$y = -sin(deg2rad($this->pitch));
		$xz = cos(deg2rad($this->pitch));
		$x = -$xz * sin(deg2rad($this->yaw));
		$z = $xz * cos(deg2rad($this->yaw));

		return $this->temporalVector->setComponents($x, $y, $z)->normalize();
	}

	/**
	 * @return Vector2
	 */
	public function getDirectionPlane(){
		return (new Vector2(-cos(deg2rad($this->yaw) - M_PI_2), -sin(deg2rad($this->yaw) - M_PI_2)))->normalize();
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){
		if($this->closed){
			return false;
		}

        $this->lastUpdate = $currentTick;

		if(!$this->isAlive()){
			++$this->deadTicks;
			if($this->deadTicks >= 25){
				$this->despawnFromAll();
				if(!$this->isPlayer){
					$this->close();
				}
			}

			return $this->deadTicks < 20;
		}

		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0){
			return false;
		}
        
		$this->timings->startTiming();

		$hasUpdate = $this->entityBaseTick($tickDiff);

		$this->updateMovement();

		$this->timings->stopTiming();

		//if($this->isStatic())
		return $hasUpdate;
		//return !($this instanceof Player);
	}

   /**
   * @depracated
   */
	public final function scheduleUpdate(){
		
	}

	/**
	 * @return bool
	 */
	public function isOnFire(){
		return $this->fireTicks > 0;
	}

	/**
	 * @param $seconds
	 */
	public function setOnFire($seconds){
		$ticks = $seconds * 20;
		if($ticks > $this->fireTicks){
			$this->fireTicks = $ticks;
		}
	}

	/**
	 * @return bool
	 */
	public function isFireProof() : bool{
		return false;
	}

	/**
	 * @return int|null
	 */
	public function getDirection(){
		$rotation = ($this->yaw - 90) % 360;
		if($rotation < 0){
			$rotation += 360.0;
		}
		if((0 <= $rotation and $rotation < 45) or (315 <= $rotation and $rotation < 360)){
			return 2; //North
		}elseif(45 <= $rotation and $rotation < 135){
			return 3; //East
		}elseif(135 <= $rotation and $rotation < 225){
			return 0; //South
		}elseif(225 <= $rotation and $rotation < 315){
			return 1; //West
		}else{
			return null;
		}
	}

    /**
     * @return int
     */
    public function getFireTicks():int{
        return $this->fireTicks;
    }

    /**
     * @param int $fireTicks
     */
    public function setFireTicks(int $fireTicks){
        $this->fireTicks = $fireTicks;
    }

	public function extinguish(){
		$this->fireTicks = 0;
		$this->setGenericFlag(self::DATA_FLAG_ONFIRE, false);
	}

	/**
	 * @return bool
	 */
	public function canTriggerWalking(){
		return true;
	}

	public function resetFallDistance(){
		$this->fallDistance = 0;
	}

    /**
     * @param $distanceThisTick
     * @param $onGround
     */
	protected function updateFallState($distanceThisTick, $onGround){
        if($onGround){
            if($this->fallDistance > 0){
                $this->fall($this->fallDistance);
                $this->resetFallDistance();
            }
        }elseif($distanceThisTick < 0){
            $this->fallDistance -= $distanceThisTick;
        }
	}

	/**
	 * @return AxisAlignedBB
	 */
	public function getBoundingBox(){
		return $this->boundingBox;
	}

	/**
	 * @param $fallDistance
	 */
	public function fall($fallDistance){
		if($this instanceof Player and $this->isSpectator()){
			return;
		}
		if($fallDistance > 3){
			$this->getLevel()->addParticle(new DestroyBlockParticle($this, $this->getLevel()->getBlock($this->floor()->subtract(0, 1, 0))));
		}
		if($this->isInsideOfWater()){
			return;
		}
		$damage = ceil($fallDistance - 3 - ($this->hasEffect(Effect::JUMP) ? $this->getEffect(Effect::JUMP)->getAmplifier() + 1 : 0));

		//Get the block directly beneath the player's feet, check if it is a slime block
		if($this->getLevel()->getBlock($this->floor()->subtract(0, 1, 0)) instanceof SlimeBlock){
			$damage = 0;
		}
		//TODO Improve
		if($this instanceof Player){
			if($this->getInventory()->getChestplate() instanceof Elytra){
				$damage = 0;
			}
		}
		if($damage > 0){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FALL, $damage);
			$this->attack($ev);
		}
	}

	public function handleLavaMovement(){ //TODO

	}

	/**
	 * @return float|int|null
	 */
	public function getEyeHeight(){
		return $this->eyeHeight;
	}

	public function moveFlying(){ //TODO

	}

    /**
     * @param Player $player
     * @return bool
     */
	public function onCollideWithPlayer(Player $player) : bool{
        return false;
	}

	/**
	 * @param Level $targetLevel
	 *
	 * @return bool
	 */
	protected function switchLevel(Level $targetLevel){
		if($this->closed){
			return false;
		}

		if($this->isValid()){
			$this->server->getPluginManager()->callEvent($ev = new EntityLevelChangeEvent($this, $this->level, $targetLevel));
			if($ev->isCancelled()){
				return false;
			}

			$this->level->removeEntity($this);
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->despawnFromAll();
		}

		$this->setLevel($targetLevel);
		$this->level->addEntity($this);
		$this->chunk = null;

		return true;
	}

	/**
	 * @return Position
	 */
	public function getPosition(){
		return new Position($this->x, $this->y, $this->z, $this->level);
	}

	/**
	 * @return Location
	 */
	public function getLocation(){
		return new Location($this->x, $this->y, $this->z, $this->yaw, $this->pitch, $this->level);
	}

	/**
	 * @return bool
	 */
	public function isInsideOfPortal(){
		$blocks = $this->getBlocksAround();

		foreach($blocks as $block){
			if($block instanceof Portal){
				return "nether";
			}elseif($block->getId() == 119){
				return "ender";
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isInsideOfWater(){
		$block = $this->level->getBlockAt(Math::floorFloat($this->x), Math::floorFloat($y = ($this->y + $this->getEyeHeight())), Math::floorFloat($this->z));

		if($block instanceof Water){
			$f = ($block->y + 1) - ($block->getFluidHeightPercent() - 0.1111111);

			return $y < $f;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isInsideOfSolid(){
		$block = $this->level->getBlockAt(Math::floorFloat($this->x), Math::floorFloat($y = ($this->y + $this->getEyeHeight())), Math::floorFloat($this->z));

		$bb = $block->getBoundingBox();

		if($bb !== null and $block->isSolid() and !$block->isTransparent() and $bb->intersectsWith($this->getBoundingBox())){
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isInsideOfFire(){
		foreach($this->getBlocksAround() as $block){
			if($block instanceof Fire){
				return true;
			}
		}

		return false;
	}

	public function getStepHeight(){
	    return 0;
    }

    public function fastMove(float $dx, float $dy, float $dz) : bool{
        $this->blocksAround = null;
        if($dx == 0 and $dz == 0 and $dy == 0){
            return true;
        }
        Timings::$entityMoveTimer->startTiming();
        $newBB = $this->boundingBox->getOffsetBoundingBox($dx, $dy, $dz);
        $list = $this->level->getCollisionCubes($this, $newBB, false);
        if(count($list) === 0){
            $this->boundingBox = $newBB;
        }
        $this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
        $this->y = $this->boundingBox->minY - $this->ySize;
        $this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;
        $this->checkChunks();
        if(!$this->onGround or $dy != 0){
            $bb = clone $this->boundingBox;
            $bb->minY -= 0.75;
            $this->onGround = false;
            if(count($this->level->getCollisionBlocks($bb)) > 0){
                $this->onGround = true;
            }
        }
        $this->isCollided = $this->onGround;
        $this->updateFallState($dy, $this->onGround);
        Timings::$entityMoveTimer->stopTiming();
        return true;
    }

	/**
	 * @param $dx
	 * @param $dy
	 * @param $dz
	 *
	 * @return bool
	 */
	public function move($dx, $dy, $dz){
        $this->blocksAround = [];
        if($dx == 0 and $dz == 0 and $dy == 0){
            return true;
        }

        if($this->keepMovement){
            $this->boundingBox->offset($dx, $dy, $dz);
            $this->setPosition($this->temporalVector->setComponents(($this->boundingBox->minX + $this->boundingBox->maxX) / 2, $this->boundingBox->minY, ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2));
            $this->onGround = $this->isPlayer ? true : false;
            return true;
        }else{
            Timings::$entityMoveTimer->startTiming();
            $this->ySize *= 0.4;
            $movX = $dx;
            $movY = $dy;
            $movZ = $dz;
            $axisalignedbb = clone $this->boundingBox;
            assert(abs($dx) <= 20 and abs($dy) <= 20 and abs($dz) <= 20, "Movement distance is excessive: dx=$dx, dy=$dy, dz=$dz");
            $list = $this->level->getCollisionCubes($this, $this->level->getTickRate() > 1 ? $this->boundingBox->getOffsetBoundingBox($dx, $dy, $dz) : $this->boundingBox->addCoord($dx, $dy, $dz), false);
            foreach($list as $bb){
                $dy = $bb->calculateYOffset($this->boundingBox, $dy);
            }
            $this->boundingBox->offset(0, $dy, 0);
            $fallingFlag = ($this->onGround or ($dy != $movY and $movY < 0));
            foreach($list as $bb){
                $dx = $bb->calculateXOffset($this->boundingBox, $dx);
            }
            $this->boundingBox->offset($dx, 0, 0);
            foreach($list as $bb){
                $dz = $bb->calculateZOffset($this->boundingBox, $dz);
            }
            $this->boundingBox->offset(0, 0, $dz);
            if($this->stepHeight > 0 and $fallingFlag and $this->ySize < 0.05 and ($movX != $dx or $movZ != $dz)){
                $cx = $dx;
                $cy = $dy;
                $cz = $dz;
                $dx = $movX;
                $dy = $this->stepHeight;
                $dz = $movZ;
                $axisalignedbb1 = clone $this->boundingBox;
                $this->boundingBox->setBB($axisalignedbb);
                $list = $this->level->getCollisionCubes($this, $this->boundingBox->addCoord($dx, $dy, $dz), false);
                foreach($list as $bb){
                    $dy = $bb->calculateYOffset($this->boundingBox, $dy);
                }
                $this->boundingBox->offset(0, $dy, 0);
                foreach($list as $bb){
                    $dx = $bb->calculateXOffset($this->boundingBox, $dx);
                }
                $this->boundingBox->offset($dx, 0, 0);
                foreach($list as $bb){
                    $dz = $bb->calculateZOffset($this->boundingBox, $dz);
                }
                $this->boundingBox->offset(0, 0, $dz);
                if(($cx ** 2 + $cz ** 2) >= ($dx ** 2 + $dz ** 2)){
                    $dx = $cx;
                    $dy = $cy;
                    $dz = $cz;
                    $this->boundingBox->setBB($axisalignedbb1);
                }else{
                    $this->ySize += 0.5;
                }
            }
            $this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
            $this->y = $this->boundingBox->minY - $this->ySize;
            $this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;
            $this->checkChunks();
            $this->checkBlockCollision();
            $this->checkGroundState($movX, $movY, $movZ, $dx, $dy, $dz);
            $this->updateFallState($dy, $this->onGround);
            if($movX != $dx){
                $this->motionX = 0;
            }
            if($movY != $dy){
                $this->motionY = 0;
            }
            if($movZ != $dz){
                $this->motionZ = 0;
            }
            Timings::$entityMoveTimer->stopTiming();
            return true;
        }
    }

	/**
	 * @param float $movX
	 * @param float $movY
	 * @param float $movZ
	 * @param float $dx
	 * @param float $dy
	 * @param float $dz
	 */
	protected function checkGroundState(float $movX, float $movY, float $movZ, float $dx, float $dy, float $dz){
		$this->isCollidedVertically = $movY != $dy;
		$this->isCollidedHorizontally = ($movX != $dx or $movZ != $dz);
		$this->isCollided = ($this->isCollidedHorizontally or $this->isCollidedVertically);
		$this->onGround = ($movY != $dy and $movY < 0);
	}

	/**
	 * @return array|null|Block[]
	 */
	public function getBlocksAround(){
		if($this->blocksAround === null){
			$minX = Math::floorFloat($this->boundingBox->minX);
			$minY = Math::floorFloat($this->boundingBox->minY);
			$minZ = Math::floorFloat($this->boundingBox->minZ);
			$maxX = Math::ceilFloat($this->boundingBox->maxX);
			$maxY = Math::ceilFloat($this->boundingBox->maxY);
			$maxZ = Math::ceilFloat($this->boundingBox->maxZ);

			$this->blocksAround = [];

			for($z = $minZ; $z <= $maxZ; ++$z){
				for($x = $minX; $x <= $maxX; ++$x){
					for($y = $minY; $y <= $maxY; ++$y){
						$block = $this->level->getBlockAt($x, $y, $z);
						if($block->hasEntityCollision()){
							$this->blocksAround[Level::blockHash($block->x, $block->y, $block->z)] = $block;
						}
					}
				}
			}
		}

		return $this->blocksAround;
	}

	protected function checkBlockCollision(){
		$vector = new Vector3(0, 0, 0);

		foreach($blocksaround = $this->getBlocksAround() as $block){
			$block->onEntityCollide($this);
			$block->addVelocityToEntity($this, $vector);
		}

		if($vector->lengthSquared() > 0){
			$vector = $vector->normalize();
			$d = 0.014;
			$this->motionX += $vector->x * $d;
			$this->motionY += $vector->y * $d;
			$this->motionZ += $vector->z * $d;
		}
	}

	/**
	 * @param Vector3 $pos
	 * @param         $yaw
	 * @param         $pitch
	 *
	 * @return bool
	 */
	public function setPositionAndRotation(Vector3 $pos, $yaw, $pitch){
		if($this->setPosition($pos) === true){
			$this->setRotation($yaw, $pitch);

			return true;
		}

		return false;
	}

	/**
	 * @param $yaw
	 * @param $pitch
	 */
	public function setRotation($yaw, $pitch){
		$this->yaw = $yaw;
		$this->pitch = $pitch;
		$this->scheduleUpdate();
	}

	protected function checkChunks(){
		if($this->chunk === null or ($this->chunk->getX() !== ($this->x >> 4) or $this->chunk->getZ() !== ($this->z >> 4))){
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->chunk = $this->level->getChunk($this->x >> 4, $this->z >> 4, true);

			if(!$this->justCreated){
				$newChunk = $this->level->getChunkPlayers($this->x >> 4, $this->z >> 4);
				foreach($this->hasSpawned as $player){
					if(!isset($newChunk[$player->getLoaderId()])){
						$this->despawnFrom($player);
					}else{
						unset($newChunk[$player->getLoaderId()]);
					}
				}
				foreach($newChunk as $player){
					$this->spawnTo($player);
				}
			}

			if($this->chunk === null){
				return;
			}

			$this->chunk->addEntity($this);
		}
	}

	/**
	 * @param Location $pos
	 *
	 * @return bool
	 */
	public function setLocation(Location $pos){
		if($this->closed){
			return false;
		}

		$this->setPositionAndRotation($pos, $pos->yaw, $pos->pitch);

		return true;
	}

	/**
	 * @param Vector3 $pos
	 *
	 * @return bool
	 */
	public function setPosition(Vector3 $pos){
		if($this->closed){
			return false;
		}

		if($pos instanceof Position and $pos->level !== null and $pos->level !== $this->level){
			if($this->switchLevel($pos->getLevel()) === false){
				return false;
			}
		}

		$this->x = $pos->x;
		$this->y = $pos->y;
		$this->z = $pos->z;

		$radius = $this->width / 2;
		$this->boundingBox->setBounds($pos->x - $radius, $pos->y, $pos->z - $radius, $pos->x + $radius, $pos->y + $this->height, $pos->z + $radius);

		$this->checkChunks();

		return true;
	}

	/**
	 * @return Vector3
	 */
	public function getMotion(){
		return new Vector3($this->motionX, $this->motionY, $this->motionZ);
	}

	/**
	 * @param Vector3 $motion
	 *
	 * @return bool
	 */
	public function setMotion(Vector3 $motion){
		if(!$this->justCreated){
			$this->server->getPluginManager()->callEvent($ev = new EntityMotionEvent($this, $motion));
			if($ev->isCancelled()){
				return false;
			}
		}

		$this->motionX = $motion->x;
		$this->motionY = $motion->y;
		$this->motionZ = $motion->z;

		if(!$this->justCreated){
			$this->updateMovement();
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isOnGround(){
		return $this->onGround === true;
	}

	public function kill(){
		$this->health = 0;
		$this->removeAllEffects();
		$this->scheduleUpdate();

		if($this->getLevel()->getServer()->expEnabled){
			$exp = mt_rand($this->getDropExpMin(), $this->getDropExpMax());
			if($exp > 0) $this->getLevel()->spawnXPOrb($this, $exp);
		}
	}

	/**
	 * @param Vector3|Position|Location $pos
	 * @param float|null                $yaw
	 * @param float|null                $pitch
	 *
	 * @return bool
	 */
	public function teleport(Vector3 $pos, $yaw = null, $pitch = null){
		if($pos instanceof Location){
			$yaw = $yaw ?? $pos->yaw;
			$pitch = $pitch ?? $pos->pitch;
		}
		$from = Position::fromObject($this, $this->level);
		$to = Position::fromObject($pos, $pos instanceof Position ? $pos->getLevel() : $this->level);
		$this->server->getPluginManager()->callEvent($ev = new EntityTeleportEvent($this, $from, $to));
		if($ev->isCancelled()){
			return false;
		}
		$this->ySize = 0;
		$pos = $ev->getTo();

		$this->setMotion($this->temporalVector->setComponents(0, 0, 0));
		if($this->setPositionAndRotation($pos, $yaw ?? $this->yaw, $pitch ?? $this->pitch) !== false){
			$this->resetFallDistance();
			$this->onGround = true;

			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;

			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;

			$this->updateMovement();

			return true;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	public function respawnToAll(){
		foreach($this->hasSpawned as $key => $player){
			unset($this->hasSpawned[$key]);
			$this->spawnTo($player);
		}
	}

	public function spawnToAll(){
		if($this->chunk === null or $this->closed){
			return;
		}
		foreach($this->level->getChunkPlayers($this->chunk->getX(), $this->chunk->getZ()) as $player){
			if($player->isOnline()){
				$this->spawnTo($player);
			}
		}
	}

	public function despawnFromAll(){
		foreach($this->hasSpawned as $player){
			$this->despawnFrom($player);
		}
	}

	public function close(){
		if(!$this->closed){
			$this->server->getPluginManager()->callEvent(new EntityDespawnEvent($this));
			$this->closed = true;
			$this->removeEffect(Effect::HEALTH_BOOST);
			$this->despawnFromAll();
			if($this->linkedType != 0){
				$this->linkedEntity->setLinked(0, $this);
			}
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
				$this->chunk = null;
			}
			if($this->getLevel() !== null){
				$this->getLevel()->removeEntity($this);
			}

			$this->namedtag = null;
		}

		if($this->attributeMap != null){
			$this->attributeMap = null;
		}
	}

    /**
     * @param $id
     * @param $type
     * @param $value
     * @param bool $send
     * @return bool
     */
    public function setDataProperty($id, $type, $value, $send = true){
		if($this->getDataProperty($id) !== $value){
			$this->dataProperties[$id] = [$type, $value];

			if($send) $this->sendData($this->hasSpawned, [$id => $this->dataProperties[$id]]);

			return true;
		}

		return false;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function linkEntity(Entity $entity){
		return $this->setLinked(1, $entity);
	}

	public function sendLinkedData(){
		if($this->linkedEntity instanceof Entity){
			$this->setLinked($this->linkedType, $this->linkedEntity);
		}
	}

	/**
	 * @param int    $type
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function setLinked($type = 0, Entity $entity){
		if($entity instanceof Boat or $entity instanceof Minecart){
			$this->setDataProperty(57, 8, [0, 1, 0]); //This is a fast hack for Boat. TODO: Improve it
		}

		if($type != 0 and $entity === null){
			return false;
		}
		if($entity === $this){
			return false;
		}
		switch($type){
			case 0:
				if($this->linkedType == 0){
					return true;
				}
				$this->linkedType = 0;
				$pk = new SetEntityLinkPacket();
				$pk->from = $entity->getId();
				$pk->to = $this->getId();
				$pk->type = 3;
				$this->server->broadcastPacket($this->level->getPlayers(), $pk);
				if($this instanceof Player){
					$pk = new SetEntityLinkPacket();
					$pk->from = $entity->getId();
					$pk->to = 0;
					$pk->type = 3;
					$this->dataPacket($pk);
				}
				if($this->linkedEntity->getLinkedType()){
					$this->linkedEntity->setLinked(0, $this);
				}
				$this->linkedEntity = null;

				return true;
			case 1:
				if(!$entity->isAlive()){
					return false;
				}
				$this->linkedEntity = $entity;
				$this->linkedType = 1;
				$entity->linkedEntity = $this;
				$entity->linkedType = 1;
				$pk = new SetEntityLinkPacket();
				$pk->from = $entity->getId();
				$pk->to = $this->getId();
				$pk->type = 2;
				$this->server->broadcastPacket($this->level->getPlayers(), $pk);
				if($this instanceof Player){
					$pk = new SetEntityLinkPacket();
					$pk->from = $entity->getId();
					$pk->to = 0;
					$pk->type = 2;
					$this->dataPacket($pk);
				}

				return true;
			case 2:
				if(!$entity->isAlive()){
					return false;
				}
				if($entity->getLinkedEntity() !== $this){
					return $entity->linkEntity($this);
				}
				$this->linkedEntity = $entity;
				$this->linkedType = 2;

				return true;
			default:
				return false;
		}
	}

	/**
	 * @return Entity
	 */
	public function getLinkedEntity(){
		return $this->linkedEntity;
	}

	/**
	 * @return null
	 */
	public function getLinkedType(){
		return $this->linkedType;
	}

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function getDataProperty($id){
		return isset($this->dataProperties[$id]) ? $this->dataProperties[$id][1] : null;
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public function getDataPropertyType($id){
		return isset($this->dataProperties[$id]) ? $this->dataProperties[$id][0] : null;
	}

	/**
	 * @param      $propertyId
	 * @param      $id
	 * @param bool $value
	 * @param int  $type
	 */
	public function setDataFlag($propertyId, $id, $value = true, $type = self::DATA_TYPE_LONG){
		if($this->getDataFlag($propertyId, $id) !== $value){
			$flags = (int) $this->getDataProperty($propertyId);
			$flags ^= 1 << $id;
			$this->setDataProperty($propertyId, $type, $flags);
		}
	}

	/**
	 * @param int $propertyId
	 * @param int $id
	 *
	 * @return bool
	 */
	public function getDataFlag($propertyId, $id){
		return (((int) $this->getDataProperty($propertyId)) & (1 << $id)) > 0;
	}

    /**
     * Wrapper around {@link Entity#getDataFlag} for generic data flag reading.
     *
     * @param int $flagId
     * @return bool
     */
    public function getGenericFlag(int $flagId) : bool{
        return $this->getDataFlag(self::DATA_FLAGS, $flagId);
    }

    /**
     * Wrapper around {@link Entity#setDataFlag} for generic data flag setting.
     *
     * @param int  $flagId
     * @param bool $value
     */
    public function setGenericFlag(int $flagId, bool $value = true){
        $this->setDataFlag(self::DATA_FLAGS, $flagId, $value, self::DATA_TYPE_LONG);
    }

	public function __destruct(){
		$this->close();
	}

	/**
	 * @param string        $metadataKey
	 * @param MetadataValue $metadataValue
	 */
	public function setMetadata(string $metadataKey, MetadataValue $metadataValue){
		$this->server->getEntityMetadata()->setMetadata($this, $metadataKey, $metadataValue);
	}

	/**
	 * @param string $metadataKey
	 *
	 * @return MetadataValue[]
	 */
	public function getMetadata(string $metadataKey){
		return $this->server->getEntityMetadata()->getMetadata($this, $metadataKey);
	}

	/**
	 * @param string $metadataKey
	 *
	 * @return bool
	 */
	public function hasMetadata(string $metadataKey) : bool {
		return $this->server->getEntityMetadata()->hasMetadata($this, $metadataKey);
	}

	/**
	 * @param string $metadataKey
	 * @param Plugin $plugin
	 */
	public function removeMetadata(string $metadataKey, Plugin $plugin){
		$this->server->getEntityMetadata()->removeMetadata($this, $metadataKey, $plugin);
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return (new \ReflectionClass($this))->getShortName() . "(" . $this->getId() . ")";
	}
	
	public function resetLastDamageCause(){
		$this->lastDamageCause = null;
	}
	
	public function isClosed() : bool{
		return $this->closed;
	}
	
	protected function resetLastMovements(){
		list($this->lastX, $this->lastY, $this->lastZ) = [$this->x, $this->y, $this->z];
		list($this->lastYaw, $this->lastPitch) = [$this->yaw, $this->pitch];
		list($this->lastMotionX, $this->lastMotionY, $this->lastMotionZ) = [$this->motionX, $this->motionY, $this->motionZ];
	}

	public function doesTriggerPressurePlate() : bool{
	    return false;
    }
    
    public function getAge() : int{
    	return $this->age;
    }
    
    public function setAge(int $age){
        $this->age = $age;
    }
}