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
 * All the Item classes
 */

namespace pocketmine\item;

use pocketmine\math\Vector3;
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
use pocketmine\inventory\FurnaceFuel;

class Item implements ItemIds, \JsonSerializable {

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


	/** @var \SplFixedArray */
	public static $list = null;
	protected $block;
	protected $id;
	protected $meta;
	private $tags = "";
	private $cachedNBT = null;
	public $count;
	protected $durability = 0;
	protected $name;

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return false;
	}

	/**
	 * @param bool $readFromJson
	 */
	public static function init($readFromJson = false){
		if(self::$list === null){

		    self::registerItem(new IronShovel());
		    self::registerItem(new IronPickaxe());
		    self::registerItem(new IronAxe());
		    self::registerItem(new FlintSteel());
		    self::registerItem(new Apple());
		    self::registerItem(new Bow());
		    self::registerItem(new Arrow());
		    self::registerItem(new Coal());
		    self::registerItem(new Diamond());
		    self::registerItem(new IronIngot());
		    self::registerItem(new GoldIngot());
		    self::registerItem(new IronSword());
		    self::registerItem(new WoodenSword());
		    self::registerItem(new WoodenShovel());
		    self::registerItem(new WoodenPickaxe());
		    self::registerItem(new WoodenAxe());
            self::registerItem(new StoneSword());
            self::registerItem(new StoneShovel());
            self::registerItem(new StonePickaxe());
            self::registerItem(new StoneAxe());
            self::registerItem(new DiamondSword());
            self::registerItem(new DiamondShovel());
            self::registerItem(new DiamondPickaxe());
            self::registerItem(new DiamondAxe());
            self::registerItem(new Stick());
            self::registerItem(new Bowl());
            self::registerItem(new GoldSword());
            self::registerItem(new GoldShovel());
            self::registerItem(new GoldPickaxe());
            self::registerItem(new GoldAxe());
            self::registerItem(new ItemString());
            self::registerItem(new Feather());
            self::registerItem(new Gunpowder());
            self::registerItem(new WoodenHoe());
            self::registerItem(new StoneHoe());
            self::registerItem(new DiamondHoe());
            self::registerItem(new GoldHoe());
            self::registerItem(new WheatSeeds());
            self::registerItem(new Wheat());
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
            self::registerItem(new Flint());
            self::registerItem(new RawPorkchop());
            self::registerItem(new CookedPorkchop());
            self::registerItem(new Painting());
            self::registerItem(new GoldenApple());
            self::registerItem(new Sign());
            self::registerItem(new WoodenDoor());
            self::registerItem(new Bucket());

            self::registerItem(new Minecart());
            // TODO : SADDLE
            self::registerItem(new IronDoor());
            self::registerItem(new Redstone());
            self::registerItem(new Snowball());
            self::registerItem(new Boat());
            self::registerItem(new Leather());

            self::registerItem(new Brick());
            self::registerItem(new Clay());
            self::registerItem(new Sugarcane());
            self::registerItem(new Paper());
            self::registerItem(new Book());
            self::registerItem(new Slimeball());
            // TODO : MINECART_WITH_CHEST

            self::registerItem(new Egg());
            self::registerItem(new Compass());
            self::registerItem(new FishingRod());
            self::registerItem(new Clock());
            self::registerItem(new GlowstoneDust());
            self::registerItem(new Fish());
            self::registerItem(new CookedFish());
            self::registerItem(new Dye());
            self::registerItem(new Bone());
            self::registerItem(new Sugar());
            self::registerItem(new Cake());
            self::registerItem(new Bed());
            self::registerItem(new Repeater());
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
            self::registerItem(new GhastTear());
            self::registerItem(new GoldNugget());
            self::registerItem(new NetherWart());
            self::registerItem(new Potion());
            self::registerItem(new GlassBottle());
            self::registerItem(new SpiderEye());
            self::registerItem(new FermentedSpiderEye());
            self::registerItem(new BlazePowder());
            self::registerItem(new MagmaCream());
            self::registerItem(new BrewingStand());
            self::registerItem(new Cauldron());
            self::registerItem(new EyeOfEnder());
            self::registerItem(new GlisteringMelon());
            self::registerItem(new SpawnEgg());
            self::registerItem(new EnchantingBottle());
            self::registerItem(new FireCharge());
            self::registerItem(new WritableBook());
            self::registerItem(new WrittenBook());
            self::registerItem(new Emerald());
            self::registerItem(new ItemFrame());
            self::registerItem(new FlowerPot());
            self::registerItem(new Carrot());
            self::registerItem(new Potato());
            self::registerItem(new BakedPotato());
            // TODO : POISONOUS_POTATO
            // TODO : EMPTY_MAP
            self::registerItem(new GoldenCarrot());
            self::registerItem(new Skull());
            // TODO : CARROT_ON_A_STICK
            self::registerItem(new NetherStar());
            self::registerItem(new PumpkinPie());
            // TODO : FIREWORK
            // TODO : FIREWORK_STAR
            self::registerItem(new EnchantedBook());
            // TODO : COMPARATOR
            self::registerItem(new NetherBrick());
            self::registerItem(new Quartz());
            // TODO : MINECART_WITH_TNT
            // TODO : MINECART_WITH_HOPPER
            self::registerItem(new PrismarineShard());
            self::registerItem(new Hopper());
            self::registerItem(new RawRabbit());
            self::registerItem(new CookedRabbit());
            self::registerItem(new RabbitStew());
            // TODO : RABBIT_FOOT
            // TODO : RABBIT_HIDE
            // TODO : LEATHER_HORSE_ARMOR
            // TODO : IRON_HORSE_ARMOR
            // TODO : GOLD_HORSE_ARMOR
            // TODO : DIAMOND_HORSE_ARMOR
            // TODO : LEAD
            // TODO : NAMETAG
            self::registerItem(new PrismarineCrystals());
            self::registerItem(new RawMutton());
            self::registerItem(new CookedMutton());
            self::registerItem(new ArmorStand());
            // TODO : END_CRYSTAL
            self::registerItem(new SpruceDoor());
            self::registerItem(new BirchDoor());
            self::registerItem(new JungleDoor());
            self::registerItem(new AcaciaDoor());
            self::registerItem(new DarkOakDoor());
            self::registerItem(new ChorusFruit());
            self::registerItem(new PoppedChorusFruit());

            self::registerItem(new DragonsBreath());
            self::registerItem(new SplashPotion());

            self::registerItem(new LingeringPotion());

            //TODO: COMMAND_BLOCK_MINECART
            self::registerItem(new Elytra());
            self::registerItem(new ShulkerShell());
            // TODO : BANNER

            self::registerItem(new TotemOfUndying());

            self::registerItem(new Item(Item::IRON_NUGGET, 0, 1, "Iron Nugget")); // Iron Nugget

            self::registerItem(new Beetroot());
            self::registerItem(new BeetrootSeeds());
            self::registerItem(new BeetrootSoup());
            //TODO: RAW_SALMON
            //TODO: CLOWN_FISH
            //TODO: PUFFER_FISH
            //TODO: COOKED_SALMON

            self::registerItem(new EnchantedGoldenApple());

            self::registerItem(new Camera());

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

			for($i = 0; $i < 256; ++$i){
				if(Block::$list[$i] !== null){
					self::$list[$i] = Block::$list[$i];
				}
			}
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
            return Block::isRegistered($id);
        }
        return isset(self::$list[$id]);
    }

	private static $creative = [];

	private static function initCreativeItems(){
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
                    $item->addEnchantment(Enchantment::getEnchantment($ench["id"])->setLevel($ench["lvl"]));
                }
            }
            if($item->getName() === "Unknown"){
                continue;
            }
            self::addCreativeItem($item);
        }
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
		if(!in_array($item, Item::$creative)){
		 Item::$creative[] = clone $item;
		}
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

    /**
     * @param int $id
     * @param int $meta
     * @param int $count
     * @param string $tags
     * @return Item
     * @throws \TypeError
     */
    public static function get(int $id, int $meta = 0, int $count = 1, string $tags = "") : Item{
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
                if(isset(self::$list[$id])){
                    $item = clone self::$list[$id];
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
	 * Item constructor.
	 *
	 * @param int    $id
	 * @param int    $meta
	 * @param int    $count
	 * @param string $name
	 */
	public function __construct(int $id, int $meta = 0, int $count = 1, string $name = "Unknown"){
		$this->id = $id & 0xffff;
		$this->meta = $meta !== -1 ? $meta & 0xffff : -1;
		$this->count = $count;
		$this->name = $name;
		if(!isset($this->block) and $this->id <= 0xff and isset(Block::$list[$this->id])){
			$this->block = Block::get($this->id, $this->meta);
			$this->name = $this->block->getName();
		}
	}

	/**
	 * @param $tags
	 *
	 * @return $this
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
	 * @return string
	 */
	public function getCompoundTag() : string{
		return $this->tags;
	}

	/**
	 * @return bool
	 */
	public function hasCompoundTag() : bool{
		return $this->tags !== "";
	}

	/**
	 * @return bool
	 */
	public function hasCustomBlockData() : bool{
		if(!$this->hasCompoundTag()){
			return false;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag){
			return true;
		}

		return false;
	}

	/**
	 * @return $this
	 */
	public function clearCustomBlockData(){
		if(!$this->hasCompoundTag()){
			return $this;
		}
		$tag = $this->getNamedTag();

		if(isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag){
			unset($tag->display->BlockEntityTag);
			$this->setNamedTag($tag);
		}

		return $this;
	}

	/**
	 * @param CompoundTag $compound
	 *
	 * @return $this
	 */
	public function setCustomBlockData(CompoundTag $compound){
		$tags = clone $compound;
		$tags->setName("BlockEntityTag");

		if(!$this->hasCompoundTag()){
			$tag = new CompoundTag("", []);
		}else{
			$tag = $this->getNamedTag();
		}

		$tag->BlockEntityTag = $tags;
		$this->setNamedTag($tag);

		return $this;
	}

	/**
	 * @return null
	 */
	public function getCustomBlockData(){
		if(!$this->hasCompoundTag()){
			return null;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag){
			return $tag->BlockEntityTag;
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function hasEnchantments() : bool{
		if(!$this->hasCompoundTag()){
			return false;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->ench)){
			$tag = $tag->ench;
			if($tag instanceof ListTag){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $id
	 *
	 * @return Enchantment|null
	 */
	public function getEnchantment(int $id){
		if(!$this->hasEnchantments()){
			return null;
		}

		foreach($this->getNamedTag()->ench as $entry){
			if($entry["id"] === $id){
				$e = Enchantment::getEnchantment($entry["id"]);
				$e->setLevel($entry["lvl"]);
				return $e;
			}
		}

		return null;
	}

	/**
	 * @param int  $id
	 * @param int  $level
	 * @param bool $compareLevel
	 *
	 * @return bool
	 */
	public function hasEnchantment(int $id, int $level = 1, bool $compareLevel = false) : bool{
		if($this->hasEnchantments()){
			foreach($this->getEnchantments() as $enchantment){
				if($enchantment->getId() == $id){
					if($compareLevel){
						if($enchantment->getLevel() == $level){
							return true;
						}
					}else{
						return true;
					}
				}
			}
		}
		return false;
	}

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
				$e->setLevel($entry["lvl"]);
				$E_level = $e->getLevel() > Enchantment::getEnchantMaxLevel($id) ? Enchantment::getEnchantMaxLevel($id) : $e->getLevel();
				return $E_level;
			}
		}

		return 0;
	}

	/**
	 * @param Enchantment $ench
	 */
	public function addEnchantment(Enchantment $ench){
		if(!$this->hasCompoundTag()){
			$tag = new CompoundTag("", []);
		}else{
			$tag = $this->getNamedTag();
		}

		if(!isset($tag->ench)){
			$tag->ench = new ListTag("ench", []);
			$tag->ench->setTagType(NBT::TAG_Compound);
		}

		$found = false;

		foreach($tag->ench as $k => $entry){
			if($entry["id"] === $ench->getId()){
				$tag->ench->{$k} = new CompoundTag("", [
					"id" => new ShortTag("id", $ench->getId()),
					"lvl" => new ShortTag("lvl", $ench->getLevel())
				]);
				$found = true;
				break;
			}
		}

		if(!$found){
			$count = 0;
			foreach($tag->ench as $key => $value){
				if(is_numeric($key)){
					$count++;
				}
			}
			$tag->ench->{$count + 1} = new CompoundTag("", [
				"id" => new ShortTag("id", $ench->getId()),
				"lvl" => new ShortTag("lvl", $ench->getLevel())
			]);
		}

		$this->setNamedTag($tag);
	}

	/**
	 * @return Enchantment[]
	 */
	public function getEnchantments() : array{
		if(!$this->hasEnchantments()){
			return [];
		}

		$enchantments = [];

		foreach($this->getNamedTag()->ench as $entry){
			$e = Enchantment::getEnchantment($entry["id"]);
			$e->setLevel($entry["lvl"]);
			$enchantments[] = $e;
		}

		return $enchantments;
	}

	/**
	 * @return bool
	 */
	public function hasRepairCost() : bool{
		if(!$this->hasCompoundTag()){
			return false;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->RepairCost)){
			$tag = $tag->RepairCost;
			if($tag instanceof IntTag){
				return true;
			}
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getRepairCost() : int{
		if(!$this->hasCompoundTag()){
			return 1;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->display)){
			$tag = $tag->RepairCost;
			if($tag instanceof IntTag){
				return $tag->getValue();
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

		if(!($hadCompoundTag = $this->hasCompoundTag())){
			$tag = new CompoundTag("", []);
		}else{
			$tag = $this->getNamedTag();
		}

		$tag->RepairCost = new IntTag("RepairCost", $cost);

		if(!$hadCompoundTag){
			$this->setCompoundTag($tag);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearRepairCost(){
		if(!$this->hasCompoundTag()){
			return $this;
		}
		$tag = $this->getNamedTag();

		if(isset($tag->RepairCost) and $tag->RepairCost instanceof IntTag){
			unset($tag->RepairCost);
			$this->setNamedTag($tag);
		}

		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasCustomName() : bool{
		if(!$this->hasCompoundTag()){
			return false;
		}

		$tag = $this->getNamedTag();
		if(isset($tag->display)){
			$tag = $tag->display;
			if($tag instanceof CompoundTag and isset($tag->Name) and $tag->Name instanceof StringTag){
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getCustomName() : string{
		if(!$this->hasCompoundTag()){
			return "";
		}

		$tag = $this->getNamedTag();
		if(isset($tag->display)){
			$tag = $tag->display;
			if($tag instanceof CompoundTag and isset($tag->Name) and $tag->Name instanceof StringTag){
				return $tag->Name->getValue();
			}
		}

		return "";
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setCustomName(string $name){
		if($name === ""){
			$this->clearCustomName();
		}

		if(!($hadCompoundTag = $this->hasCompoundTag())){
			$tag = new CompoundTag("", []);
		}else{
			$tag = $this->getNamedTag();
		}

		if(isset($tag->display) and $tag->display instanceof CompoundTag){
			$tag->display->Name = new StringTag("Name", $name);
		}else{
			$tag->display = new CompoundTag("display", [
				"Name" => new StringTag("Name", $name)
			]);
		}

		if(!$hadCompoundTag){
			$this->setCompoundTag($tag);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearCustomName(){
		if(!$this->hasCompoundTag()){
			return $this;
		}
		$tag = $this->getNamedTag();

		if(isset($tag->display) and $tag->display instanceof CompoundTag){
			unset($tag->display->Name);
			if($tag->display->getCount() === 0){
				unset($tag->display);
			}

			$this->setNamedTag($tag);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getLore() : array{
		$tag = $this->getNamedTagEntry("display");
		if($tag instanceof CompoundTag and isset($tag->Lore) and $tag->Lore instanceof ListTag){
			$lines = [];
			/** @var StringTag $line */
            foreach($tag->Lore->getValue() as $line){
				$lines[] = $line->getValue();
			}
			return $lines;
		}
		return [];
	}

	/**
	 * @param array $lines
	 *
	 * @return $this
	 */
	public function setLore(array $lines){
		$tag = $this->getNamedTag() ?? new CompoundTag("", []);
		if(!isset($tag->display)){
			$tag->display = new CompoundTag("display", []);
		}
		$tag->display->Lore = new ListTag("Lore");
		$tag->display->Lore->setTagType(NBT::TAG_String);
		$count = 0;
		foreach($lines as $line){
			$tag->display->Lore[$count++] = new StringTag("", $line);
		}
		$this->setNamedTag($tag);
		return $this;
	}


	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function getNamedTagEntry($name){
		$tag = $this->getNamedTag();
		if($tag !== null){
			return isset($tag->{$name}) ? $tag->{$name} : null;
		}

		return null;
	}

	/**
	 * @return null|CompoundTag
	 */
	public function getNamedTag(){
        if(!$this->hasCompoundTag() and $this->cachedNBT === null){
            $this->cachedNBT = new CompoundTag();
        }
        return $this->cachedNBT ?? ($this->cachedNBT = self::parseCompoundTag($this->tags));
	}

	/**
	 * @param CompoundTag $tag
	 *
	 * @return $this|Item
	 */
	public function setNamedTag(CompoundTag $tag){
		if($tag->getCount() === 0){
			return $this->clearNamedTag();
		}

		$this->cachedNBT = $tag;
		$this->tags = self::writeCompoundTag($tag);

		return $this;
	}

	/**
	 * @return Item
	 */
	public function clearNamedTag(){
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
	 */
	public function setCount(int $count){
		$this->count = $count;
	}

	/**
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
	 * @return bool
	 */
	final public function isPlaceable() : bool{
		return $this->canBePlaced();
	}

	/**
	 * @return bool
	 */
	public function canBeConsumed() : bool{
		return false;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canBeConsumedBy(Entity $entity) : bool{
		return $this->canBeConsumed();
	}

	/**
	 * @param Entity $entity
	 */
	public function onConsume(Entity $entity){
	}

	/**
	 * @return Block
	 */
	public function getBlock() : Block{
		if($this->block instanceof Block){
			return clone $this->block;
		}else{
			return Block::get(self::AIR);
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
	 */
	public function setDamage(int $meta){
        $this->meta = $meta !== null ? $meta & 32767 : null;
	}

	/**
	 * @return bool
	 */
	public function hasAnyDamageValue() : bool{
		return $this->meta === -1;
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 64;
	}

	/**
	 * @return int
	 */
	public function getFuelTime() : int{
		return FurnaceFuel::getFurnaceFuelTime($this->getId());
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
	 * @return int|bool
	 */
	public function getMaxDurability(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isPickaxe(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isAxe(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isSword(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isShovel(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isHoe(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isShears(){
		return false;
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
	public function getArmorValue(){
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
	 * @return int
	 */
	public function getAttackDamage(){
		return 1;
	}

	/**
	 * @param Entity $target
	 *
	 * @return float|int
	 */
	public function getModifyAttackDamage(Entity $target){
		$rec = $this->getAttackDamage();
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

	/**
	 * @param Block  $block
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getDestroySpeed(Block $block, Player $player){
		return 1;
	}

	/**
	 * @param Level  $level
	 * @param Player $player
	 * @param Block  $block
	 * @param Block  $target
	 * @param        $face
	 * @param        $fx
	 * @param        $fy
	 * @param        $fz
	 *
	 * @return bool
	 */
	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		return false;
	}

	/**
	 * @param Item $item
	 * @param bool $checkDamage
	 * @param bool $checkCompound
	 * @param bool $checkCount
	 *
	 * @return bool
	 */
	public final function equals(Item $item, bool $checkDamage = true, bool $checkCompound = true, $checkCount = false) : bool{
		if($this->id === $item->getId() and ($checkDamage === false or $this->getDamage() === $item->getDamage()) and ($checkCount === false or $this->getCount() === $item->getCount())){
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
	 * @return string
	 */
	final public function __toString() : string{
		return "Item " . $this->name . " (" . $this->id . ":" . ($this->meta === null ? "?" : $this->meta) . ")x" . $this->count . ($this->hasCompoundTag() ? " tags:0x" . bin2hex($this->getCompoundTag()) : "");
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
	 * Serializes the item to an NBT CompoundTag
	 *
	 * @param int    $slot    optional, the inventory slot of the item
	 * @param string $tagName the name to assign to the CompoundTag object
	 *
	 * @return CompoundTag
	 */
	public function nbtSerialize(int $slot = -1, string $tagName = "") : CompoundTag{
		$tag = new CompoundTag($tagName, [
			"id" => new ShortTag("id", $this->id),
			"Count" => new ByteTag("Count", $this->count ?? -1),
			"Damage" => new ShortTag("Damage", $this->meta),
		]);

		if($this->hasCompoundTag()){
			$tag->tag = clone $this->getNamedTag();
			$tag->tag->setName("tag");
		}

		if($slot !== -1){
			$tag->Slot = new ByteTag("Slot", $slot);
		}

		return $tag;
	}

	/**
	 * Deserializes an Item from an NBT CompoundTag
	 *
	 * @param CompoundTag $tag
	 *
	 * @return Item
	 */
	public static function nbtDeserialize(CompoundTag $tag) : Item{
		if(!isset($tag->id) or !isset($tag->Count)){
			return Item::get(0);
		}

		if($tag->id instanceof ShortTag){
			$item = Item::get($tag->id->getValue(), !isset($tag->Damage) ? 0 : $tag->Damage->getValue(), $tag->Count->getValue());
		}elseif($tag->id instanceof StringTag){ //PC item save format
			$item = Item::fromString($tag->id->getValue());
			$item->setDamage(!isset($tag->Damage) ? 0 : $tag->Damage->getValue());
			$item->setCount($tag->Count->getValue());
		}else{
			throw new \InvalidArgumentException("Item CompoundTag ID must be an instance of StringTag or ShortTag, " . get_class($tag->id) . " given");
		}

		if(isset($tag->tag) and $tag->tag instanceof CompoundTag){
            $t = clone $tag->tag;
            $t->setName("");
            $item->setNamedTag($t);
		}

		return $item;
	}
	
	public function isNull() : bool{
		return $this->count <= 0 or $this->getId() == Item::AIR;
	}
	
	/**
	 * Returns an array of item stack properties that can be serialized to json.
	 *
	 * @return array
	 */
	final public function jsonSerialize(){
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


}