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

/**
 * All the Item classes
 */

namespace pocketmine\item;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockToolType;
use pocketmine\entity\utils\FireworkUtils;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\entity\neutral\CaveSpider;
use pocketmine\entity\Entity;
use pocketmine\entity\neutral\ZombiePigman;
use pocketmine\entity\hostile\Silverfish;
use pocketmine\entity\hostile\Skeleton;
use pocketmine\entity\neutral\Spider;
use pocketmine\entity\hostile\Witch;
use pocketmine\entity\hostile\Zombie;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Binary;

class Item implements ItemIds, \JsonSerializable {

    const TAG_ENCH = "ench";
    const TAG_DISPLAY = "display";
    const TAG_BLOCK_ENTITY_TAG = "BlockEntityTag";

    const TAG_DISPLAY_NAME = "Name";
    const TAG_DISPLAY_LORE = "Lore";

	/** @var NBT */
	private static $cachedParser = null;

	/**
	 * @param string $tag
	 *
	 * @return CompoundTag
	 */
	private static function parseCompoundTag(string $tag) : CompoundTag{
        if($tag === ""){
            throw new \InvalidArgumentException("No NBT data found in supplied string");
        }

        if(self::$cachedParser === null){
            self::$cachedParser = new NBT(NBT::LITTLE_ENDIAN);
        }

        self::$cachedParser->read($tag);
        $data = self::$cachedParser->getData();

        if(!($data instanceof CompoundTag)){
            throw new \InvalidArgumentException("Invalid item NBT string given, it could not be deserialized");
        }

        return $data;
	}

	/**
	 * @param CompoundTag $tag
	 *
	 * @return string
	 */
	private static function writeCompoundTag(CompoundTag $tag) : string{
		if(self::$cachedParser === null){
			self::$cachedParser = new NBT(NBT::LITTLE_ENDIAN);
		}

		self::$cachedParser->setData($tag);
		return self::$cachedParser->write();
	}

    /**
     * Returns an instance of the Item with the specified id, meta, count and NBT.
     *
     * @param int                $id
     * @param int                $meta
     * @param int                $count
     * @param CompoundTag|string $tags
     *
     * @return Item
     * @throws \TypeError
     */
    public static function get(int $id, int $meta = 0, int $count = 1, $tags = "") : Item{
        if(!is_string($tags) and !($tags instanceof CompoundTag)){
            throw new \TypeError("`tags` argument must be a string or CompoundTag instance, " . (is_object($tags) ? "instance of " . get_class($tags) : gettype($tags)) . " given");
        }

        $item = null;
        try{
            if($id < 256){
                /* Blocks must have a damage value 0-15, but items can have damage value -1 to indicate that they are
                 * crafting ingredients with any-damage. */
                $item = new ItemBlock($id, $meta);
            }else{
                /** @var Item|null $listed */
                $listed = self::$list[$id];
                if($listed !== null){
                    $item = clone $listed;
                }
            }
        }catch(\RuntimeException $e){
            throw new \InvalidArgumentException("Item ID $id is invalid or out of bounds");
        }

        $item = ($item ?? new Item($id, $meta));

        $item->setDamage($meta);
        $item->setCount($count);
        $item->setCompoundTag($tags);
        return $item;
    }

    /**
     * @param string $str
     * @param bool   $multiple
     *
     * @return Item[]|Item
     */
    public static function fromString(string $str, bool $multiple = false){
        if($multiple === true){
            $blocks = [];
            foreach(explode(",", $str) as $b){
                $blocks[] = self::fromString($b, false);
            }

            return $blocks;
        }else{
            $b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($str)));
            if(!isset($b[1])){
                $meta = 0;
            }else{
                $meta = $b[1] & 32767;
            }

            if(defined(Item::class . "::" . strtoupper($b[0]))){
                $item = self::get(constant(Item::class . "::" . strtoupper($b[0])), $meta);
                if($item->getId() === self::AIR and strtoupper($b[0]) !== "AIR"){
                    $item = self::get($b[0] & 0xFFFF, $meta);
                }
            }else{
                $item = self::get($b[0] & 0xFFFF, $meta);
            }

