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

namespace pocketmine\entity;

use pocketmine\entity\projectile\ProjectileSource;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\inventory\EnderChestInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Math;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\Player;
use pocketmine\utils\UUID;

class Human extends Creature implements ProjectileSource, InventoryHolder {

	const DATA_PLAYER_FLAG_SLEEP = 1;
	const DATA_PLAYER_FLAG_DEAD = 2; //TODO: CHECK

	const DATA_PLAYER_FLAGS = 27;

	const DATA_PLAYER_BED_POSITION = 29;

	/** @var PlayerInventory */
	protected $inventory;

	/** @var EnderChestInventory */
	protected $enderChestInventory;

	/** @var UUID */
	protected $uuid;
	protected $rawUUID;

	public $width = 0.6;
	public $height = 1.8;
	public $eyeHeight = 1.62;
	public $baseOffset = 1.62;
	
	/** @var Skin */
	protected $skin;

	protected $foodTickTimer = 0;

	protected $totalXp = 0;
	protected $xpSeed = 0;
	protected $xpCooldown = 0;

	public function jump(){
		parent::jump();
		if($this->isSprinting()){
			$this->exhaust(0.8, PlayerExhaustEvent::CAUSE_SPRINT_JUMPING);
		}else{
			$this->exhaust(0.2, PlayerExhaustEvent::CAUSE_JUMPING);
		}
	}

	/**
	 * @return mixed
	 */
	public function getSkinData(){
		return $this->skin->getSkinData();
	}

	/**
	 * @return mixed
	 */
	public function getSkinId(){
		return $this->skin->getSkinId();
	}

	/**
	 * @return UUID|null
	 */
	public function getUniqueId(){
		return $this->uuid;
	}

	/**
	 * @return string
	 */
	public function getRawUniqueId(){
		return $this->rawUUID;
	}
	
	/**
	 * Sets the human's skin. This will not send any update to viewers, you need to do that manually using
	 * {@link sendSkin}.
	 *
	 * @param Skin $skin
	 */
	public function setSkin(Skin $skin){
		if(!$skin->isValid()){
			throw new \InvalidStateException("Specified skin is not valid, must be 8KiB or 16KiB");
		}
		$skin->debloatGeometryData();
		$this->skin = $skin;
	}

	/**
	 * @param Player[] $targets
	 */
	public function sendSkin(array $targets){
		$pk = new PlayerSkinPacket();
		$pk->uuid = $this->getUniqueId();
		$pk->skin = $this->skin;
		$this->server->broadcastPacket($targets, $pk);
	}
	
	public function getSkin() : Skin{
		return $this->skin;
	}
	
	/**
	 * @return float
	 */
	public function getFood() : float{
		return $this->attributeMap->getAttribute(Attribute::HUNGER)->getValue();
	}

	/**
	 * WARNING: This method does not check if full and may throw an exception if out of bounds.
	 * Use {@link Human::addFood()} for this purpose
	 *
	 * @param float $new
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setFood(float $new){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$old = $attr->getValue();
		$attr->setValue($new);
		// ranges: 18-20 (regen), 7-17 (none), 1-6 (no sprint), 0 (health depletion)
		foreach([17, 6, 0] as $bound){
			if(($old > $bound) !== ($new > $bound)){
				$reset = true;
			}
		}
		if(isset($reset)){
			$this->foodTickTimer = 0;
		}

	}

	/**
	 * @return float
	 */
	public function getMaxFood() : float{
		return $this->attributeMap->getAttribute(Attribute::HUNGER)->getMaxValue();
	}

	/**
	 * @param float $amount
	 */
	public function addFood(float $amount){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$amount += $attr->getValue();
		$amount = max(min($amount, $attr->getMaxValue()), $attr->getMinValue());
		$this->setFood($amount);
	}

	/**
	 * @return float
	 */
	public function getSaturation() : float{
		return $this->attributeMap->getAttribute(Attribute::SATURATION)->getValue();
	}

	/**
	 * WARNING: This method does not check if saturated and may throw an exception if out of bounds.
	 * Use {@link Human::addSaturation()} for this purpose
	 *
	 * @param float $saturation
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setSaturation(float $saturation){
		$this->attributeMap->getAttribute(Attribute::SATURATION)->setValue($saturation);
	}

	/**
	 * @param float $amount
	 */
	public function addSaturation(float $amount){
		$attr = $this->attributeMap->getAttribute(Attribute::SATURATION);
		$attr->setValue($attr->getValue() + $amount, true);
	}