            return $item;
        }
    }

	/**
	 * @param bool $readFromJson
	 */
	public static function init($readFromJson = false){
		if(self::$list === null){
            self::$list = new \SplFixedArray(65536);

            self::registerItem(new Shovel(Item::IRON_SHOVEL, 0, "Iron Shovel", TieredTool::TIER_IRON));
            self::registerItem(new Pickaxe(Item::IRON_PICKAXE, 0, "Iron Pickaxe", TieredTool::TIER_IRON));
            self::registerItem(new Axe(Item::IRON_AXE, 0, "Iron Axe", TieredTool::TIER_IRON));
		    self::registerItem(new FlintSteel());
		    self::registerItem(new Apple());
		    self::registerItem(new Bow());
		    self::registerItem(new Item(Item::ARROW, 0, "Arrow"));
		    self::registerItem(new Coal());
            self::registerItem(new Item(Item::DIAMOND, 0, "Diamond"));
            self::registerItem(new Item(Item::IRON_INGOT, 0, "Iron Ingot"));
            self::registerItem(new Item(Item::GOLD_INGOT, 0, "Gold Ingot"));
		    self::registerItem(new Sword(Item::IRON_SWORD, 0, "Iron Sword", TieredTool::TIER_IRON));
            self::registerItem(new Sword(Item::WOODEN_SWORD, 0, "Wooden Sword", TieredTool::TIER_WOODEN));
            self::registerItem(new Shovel(Item::WOODEN_SHOVEL, 0, "Wooden Shovel", TieredTool::TIER_WOODEN));
            self::registerItem(new Pickaxe(Item::WOODEN_PICKAXE, 0, "Wooden Pickaxe", TieredTool::TIER_WOODEN));
            self::registerItem(new Axe(Item::WOODEN_AXE, 0, "Wooden Axe", TieredTool::TIER_WOODEN));
            self::registerItem(new Sword(Item::STONE_SWORD, 0, "Stone Sword", TieredTool::TIER_STONE));
            self::registerItem(new Shovel(Item::STONE_SHOVEL, 0, "Stone Shovel", TieredTool::TIER_STONE));
            self::registerItem(new Pickaxe(Item::STONE_PICKAXE, 0, "Stone Pickaxe", TieredTool::TIER_STONE));
            self::registerItem(new Axe(Item::STONE_AXE, 0, "Stone Axe", TieredTool::TIER_STONE));
            self::registerItem(new Sword(Item::DIAMOND_SWORD, 0, "Diamond Sword", TieredTool::TIER_DIAMOND));
            self::registerItem(new Shovel(Item::DIAMOND_SHOVEL, 0, "Diamond Shovel", TieredTool::TIER_DIAMOND));
            self::registerItem(new Pickaxe(Item::DIAMOND_PICKAXE, 0, "Diamond Pickaxe", TieredTool::TIER_DIAMOND));
            self::registerItem(new Axe(Item::DIAMOND_AXE, 0, "Diamond Axe", TieredTool::TIER_DIAMOND));
            self::registerItem(new Stick());
            self::registerItem(new Bowl());
            self::registerItem(new Sword(Item::GOLD_SWORD, 0, "Gold Sword", TieredTool::TIER_GOLD));
            self::registerItem(new Shovel(Item::GOLDEN_SHOVEL, 0, "Gold Shovel", TieredTool::TIER_GOLD));
            self::registerItem(new Pickaxe(Item::GOLDEN_PICKAXE, 0, "Gold Pickaxe", TieredTool::TIER_GOLD));
            self::registerItem(new Axe(Item::GOLDEN_AXE, 0, "Gold Axe", TieredTool::TIER_GOLD));
            self::registerItem(new StringItem());
            self::registerItem(new Item(Item::FEATHER, 0, "Feather"));
            self::registerItem(new Item(Item::GUNPOWDER, 0, "Gunpowder"));
            self::registerItem(new Hoe(Item::WOODEN_HOE, 0, "Wooden Hoe", TieredTool::TIER_WOODEN));
            self::registerItem(new Hoe(Item::STONE_HOE, 0, "Stone Hoe", TieredTool::TIER_STONE));
            self::registerItem(new Hoe(Item::IRON_HOE, 0, "Iron Hoe", TieredTool::TIER_IRON));
            self::registerItem(new Hoe(Item::DIAMOND_HOE, 0, "Diamond Hoe", TieredTool::TIER_DIAMOND));
            self::registerItem(new Hoe(Item::GOLDEN_HOE, 0, "Golden Hoe", TieredTool::TIER_GOLD));
            self::registerItem(new WheatSeeds());
            self::registerItem(new Item(Item::WHEAT, 0, "Wheat"));
            self::registerItem(new Bread());
            self::registerItem(new LeatherCap());
            self::registerItem(new LeatherTunic());
            self::registerItem(new LeatherPants());
            self::registerItem(new LeatherBoots());
            self::registerItem(new ChainHelmet());
            self::registerItem(new ChainChestplate());
            self::registerItem(new ChainLeggings());
            self::registerItem(new ChainBoots());
            self::registerItem(new IronHelmet());
            self::registerItem(new IronChestplate());
            self::registerItem(new IronLeggings());
            self::registerItem(new IronBoots());
            self::registerItem(new DiamondHelmet());
            self::registerItem(new DiamondChestplate());
            self::registerItem(new DiamondLeggings());
            self::registerItem(new DiamondBoots());
            self::registerItem(new GoldHelmet());
            self::registerItem(new GoldChestplate());
            self::registerItem(new GoldLeggings());
            self::registerItem(new GoldBoots());
            self::registerItem(new Item(Item::FLINT, 0, "Flint"));
            self::registerItem(new RawPorkchop());
            self::registerItem(new CookedPorkchop());
            self::registerItem(new Painting());
            self::registerItem(new GoldenApple());
            self::registerItem(new Sign());
            self::registerItem(new ItemBlock(Block::OAK_DOOR_BLOCK, 0, Item::OAK_DOOR));
            self::registerItem(new Bucket());

            self::registerItem(new Minecart());
            // TODO : SADDLE
            self::registerItem(new ItemBlock(Block::IRON_DOOR_BLOCK, 0, Item::IRON_DOOR));
            self::registerItem(new Redstone());
            self::registerItem(new Snowball());
            self::registerItem(new Boat());
            self::registerItem(new Item(Item::LEATHER, 0, "Leather"));

            self::registerItem(new Item(Item::BRICK, 0, "Brick"));
            self::registerItem(new Item(Item::CLAY, 0, "Clay"));
            self::registerItem(new ItemBlock(Block::SUGARCANE_BLOCK, 0, Item::SUGARCANE));
            self::registerItem(new Item(Item::PAPER, 0, "Paper"));
            self::registerItem(new Item(Item::BOOK, 0, "Book"));
            self::registerItem(new Item(Item::SLIMEBALL, 0, "Slimeball"));
            // TODO : MINECART_WITH_CHEST

            self::registerItem(new Egg());
            self::registerItem(new Compass());
            self::registerItem(new FishingRod());
            self::registerItem(new Clock());
            self::registerItem(new Item(Item::GLOWSTONE_DUST, 0, "Glowstone Dust"));
            self::registerItem(new RawFish());
            self::registerItem(new CookedFish());
            self::registerItem(new Dye());
            self::registerItem(new Item(Item::BONE, 0, "Bone"));
            self::registerItem(new Item(Item::SUGAR, 0,"Sugar"));
            self::registerItem(new Cake());
            self::registerItem(new Bed());
            self::registerItem(new ItemBlock(Block::REPEATER_BLOCK, 0, Item::REPEATER));
            self::registerItem(new Cookie());
            // TODO : FILLED_MAP
            self::registerItem(new Shears());
            self::registerItem(new Melon());
            self::registerItem(new PumpkinSeeds());
            self::registerItem(new MelonSeeds());
            self::registerItem(new RawBeef());
            self::registerItem(new Steak());
            self::registerItem(new RawChicken());
            self::registerItem(new CookedChicken());
            self::registerItem(new RottenFlesh());
            self::registerItem(new EnderPearl());
            self::registerItem(new BlazeRod());
            self::registerItem(new Item(Item::GHAST_TEAR, 0, "Ghast Tear"));
            self::registerItem(new Item(Item::GOLD_NUGGET, 0, "Gold Nugget"));
            self::registerItem(new ItemBlock(Block::NETHER_WART_PLANT, 0, Item::NETHER_WART));
            self::registerItem(new Potion());
            self::registerItem(new GlassBottle());
            self::registerItem(new SpiderEye());
            self::registerItem(new Item(Item::FERMENTED_SPIDER_EYE, 0, "Fermented Spider Eye"));
            self::registerItem(new Item(Item::BLAZE_POWDER, 0, "Blaze Powder"));
            self::registerItem(new Item(Item::MAGMA_CREAM, 0, "Magma Cream"));
            self::registerItem(new ItemBlock(Block::BREWING_STAND_BLOCK, 0, Item::BREWING_STAND));
            self::registerItem(new ItemBlock(Block::CAULDRON_BLOCK, 0, Item::CAULDRON));
            self::registerItem(new EyeOfEnder());
            self::registerItem(new Item(Item::GLISTERING_MELON, 0, "Glistering Melon"));
            self::registerItem(new SpawnEgg());
            self::registerItem(new EnchantingBottle());
            self::registerItem(new FireCharge());
            self::registerItem(new WritableBook());
            self::registerItem(new WrittenBook());
            self::registerItem(new Item(Item::EMERALD, 0, "Emerald"));
            self::registerItem(new ItemBlock(Block::ITEM_FRAME_BLOCK, 0,Item::ITEM_FRAME));
            self::registerItem(new ItemBlock(Block::FLOWER_POT_BLOCK, 0,Item::FLOWER_POT));
            self::registerItem(new Carrot());
            self::registerItem(new Potato());
            self::registerItem(new BakedPotato());
            self::registerItem(new PoisonousPotato());
            // TODO : EMPTY_MAP
            self::registerItem(new GoldenCarrot());
            self::registerItem(new ItemBlock(Block::SKULL_BLOCK, 0, Item::SKULL));
            // TODO : CARROT_ON_A_STICK
            self::registerItem(new Item(Item::NETHER_STAR, 0, "Nether Star"));
            self::registerItem(new PumpkinPie());
            self::registerItem(new FireworkRocket());
            // TODO : FIREWORK_STAR
            self::registerItem(new EnchantedBook());
            self::registerItem(new ItemBlock(Block::COMPARATOR_BLOCK, 0, Item::COMPARATOR));
            self::registerItem(new Item(Item::NETHER_BRICK, 0, "Nether Brick"));
            self::registerItem(new Item(Item::NETHER_QUARTZ, 0, "Nether Quartz"));
            // TODO : MINECART_WITH_TNT
            // TODO : MINECART_WITH_HOPPER
            self::registerItem(new Item(Item::PRISMARINE_SHARD, 0, "Prismarine Shard"));
            self::registerItem(new ItemBlock(Block::HOPPER_BLOCK, 0, Item::HOPPER));
            self::registerItem(new RawRabbit());
            self::registerItem(new CookedRabbit());
            self::registerItem(new RabbitStew());
            self::registerItem(new Item(Item::RABBIT_FOOT, 0, "Rabbit's Foot"));
            self::registerItem(new Item(Item::RABBIT_HIDE, 0, "Rabbit Hide"));
            // TODO : LEATHER_HORSE_ARMOR
            // TODO : IRON_HORSE_ARMOR
            // TODO : GOLD_HORSE_ARMOR
            // TODO : DIAMOND_HORSE_ARMOR
            // TODO : LEAD
            // TODO : NAMETAG
            self::registerItem(new Item(Item::PRISMARINE_CRYSTALS, 0, "Prismarine Crystals"));
            self::registerItem(new RawMutton());
            self::registerItem(new CookedMutton());
            self::registerItem(new ArmorStand());
            // TODO : END_CRYSTAL
            self::registerItem(new ItemBlock(Block::SPRUCE_DOOR_BLOCK, 0, Item::SPRUCE_DOOR));
            self::registerItem(new ItemBlock(Block::BIRCH_DOOR_BLOCK, 0, Item::BIRCH_DOOR));
            self::registerItem(new ItemBlock(Block::JUNGLE_DOOR_BLOCK, 0, Item::JUNGLE_DOOR));
            self::registerItem(new ItemBlock(Block::ACACIA_DOOR_BLOCK, 0, Item::ACACIA_DOOR));
            self::registerItem(new ItemBlock(Block::DARK_OAK_DOOR_BLOCK, 0, Item::DARK_OAK_DOOR));
            self::registerItem(new ChorusFruit());
            self::registerItem(new Item(Item::CHORUS_FRUIT_POPPED, 0, "Popped Chorus Fruit"));

            self::registerItem(new DragonsBreath());
            self::registerItem(new SplashPotion());

            self::registerItem(new LingeringPotion());

            //TODO: COMMAND_BLOCK_MINECART
            self::registerItem(new Elytra());
            self::registerItem(new Item(Item::SHULKER_SHELL, 0, "Shulker Shell"));
            self::registerItem(new Banner());

            self::registerItem(new TotemOfUndying());

            self::registerItem(new Item(Item::IRON_NUGGET, 0, "Iron Nugget")); // Iron Nugget

            self::registerItem(new Beetroot());
            self::registerItem(new ItemBlock(Block::BEETROOT_BLOCK, 0, Item::BEETROOT_SEED));
            self::registerItem(new BeetrootSoup());
            self::registerItem(new RawSalmon());
            self::registerItem(new Clownfish());
            self::registerItem(new Pufferfish());
            self::registerItem(new CookedSalmon());

            self::registerItem(new EnchantedGoldenApple());

            self::registerItem(new MusicDisc13());
            self::registerItem(new MusicDiscCat());
            self::registerItem(new MusicDiscBlocks());
            self::registerItem(new MusicDiscChirp());
            self::registerItem(new MusicDiscFar());
            self::registerItem(new MusicDiscMall());
            self::registerItem(new MusicDiscMellohi());
            self::registerItem(new MusicDiscStal());
            self::registerItem(new MusicDiscStrad());
            self::registerItem(new MusicDiscWard());
            self::registerItem(new MusicDisc11());
            self::registerItem(new MusicDiscWait());
		}

		self::initCreativeItems();
	}

    /**
     * Registers an item type into the index. Plugins may use this method to register new item types or override existing
     * ones.
     *
     * NOTE: If you are registering a new item type, you will need to add it to the creative inventory yourself - it
     * will not automatically appear there.
     *
     * @param Item $item
     * @param bool $override
     *
     * @throws \RuntimeException if something attempted to override an already-registered item without specifying the
     * $override parameter.
     */
    public static function registerItem(Item $item, bool $override = false){
        $id = $item->getId();
        if(!$override and self::isRegistered($id)){
            throw new \RuntimeException("Trying to overwrite an already registered item : ".$id);
        }

        self::$list[$id] = clone $item;
    }

    /**
     * Returns whether the specified item ID is already registered in the item factory.
     *
     * @param int $id
     * @return bool
     */
    public static function isRegistered(int $id) : bool{
        if($id < 256){
            return BlockFactory::isRegistered($id);
        }
        return isset(self::$list[$id]);
    }

	private static $creative = [];

	public static function initCreativeItems(){
		self::clearCreativeItems();

		$degerler = ["id" => 0, "meta" => 0, "count" => 1, "ench" => []];
		foreach (CreativeItems::ITEMS as $itemdata){
		    foreach ($degerler as $deger => $standart){
		        if(empty($itemdata[$deger])){
		            $itemdata[$deger] = $standart;
                }
            }
            $item = Item::get($itemdata["id"], $itemdata["meta"], $itemdata["count"]);
            if(is_array($itemdata["ench"]) && count($itemdata["ench"]) > 0){
                foreach($itemdata["ench"] as $ench){
                    $enchant = Enchantment::getEnchantment($ench["id"]);
                    if($enchant !== null) $enchant = new EnchantmentInstance($enchant, $ench["lvl"]);
                    $item->addEnchantment($enchant);
                }
            }
            if($item->getName() === "Unknown"){
                continue;
            }
            self::addCreativeItem($item);
        }

        $item = Item::get(Item::FIREWORK);
        $expTags = [];
        $expTags[] = FireworkUtils::createExplosion(0, 1, true, false, 0);
        $expTags[] = FireworkUtils::createExplosion(1, 2, true, false, 1);
        $expTags[] = FireworkUtils::createExplosion(2, 3, true, false, 2);
        $expTags[] = FireworkUtils::createExplosion(3, 4, true, false, 3);
        $expTags[] = FireworkUtils::createExplosion(4, 5, true, false, 4);
        $item->setNamedTag(FireworkUtils::createNBT(2, $expTags));

        self::addCreativeItem($item);
	}

	public static function clearCreativeItems(){
		Item::$creative = [];
	}

	/**
	 * @return array
	 */
	public static function getCreativeItems() : array{
		return Item::$creative;
	}

	/**
	 * @param Item $item
	 */
	public static function addCreativeItem(Item $item){
        Item::$creative[] = clone $item;
	}

	/**
	 * @param Item $item
	 */
	public static function removeCreativeItem(Item $item){
		$index = self::getCreativeItemIndex($item);
		if($index !== -1){
			unset(Item::$creative[$index]);
		}
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public static function isCreativeItem(Item $item) : bool{
		foreach(Item::$creative as $i => $d){
			if($item->equals($d, !$item->isTool())){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $index
	 *
	 * @return Item
	 */
	public static function getCreativeItem(int $index){
		return isset(Item::$creative[$index]) ? Item::$creative[$index] : null;
	}

	/**
	 * @param Item $item
	 *
	 * @return int
	 */
	public static function getCreativeItemIndex(Item $item) : int{
		foreach(Item::$creative as $i => $d){
			if($item->equals($d, !$item->isTool())){
				return $i;
			}
		}

		return -1;
	}

    /** @var \SplFixedArray */
    private static $list = null;
    /** @var Block|null */
    protected $block;
    /** @var int */
    protected $id;
    /** @var int */
    protected $meta;
    /** @var string */
    private $tags = "";
    /** @var CompoundTag|null */
    private $cachedNBT = null;
    /** @var int */
    public $count = 1;
    /** @var string */
    protected $name;

    /**
     * Constructs a new Item type. This constructor should ONLY be used when constructing a new item TYPE to register
     * into the index.
     *
     * @param int $id
     * @param int $meta
     * @param string $name
     */
    public function __construct(int $id, int $meta = 0, string $name = "Unknown"){
        $this->id = $id & 0xffff;
        $this->setDamage($meta);
        $this->name = $name;
        if(!isset($this->block) and $this->id <= 0xff){
            $this->block = BlockFactory::get($this->id, $this->meta);
            $this->name = $this->block->getName();
        }
    }

    /**
     * Sets the Item's NBT
     *
     * @param CompoundTag|string $tags
     *
     * @return Item
     */
	public function setCompoundTag($tags){
		if($tags instanceof CompoundTag){
			$this->setNamedTag($tags);
		}else{
			$this->tags = (string) $tags;
			$this->cachedNBT = null;
		}

		return $this;
	}

    /**
     * Returns the serialized NBT of the Item
     * @return string
     */
    public function getCompoundTag() : string{
        return $this->tags;
    }

    /**
     * Returns whether this Item has a non-empty NBT.
     * @return bool
     */
    public function hasCompoundTag() : bool{
        return $this->tags !== "";
    }

    /**
     * @return bool
     */
    public function hasCustomBlockData() : bool{
        return $this->getNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG) instanceof CompoundTag;
    }

    public function clearCustomBlockData(){
        $this->removeNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG);
        return $this;
    }

    /**
     * @param CompoundTag $compound
     *
     * @return Item
     */
    public function setCustomBlockData(CompoundTag $compound) : Item{
        $tags = clone $compound;
        $tags->setName(self::TAG_BLOCK_ENTITY_TAG);
        $this->setNamedTagEntry($tags);

        return $this;
    }

    /**
     * @return CompoundTag|null
     */
    public function getCustomBlockData(){
        $tag = $this->getNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG);
        return $tag instanceof CompoundTag ? $tag : null;
    }

    /**
     * @return bool
     */
    public function hasEnchantments() : bool{
        return $this->getNamedTagEntry(self::TAG_ENCH) instanceof ListTag;
    }

    /**
     * @param int $id
     * @param int $level
     *
     * @return bool
     */
    public function hasEnchantment(int $id, int $level = -1) : bool{
        $ench = $this->getNamedTagEntry(self::TAG_ENCH);
        if(!($ench instanceof ListTag)){
            return false;
        }

        /** @var CompoundTag $entry */
        foreach($ench as $entry){
            if($entry->getShort("id") === $id and ($level === -1 or $entry->getShort("lvl") === $level)){
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $id
     *
     * @return EnchantmentInstance|null
     */
    public function getEnchantment(int $id){
        $ench = $this->getNamedTagEntry(self::TAG_ENCH);
        if(!($ench instanceof ListTag)){
            return null;
        }

        /** @var CompoundTag $entry */
        foreach($ench as $entry){
            if($entry->getShort("id") === $id){
                $e = Enchantment::getEnchantment($entry->getShort("id"));
                if($e !== null){
                    return new EnchantmentInstance($e, $entry->getShort("lvl"));
                }
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @param int $level
     */
    public function removeEnchantment(int $id, int $level = -1){
        $ench = $this->getNamedTagEntry(self::TAG_ENCH);
        if(!($ench instanceof ListTag)){
            return;
        }

        /** @var CompoundTag $entry */
        foreach($ench as $k => $entry){
            if($entry->getShort("id") === $id and ($level === -1 or $entry->getShort("lvl") === $level)){
                unset($ench[$k]);
                break;
            }
        }

        $this->setNamedTagEntry($ench);
    }

    public function removeEnchantments(){
        $this->removeNamedTagEntry(self::TAG_ENCH);
    }

    /**
     * @param EnchantmentInstance $enchantment
     */
    public function addEnchantment(EnchantmentInstance $enchantment){
        $found = false;

        $ench = $this->getNamedTagEntry(self::TAG_ENCH);
        if(!($ench instanceof ListTag)){
            $ench = new ListTag(self::TAG_ENCH, [], NBT::TAG_Compound);
        }else{
            /** @var CompoundTag $entry */
            foreach($ench as $k => $entry){
                if($entry->getShort("id") === $enchantment->getId()){
                    $ench[$k] = new CompoundTag("", [
                        new ShortTag("id", $enchantment->getId()),
                        new ShortTag("lvl", $enchantment->getLevel())
                    ]);
                    $found = true;
                    break;
                }
            }
        }

        if(!$found){
            $ench[count($ench)] = new CompoundTag("", [
                new ShortTag("id", $enchantment->getId()),
                new ShortTag("lvl", $enchantment->getLevel())
            ]);
        }

        $this->setNamedTagEntry($ench);
    }

    /**
     * @return EnchantmentInstance[]
     */
    public function getEnchantments() : array{
        /** @var EnchantmentInstance[] $enchantments */
        $enchantments = [];

        $ench = $this->getNamedTagEntry(self::TAG_ENCH);
        if($ench instanceof ListTag){
            /** @var CompoundTag $entry */
            foreach($ench as $entry){
                $e = Enchantment::getEnchantment($entry->getShort("id"));
                if($e !== null){
                    $enchantments[] = new EnchantmentInstance($e, $entry->getShort("lvl"));
                }
            }
        }

        return $enchantments;
    }

    /**
     * @return bool
     */
    public function hasCustomName() : bool{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if($display instanceof CompoundTag){
            return $display->hasTag(self::TAG_DISPLAY_NAME);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCustomName() : string{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if($display instanceof CompoundTag){
            return $display->getString(self::TAG_DISPLAY_NAME, "");
        }

        return "";
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    public function setCustomName(string $name) : Item{
        if($name === ""){
            $this->clearCustomName();
        }

        /** @var CompoundTag $display */
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if(!($display instanceof CompoundTag)){
            $display = new CompoundTag(self::TAG_DISPLAY);
        }

        $display->setString(self::TAG_DISPLAY_NAME, $name);
        $this->setNamedTagEntry($display);

        return $this;
    }

    /**
     * @return Item
     */
    public function clearCustomName() : Item{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if($display instanceof CompoundTag){
            $display->removeTag(self::TAG_DISPLAY_NAME);

            if($display->getCount() === 0){
                $this->removeNamedTagEntry($display->getName());
            }else{
                $this->setNamedTagEntry($display);
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLore() : array{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if($display instanceof CompoundTag and ($lore = $display->getListTag(self::TAG_DISPLAY_LORE)) !== null){
            return $lore->getAllValues();
        }

        return [];
    }

    /**
     * @param string[] $lines
     *
     * @return Item
     */
    public function setLore(array $lines) : Item{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if(!($display instanceof CompoundTag)){
            $display = new CompoundTag(self::TAG_DISPLAY, []);
        }

        $display->setTag(new ListTag(self::TAG_DISPLAY_LORE, array_map(function(string $str) : StringTag{
            return new StringTag("", $str);
        }, $lines), NBT::TAG_String));

        $this->setNamedTagEntry($display);

        return $this;
    }

    /**
     * @param string $name
     * @return NamedTag|null
     */
    public function getNamedTagEntry(string $name){
        return $this->getNamedTag()->getTag($name);
    }

    public function setNamedTagEntry(NamedTag $new){
        $tag = $this->getNamedTag();
        $tag->setTag($new);
        $this->setNamedTag($tag);
    }

    public function removeNamedTagEntry(string $name){
        $tag = $this->getNamedTag();
        $tag->removeTag($name);
        $this->setNamedTag($tag);
    }

    /**
     * Returns a tree of Tag objects representing the Item's NBT. If the item does not have any NBT, an empty CompoundTag
     * object is returned to allow the caller to manipulate and apply back to the item.
     *
     * @return CompoundTag
     */
    public function getNamedTag() : CompoundTag{
        if(!$this->hasCompoundTag() and $this->cachedNBT === null){
            $this->cachedNBT = new CompoundTag();
        }

        return $this->cachedNBT ?? ($this->cachedNBT = self::parseCompoundTag($this->tags));
    }

    /**
     * Sets the Item's NBT from the supplied CompoundTag object.
     * @param CompoundTag $tag
     *
     * @return Item
     */
    public function setNamedTag(CompoundTag $tag) : Item{
        if($tag->getCount() === 0){
            return $this->clearNamedTag();
        }

        $this->cachedNBT = $tag;
        $this->tags = self::writeCompoundTag($tag);

        return $this;
    }

    /**
     * Removes the Item's NBT.
     * @return Item
     */
    public function clearNamedTag() : Item{
        return $this->setCompoundTag("");
    }

    /**
     * @return int
     */
    public function getCount() : int{
        return $this->count;
    }

    /**
     * @param int $count
     * @return Item
     */
    public function setCount(int $count) : Item{
        $this->count = $count;

        return $this;
    }

    /**
     * Pops an item from the stack and returns it, decreasing the stack count of this item stack by one.
     * @return Item
     *
     * @throws \InvalidStateException if the count is less than or equal to zero, or if the stack is air.
     */
    public function pop() : Item{
        if($this->isNull()){
            throw new \InvalidStateException("Cannot pop an item from a null stack");
        }

        $item = clone $this;
        $item->setCount(1);

        $this->count--;

        return $item;
    }

    public function isNull() : bool{
        return $this->count <= 0 or $this->id === Item::AIR;
    }

    /**
     * Returns the name of the item, or the custom name if it is set.
     * @return string
     */
    final public function getName() : string{
        return $this->hasCustomName() ? $this->getCustomName() : $this->name;
    }

    /**
     * @return bool
     */
    final public function canBePlaced() : bool{
        return $this->block !== null and $this->block->canBePlaced();
    }

    /**
     * Returns the block corresponding to this Item.
     * @return Block
     */
    public function getBlock() : Block{
        if($this->block instanceof Block){
            return clone $this->block;
        }else{
            return BlockFactory::get(Block::AIR);
        }
    }

    /**
     * @return int
     */
    final public function getId() : int{
        return $this->id;
    }

    /**
     * @return int
     */
    final public function getDamage() : int{
        return $this->meta;
    }

    /**
     * @param int $meta
     * @return Item
     */
    public function setDamage(int $meta) : Item{
        $this->meta = $meta !== -1 ? $meta & 0x7FFF : -1;

        return $this;
    }

    /**
     * Returns whether this item can match any item with an equivalent ID with any meta value.
     * Used in crafting recipes which accept multiple variants of the same item, for example crafting tables recipes.
     *
     * @return bool
     */
    public function hasAnyDamageValue() : bool{
        return $this->meta === -1;
    }

    /**
     * Returns the highest amount of this item which will fit into one inventory slot.
     * @return int
     */
    public function getMaxStackSize() : int{
        return 64;
    }

    /**
     * Returns the time in ticks which the item will fuel a furnace for.
     * @return int
     */
    public function getFuelTime() : int{
        return 0;
    }

    /**
     * Returns how many points of damage this item will deal to an entity when used as a weapon.
     * @return int
     */
    public function getAttackPoints() : int{
        return 1;
    }

    /**
     * Returns how many armor points can be gained by wearing this item.
     * @return int
     */
    public function getDefensePoints() : int{
        return 0;
    }

    /**
     * @param Entity|Block $object
     *
     * @return bool
     */
    public function useOn($object){
        return false;
    }

    /**
     * @return bool
     */
    public function isTool(){
        return false;
    }

    /**
     * Returns what type of block-breaking tool this is. Blocks requiring the same tool type as the item will break
     * faster (except for blocks requiring no tool, which break at the same speed regardless of the tool used)
     *
     * @return int
     */
    public function getBlockToolType() : int{
        return BlockToolType::TYPE_NONE;
    }

    /**
     * Returns the harvesting power that this tool has. This affects what blocks it can mine when the tool type matches
     * the mined block.
     * This should return 1 for non-tiered tools, and the tool tier for tiered tools.
     *
     * @see Block::getToolHarvestLevel()
     *
     * @return int
     */
    public function getBlockToolHarvestLevel() : int{
        return 0;
    }

    /**
     * Returns the maximum amount of damage this item can take before it breaks.
     *
     * @return int|bool
     */
    public function getMaxDurability(){
        return false;
    }

    public function isPickaxe(){
        return false;
    }

    public function isAxe(){
        return false;
    }

    public function isSword(){
        return false;
    }

    public function isShovel(){
        return false;
    }

    public function isHoe(){
        return false;
    }

    public function isShears(){
        return false;
    }

    public function getMiningEfficiency(Block $block) : float{
        return 1;
    }

    /**
     * Called when a player uses this item on a block.
     *
     * @param Level   $level
     * @param Player  $player
     * @param Block   $blockReplace
     * @param Block   $blockClicked
     * @param int     $face
     * @param Vector3 $clickVector
     *
     * @return bool
     */
    public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool{
        return false;
    }

    /**
     * Called when a player uses the item on air, for example throwing a projectile.
     * Returns whether the item was changed, for example count decrease or durability change.
     *
     * @param Player  $player
     * @param Vector3 $directionVector
     *
     * @return bool
     */
    public function onClickAir(Player $player, Vector3 $directionVector) : bool{
        return false;
    }

    /**
     * Called when a player is using this item and releases it. Used to handle bow shoot actions.
     * Returns whether the item was changed, for example count decrease or durability change.
     *
     * @param Player $player
     * @return bool
     */
    public function onReleaseUsing(Player $player) : bool{
        return false;
    }

    /**
     * Compares an Item to this Item and check if they match.
     *
     * @param Item $item
     * @param bool $checkDamage Whether to verify that the damage values match.
     * @param bool $checkCompound Whether to verify that the items' NBT match.
     *
     * @return bool
     */
    final public function equals(Item $item, bool $checkDamage = true, bool $checkCompound = true) : bool{
        if($this->id === $item->getId() and ($checkDamage === false or $this->getDamage() === $item->getDamage())){
            if($checkCompound){
                if($item->getCompoundTag() === $this->getCompoundTag()){
                    return true;
                }elseif($this->hasCompoundTag() and $item->hasCompoundTag()){
                    //Serialized NBT didn't match, check the cached object tree.
                    return NBT::matchTree($this->getNamedTag(), $item->getNamedTag());
                }
            }else{
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether the specified item stack has the same ID, damage, NBT and count as this item stack.
     * @param Item $other
     *
     * @return bool
     */
    final public function equalsExact(Item $other) : bool{
        return $this->equals($other, true, true) and $this->count === $other->count;
    }

    /**
     * @deprecated Use {@link Item#equals} instead, this method will be removed in the future.
     *
     * @param Item $item
     * @param bool $checkDamage
     * @param bool $checkCompound
     *
     * @return bool
     */
    final public function deepEquals(Item $item, bool $checkDamage = true, bool $checkCompound = true) : bool{
        return $this->equals($item, $checkDamage, $checkCompound);
    }

    /**
     * @return string
     */
    final public function __toString() : string{
        return "Item " . $this->name . " (" . $this->id . ":" . ($this->hasAnyDamageValue() ? "?" : $this->meta) . ")x" . $this->count . ($this->hasCompoundTag() ? " tags:0x" . bin2hex($this->getCompoundTag()) : "");
    }

    /**
     * Returns an array of item stack properties that can be serialized to json.
     *
     * @return array
     */
    final public function jsonSerialize() : array{
        $data = [
            "id" => $this->getId()
        ];

        if($this->getDamage() !== 0){
            $data["damage"] = $this->getDamage();
        }

        if($this->getCount() !== 1){
            $data["count"] = $this->getCount();
        }

        if($this->hasCompoundTag()){
            $data["nbt_hex"] = bin2hex($this->getCompoundTag());
        }

        return $data;
    }

    /**
     * Returns an Item from properties created in an array by {@link Item#jsonSerialize}
     *
     * @param array $data
     * @return Item
     */
    final public static function jsonDeserialize(array $data) : Item{
        return Item::get(
            (int) $data["id"],
            (int) ($data["damage"] ?? 0),
            (int) ($data["count"] ?? 1),
            (string) ($data["nbt"] ?? (isset($data["nbt_hex"]) ? hex2bin($data["nbt_hex"]) : "")) //`nbt` key might contain old raw data
        );
    }

    /**
     * Serializes the item to an NBT CompoundTag
     *
     * @param int    $slot optional, the inventory slot of the item
     * @param string $tagName the name to assign to the CompoundTag object
     *
     * @return CompoundTag
     */
    public function nbtSerialize(int $slot = -1, string $tagName = "") : CompoundTag{
        $result = new CompoundTag($tagName, [
            new ShortTag("id", PHP_INT_SIZE === 8 ? Binary::signShort($this->id) : $this->id),
            new ByteTag("Count", PHP_INT_SIZE === 8 ? Binary::signByte($this->count) : $this->count ?? -1),
            new ShortTag("Damage", $this->meta)
        ]);

        if($this->hasCompoundTag()){
            $itemNBT = clone $this->getNamedTag();
            $itemNBT->setName("tag");
            $result->setTag($itemNBT);
        }

        if($slot !== -1){
            $result->setByte("Slot", $slot);
        }

        return $result;
    }

    /**
     * Deserializes an Item from an NBT CompoundTag
     *
     * @param CompoundTag $tag
     *
     * @return Item
     */
    public static function nbtDeserialize(CompoundTag $tag) : Item{
        if(!$tag->hasTag("id") or !$tag->hasTag("Count")){
            return Item::get(0);
        }

        $count = Binary::unsignByte($tag->getByte("Count"));
        $meta = $tag->getShort("Damage", 0);

        $idTag = $tag->getTag("id");
        if($idTag instanceof ShortTag){
            $item = Item::get(Binary::unsignShort($idTag->getValue()), $meta, $count);
        }elseif($idTag instanceof StringTag){ //PC item save format
            $item = Item::fromString($idTag->getValue());
            $item->setDamage($meta);
            $item->setCount($count);
        }else{
            throw new \InvalidArgumentException("Item CompoundTag ID must be an instance of StringTag or ShortTag, " . get_class($idTag) . " given");
        }

        $itemNBT = $tag->getCompoundTag("tag");
        if($itemNBT instanceof CompoundTag){
            /** @var CompoundTag $t */
            $t = clone $itemNBT;
            $t->setName("");
            $item->setNamedTag($t);
        }

        return $item;
    }

    public function __clone(){
        if($this->block !== null){
            $this->block = clone $this->block;
        }

        $this->cachedNBT = null;
    }

    // TURANIC

	/**
	 * @param $id
	 *
	 * @return Int level|0(for null)
	 */
	public function getEnchantmentLevel(int $id){
        if(!$this->hasEnchantments()){
            return 0;
        }

        foreach($this->getNamedTag()->ench as $entry){
            if($entry["id"] === $id){
                $e = Enchantment::getEnchantment($entry["id"]);
                $e = new EnchantmentInstance($e, $entry["lvl"]);
                $e->setLevel($entry["lvl"]);
                $E_level = $e->getLevel() > Enchantment::getEnchantMaxLevel($id) ? Enchantment::getEnchantMaxLevel($id) : $e->getLevel();
                return $E_level;
            }
        }

        return 0;
	}

	/**
	 * @return bool
	 */
	public function hasRepairCost() : bool{
	    $tag = $this->getNamedTag();
		if($tag->hasTag("RepairCost", IntTag::class)){
			return true;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getRepairCost() : int{
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if($display instanceof CompoundTag){
            if($display->hasTag("RepairCost")){
                return $display->getInt("RepairCost");
            }
        }

		return 1;
	}


	/**
	 * @param int $cost
	 *
	 * @return $this
	 */
	public function setRepairCost(int $cost){
		if($cost === 1){
			$this->clearRepairCost();
		}

        /** @var CompoundTag $display */
        $display = $this->getNamedTagEntry(self::TAG_DISPLAY);
        if(!($display instanceof CompoundTag)){
            $display = new CompoundTag(self::TAG_DISPLAY);
        }

        $display->setInt("RepairCost", $cost);
        $this->setNamedTagEntry($display);

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearRepairCost(){
        $this->removeNamedTagEntry("RepairCost");

        return $this;
	}

	/**
	 * @return bool
	 */
	public function isArmor(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isBoots(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isHelmet(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isLeggings(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isChestplate(){
		return false;
	}

	/**
	 * @param Entity $target
	 *
	 * @return float|int
	 */
	public function getModifyAttackDamage(Entity $target){
		$rec = $this->getAttackPoints();
		$sharpL = $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_SHARPNESS);
		if($sharpL > 0){
			$rec += 0.5 * ($sharpL + 1);
		}

		if($target instanceof Skeleton or $target instanceof Zombie or
			$target instanceof Witch or $target instanceof ZombiePigman
		){
			//SMITE    wither skeletons
			$rec += 2.5 * $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_SMITE);

		}elseif($target instanceof Spider or $target instanceof CaveSpider or
			$target instanceof Silverfish
		){
			//Bane of Arthropods    wither skeletons
			$rec += 2.5 * $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_ARTHROPODS);

		}
		return $rec;
	}

}