	/**
	 * @return float
	 */
	public function getExhaustion() : float{
		return $this->attributeMap->getAttribute(Attribute::EXHAUSTION)->getValue();
	}

	/**
	 * WARNING: This method does not check if exhausted and does not consume saturation/food.
	 * Use {@link Human::exhaust()} for this purpose.
	 *
	 * @param float $exhaustion
	 */
	public function setExhaustion(float $exhaustion){
		$this->attributeMap->getAttribute(Attribute::EXHAUSTION)->setValue($exhaustion);
	}

	/**
	 * Increases a human's exhaustion level.
	 *
	 * @param float $amount
	 * @param int   $cause
	 *
	 * @return float the amount of exhaustion level increased
	 */
	public function exhaust(float $amount, int $cause = PlayerExhaustEvent::CAUSE_CUSTOM) : float{
		$this->server->getPluginManager()->callEvent($ev = new PlayerExhaustEvent($this, $amount, $cause));
		if($ev->isCancelled()){
			return 0.0;
		}

		$exhaustion = $this->getExhaustion();
		$exhaustion += $ev->getAmount();

		while($exhaustion >= 4.0){
			$exhaustion -= 4.0;

			$saturation = $this->getSaturation();
			if($saturation > 0){
				$saturation = max(0, $saturation - 1.0);
				$this->setSaturation($saturation);
			}else{
				$food = $this->getFood();
				if($food > 0){
				    $check = true;
					if($this instanceof Player && $this->isCreative()){
					    $check = false;
                    }
                    if($check){
                        $food--;
                        $this->setFood($food);
                    }
				}
			}
		}
		$this->setExhaustion($exhaustion);

		return $ev->getAmount();
	}

	/**
	 * @return int
	 */
	public function getXpLevel() : int{
		return (int) $this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->getValue();
	}

	/**
	 * @param int $level
	 *
	 * @return bool
	 */
	public function setXpLevel(int $level) : bool{
		$this->server->getPluginManager()->callEvent($ev = new PlayerExperienceChangeEvent($this, $level, $this->getXpProgress()));
		if(!$ev->isCancelled()){
			$this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->setValue($ev->getExpLevel());
			return true;
		}
		return false;
	}

	/**
	 * @param int $level
	 *
	 * @return bool
	 */
	public function addXpLevel(int $level) : bool{
		return $this->setXpLevel($this->getXpLevel() + $level);
	}

	/**
	 * @param int $level
	 *
	 * @return bool
	 */
	public function takeXpLevel(int $level) : bool{
		return $this->setXpLevel($this->getXpLevel() - $level);
	}

	/**
	 * @return float
	 */
	public function getXpProgress() : float{
		return $this->attributeMap->getAttribute(Attribute::EXPERIENCE)->getValue();
	}

	/**
	 * @param float $progress
	 *
	 * @return bool
	 */
	public function setXpProgress(float $progress) : bool{
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE)->setValue($progress);
		return true;
	}

	/**
	 * @return int
	 */
	public function getTotalXp() : int{
		return $this->totalXp;
	}

	/**
	 * Changes the total exp of a player
	 *
	 * @param int  $xp
	 * @param bool $syncLevel This will reset the level to be in sync with the total. Usually you don't want to do this,
	 *						because it'll mess up use of xp in anvils and enchanting tables.
	 *
	 * @return bool
	 */
	public function setTotalXp(int $xp, bool $syncLevel = false) : bool{
		$xp &= 0x7fffffff;
		if($xp === $this->totalXp){
			return false;
		}
		if(!$syncLevel){
			$level = $this->getXpLevel();
			$diff = $xp - $this->totalXp + $this->getFilledXp();
			if($diff > 0){ //adding xp
				while($diff > ($v = self::getLevelXpRequirement($level))){
					$diff -= $v;
					if(++$level >= 21863){
						$diff = $v; //fill exp bar
						break;
					}
				}
			}else{ //taking xp
				while($diff < ($v = self::getLevelXpRequirement($level - 1))){
					$diff += $v;
					if(--$level <= 0){
						$diff = 0;
						break;
					}
				}
			}
			$progress = ($diff / $v);
		}else{
			$values = self::getLevelFromXp($xp);
			$level = $values[0];
			$progress = $values[1];
		}

		$this->server->getPluginManager()->callEvent($ev = new PlayerExperienceChangeEvent($this, intval($level), $progress));
		if(!$ev->isCancelled()){
			$this->totalXp = $xp;
			$this->setXpLevel($ev->getExpLevel());
			$this->setXpProgress($ev->getProgress());
			return true;
		}
		return false;
	}

	/**
	 * @param int  $xp
	 * @param bool $syncLevel
	 *
	 * @return bool
	 */
	public function addXp(int $xp, bool $syncLevel = false) : bool{
		return $this->setTotalXp($this->totalXp + $xp, $syncLevel);
	}

	/**
	 * @param int  $xp
	 * @param bool $syncLevel
	 *
	 * @return bool
	 */
	public function takeXp(int $xp, bool $syncLevel = false) : bool{
		return $this->setTotalXp($this->totalXp - $xp, $syncLevel);
	}

    /**
     * @return float
     */
	public function getRemainderXp() : float{
		return self::getLevelXpRequirement($this->getXpLevel()) - $this->getFilledXp();
	}

    /**
     * @return float
     */
	public function getFilledXp() : float{
		return self::getLevelXpRequirement($this->getXpLevel()) * $this->getXpProgress();
	}

	/**
	 * @return float
	 */
	public function recalculateXpProgress() : float{
		$this->setXpProgress($progress = $this->getRemainderXp() / self::getLevelXpRequirement($this->getXpLevel()));
		return $progress;
	}

	/**
	 * @return int
	 */
	public function getXpSeed() : int{
		//TODO: use this for randomizing enchantments in enchanting tables
		return $this->xpSeed;
	}

	public function resetXpCooldown(){
		$this->xpCooldown = microtime(true);
	}

	/**
	 * @return bool
	 */
	public function canPickupXp() : bool{
		return microtime(true) - $this->xpCooldown > 0.5;
	}

	/**
	 * Returns the total amount of exp required to reach the specified level.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public static function getTotalXpRequirement(int $level) : int{
		if($level <= 16){
			return ($level ** 2) + (6 * $level);
		}elseif($level <= 31){
			return (2.5 * ($level ** 2)) - (40.5 * $level) + 360;
		}elseif($level <= 21863){
			return (4.5 * ($level ** 2)) - (162.5 * $level) + 2220;
		}
		return PHP_INT_MAX; //prevent float returns for invalid levels on 32-bit systems
	}

	/**
	 * Returns the amount of exp required to complete the specified level.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public static function getLevelXpRequirement(int $level) : int{
        if ($level <= 16) {
            return (2 * $level) + 7;
        } elseif ($level <= 32) {
            return (5 * $level) - 38;
        }
        return $level ** 2 * 4.5 - 162.5 * $level + 2220;
    }

	/**
	 * Converts a quantity of exp into a level and a progress percentage
	 *
	 * @param int $xp
	 *
	 * @return int[]
	 */
	public static function getLevelFromXp(int $xp) : array{
		$xp &= 0x7fffffff;

		/** These values are correct up to and including level 16 */
		$a = 1;
		$b = 6;
		$c = -$xp;
		if($xp > self::getTotalXpRequirement(16)){
			/** Modify the coefficients to fit the relevant equation */
			if($xp <= self::getTotalXpRequirement(31)){
				/** Levels 16-31 */
				$a = 2.5;
				$b = -40.5;
				$c += 360;
			}else{
				/** Level 32+ */
				$a = 4.5;
				$b = -162.5;
				$c += 2220;
			}
		}

		$answer = max(Math::solveQuadratic($a, $b, $c)); //Use largest result value
		$level = floor($answer);
		$progress = $answer - $level;
		return [$level, $progress];
	}

	/**
	 * @return PlayerInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	/**
	 * @return EnderChestInventory
	 */
	public function getEnderChestInventory(){
		return $this->enderChestInventory;
	}

    /**
     * For Human entities which are not players, sets their properties such as nametag, skin and UUID from NBT.
     */
    protected function initHumanData(){
        if($this->namedtag->hasTag("NameTag", StringTag::class)){
            $this->setNameTag($this->namedtag->getString("NameTag"));
        }

        $skin = $this->namedtag->getCompoundTag("Skin");
        if($skin !== null){
            $this->setSkin(new Skin(
                $skin->getString("Name"),
                $skin->getString("Data"),
                $skin->getString("CapeData", ""),
                $skin->getString("GeometryName", ""),
                $skin->getString("GeometryData", "")
            ));
        }

        $this->uuid = UUID::fromData((string) $this->getId(), $this->skin->getSkinData(), $this->getNameTag());
    }

	protected function initEntity(){
		$this->setDataFlag(self::DATA_PLAYER_FLAGS, self::DATA_PLAYER_FLAG_SLEEP, false, self::DATA_TYPE_BYTE);
		$this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [0, 0, 0], false);

		$this->inventory = new PlayerInventory($this);
        $this->enderChestInventory = new EnderChestInventory($this, $this->namedtag->getListTag("EnderChestInventory"));
        $this->initHumanData();

        $inventoryTag = $this->namedtag->getListTag("Inventory");
        if($inventoryTag !== null){
            /** @var CompoundTag $item */
            foreach($inventoryTag as $i => $item){
                $slot = $item->getByte("Slot");
                if($slot >= 0 and $slot < 9){ //Hotbar
                    //Old hotbar saving stuff, remove it (useless now)
                    unset($inventoryTag[$i]);
                }elseif($slot >= 100 and $slot < 104){ //Armor
                    $this->inventory->setItem($this->inventory->getSize() + $slot - 100, ItemItem::nbtDeserialize($item));
                }else{
                    $this->inventory->setItem($slot - 9, ItemItem::nbtDeserialize($item));
                }
            }
        }

        $this->inventory->setHeldItemIndex($this->namedtag->getInt("SelectedInventorySlot", 0), false);

		parent::initEntity();

        $this->setFood((float) $this->namedtag->getInt("foodLevel", (int) $this->getFood(), true));
        $this->setExhaustion($this->namedtag->getFloat("foodExhaustionLevel", $this->getExhaustion(), true));
        $this->setSaturation($this->namedtag->getFloat("foodSaturationLevel", $this->getSaturation(), true));
        $this->foodTickTimer = $this->namedtag->getInt("foodTickTimer", $this->foodTickTimer, true);

        $this->setXpLevel($this->namedtag->getInt("XpLevel", $this->getXpLevel(), true));
        $this->setXpProgress($this->namedtag->getFloat("XpP", $this->getXpProgress(), true));
        $this->totalXp = $this->namedtag->getInt("XpTotal", $this->totalXp, true);

        if($this->namedtag->hasTag("XpSeed", IntTag::class)){
            $this->xpSeed = $this->namedtag->getInt("XpSeed");
        }else{
            $this->xpSeed = random_int(INT32_MIN, INT32_MAX);
        }
	}

	public function getAbsorption() : float{
		return $this->attributeMap->getAttribute(Attribute::ABSORPTION)->getValue();
	}

	public function setAbsorption(float $absorption){
		$this->attributeMap->getAttribute(Attribute::ABSORPTION)->setValue($absorption);
	}

	protected function addAttributes(){
		parent::addAttributes();

		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::SATURATION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXHAUSTION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HUNGER));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE_LEVEL));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HEALTH));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::MOVEMENT_SPEED));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::ABSORPTION));
	}

	/**
	 * @param int $tickDiff
	 * @param int $EnchantL
	 *
	 * @return bool
	 */
	public function entityBaseTick(int $tickDiff = 1, $EnchantL = 0){
		if($this->getInventory() instanceof PlayerInventory){
			$EnchantL = $this->getInventory()->getHelmet()->getEnchantmentLevel(Enchantment::TYPE_WATER_BREATHING);
		}
		$this->maxAir = 400 + $EnchantL * 300;
		$hasUpdate = parent::entityBaseTick($tickDiff);
		$this->maxAir = 400;

		if($this->isAlive()){
			$food = $this->getFood();
			$health = $this->getHealth();
			if($food >= 18){
				$this->foodTickTimer++;
				if($this->foodTickTimer >= 80 and $health < $this->getMaxHealth()){
					$this->heal(new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_SATURATION));
					$this->exhaust(3.0, PlayerExhaustEvent::CAUSE_HEALTH_REGEN);
					$this->foodTickTimer = 0;

				}
			}elseif($food === 0){
				$this->foodTickTimer++;
				if($this->foodTickTimer >= 80){
					$diff = $this->server->getDifficulty();
					$can = false;
					if($diff === 1){
						$can = $health > 10;
					}elseif($diff === 2){
						$can = $health > 1;
					}elseif($diff === 3){
						$can = true;
					}
					if($can){
						$this->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_STARVATION, 1));
					}
				}
			}
			if($food <= 6){
				if($this->isSprinting()){
					$this->setSprinting(false);
				}
			}
		}

		return $hasUpdate;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->getNameTag();
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops = [];
		if($this->inventory !== null){
			foreach($this->inventory->getContents() as $item){
				$drops[] = $item;
			}
		}

		return $drops;
	}

	public function saveNBT(){
		parent::saveNBT();

        //Food
        $this->namedtag->setInt("foodLevel", (int) $this->getFood(), true);
        $this->namedtag->setFloat("foodExhaustionLevel", $this->getExhaustion(), true);
        $this->namedtag->setFloat("foodSaturationLevel", $this->getSaturation(), true);
        $this->namedtag->setInt("foodTickTimer", $this->foodTickTimer);

        //Xp
        $this->namedtag->setInt("XpLevel", $this->getXpLevel());
        $this->namedtag->setInt("XpTotal", $this->getTotalXp());
        $this->namedtag->setFloat("XpP", $this->getXpProgress());
        $this->namedtag->setInt("XpSeed", $this->getXpSeed());

        $inventoryTag = new ListTag("Inventory", [], NBT::TAG_Compound);
        $this->namedtag->setTag($inventoryTag);
		if($this->inventory !== null){
			//Normal inventory
			$slotCount = $this->inventory->getSize() + $this->inventory->getHotbarSize();
			for($slot = $this->inventory->getHotbarSize(); $slot < $slotCount; ++$slot){
				$item = $this->inventory->getItem($slot - 9);
				if($item->getId() !== ItemItem::AIR){
					$inventoryTag[$slot] = $item->nbtSerialize($slot);
				}
			}

			//Armor
			for($slot = 100; $slot < 104; ++$slot){
				$item = $this->inventory->getItem($this->inventory->getSize() + $slot - 100);
				if($item instanceof ItemItem and $item->getId() !== ItemItem::AIR){
					$inventoryTag[$slot] = $item->nbtSerialize($slot);
				}
			}

			$this->namedtag->setInt("SelectedInventorySlot", $this->inventory->getHeldItemIndex());
		}

		if($this->enderChestInventory !== null){
            /** @var CompoundTag[] $items */
            $items = [];

            $slotCount = $this->enderChestInventory->getSize();
            for($slot = 0; $slot < $slotCount; ++$slot){
                $item = $this->enderChestInventory->getItem($slot);
                if(!$item->isNull()){
                    $items[] = $item->nbtSerialize($slot);
                }
            }

            $this->namedtag->setTag(new ListTag("EnderChestInventory", $items, NBT::TAG_Compound));
		}

		if($this->skin instanceof Skin){
            $this->namedtag->setTag(new CompoundTag("Skin", [
                new StringTag("Data", $this->skin->getSkinData()),
                new StringTag("Name", $this->skin->getSkinId()),
                new StringTag("CapeData", $this->skin->getCapeData()),
                new StringTag("GeometryName", $this->skin->getGeometryName()),
                new StringTag("GeometryData", $this->skin->getGeometryData()),
            ]));
		}
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])){
			$this->hasSpawned[$player->getLoaderId()] = $player;

			if($this->skin === null or !$this->skin->isValid()){
				throw new \InvalidStateException((new \ReflectionClass($this))->getShortName() . " must have a valid skin set");
			}

			$pk = new AddPlayerPacket();
			$pk->uuid = $this->getUniqueId();
			$pk->username = $this->getName();
			$pk->entityRuntimeId = $this->getId();
			$pk->position = $this->asVector3();
			$pk->motion = $this->getMotion();
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->item = $this->getInventory()->getItemInHand();
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk);

			$this->inventory->sendArmorContents($player);

			if(!($this instanceof Player)){
				$this->sendSkin([$player]);
			}
		}
	}

	public function close(){
		if(!$this->closed){
			if(!($this instanceof Player) or $this->spawned){
				foreach($this->inventory->getViewers() as $viewer){
					$viewer->removeWindow($this->inventory);
				}
			}
			parent::close();
		}
	}
}