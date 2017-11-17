<?php

namespace pocketmine\inventory\transaction;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class InventoryTransaction {

    private $creationTime;

    protected $source = null;

    /** @var InventoryAction[] */
    protected $actions = [];

    /** @var Inventory[] */
    protected $inventories = [];

    protected $hasExecuted = false;

    public function __construct(Player $player = null){
        $this->creationTime = microtime(true);
        $this->source = $player;
    }

    public function addAction(InventoryAction $action){
        if (isset($this->actions[spl_object_hash($action)])) {
            return;
        }
        if($action instanceof SlotChangeAction){
            $action->setInventoryFrom($this->source);
            $this->inventories[spl_object_hash($action->getInventory())] = $action->getInventory();
        }
        $this->actions[spl_object_hash($action)] = $action;
    }

    /**
     * @internal This method should not be used by plugins, it's used to add tracked inventories for InventoryActions
     * involving inventories.
     *
     * @param Inventory $inventory
     */
    public function addInventory(Inventory $inventory) : void{
        if(!isset($this->inventories[$hash = spl_object_hash($inventory)])){
            $this->inventories[$hash] = $inventory;
        }
    }

    public function canExecute() : bool{
        $haveItems = [];
        $needItems = [];
        return $this->matchItems($needItems, $haveItems) and count($this->actions) > 0 and count($haveItems) === 0 and count($needItems) === 0;
    }

    /**
     * @param Item[] $needItems
     * @param Item[] $haveItems
     *
     * @return bool
     */
    protected function matchItems(array &$needItems, array &$haveItems) : bool{
        foreach($this->actions as $key => $action){
            if(!$action->getTargetItem()->isNull()){
                $needItems[] = $action->getTargetItem();
            }
            if(!$action->getSourceItem()->isNull()){
                $haveItems[] = $action->getSourceItem();
            }
        }
        foreach($needItems as $i => $needItem){
            foreach($haveItems as $j => $haveItem){
                if($needItem->equals($haveItem)){
                    $amount = min($needItem->getCount(), $haveItem->getCount());
                    $needItem->setCount($needItem->getCount() - $amount);
                    $haveItem->setCount($haveItem->getCount() - $amount);
                    if($haveItem->getCount() === 0){
                        unset($haveItems[$j]);
                    }
                    if($needItem->getCount() === 0){
                        unset($needItems[$i]);
                        break;
                    }
                }
            }
        }
        return true;
    }

    public function execute() : bool{
        if ($this->hasExecuted() or !$this->canExecute()) {
            return false;
        }
        Server::getInstance()->getPluginManager()->callEvent($ev = new InventoryTransactionEvent($this));
        if ($ev->isCancelled()) {
            $this->sendInventories();
            return false;
        }

        $actions = $this->actions;
        foreach ($actions as $action) {
            if(!$action->isValid($this->source) || $action->isAlreadyDone($this->source)){
                unset($actions[spl_object_hash($action)]);
            }
            if(!$action->onPreExecute($this->source)){
                return false;
            }
        }

        foreach ($actions as $action) {
            if($action->execute($this->source)){
                $action->onExecuteSuccess($this->source);
            }else{
                $action->onExecuteFail($this->source);
            }
        }

        $this->hasExecuted = true;
        return true;
    }

    public function sendInventories() {
        foreach ($this->inventories as $inventory) {
            if ($inventory instanceof PlayerInventory) {
                $inventory->sendArmorContents($this->getSource());
            }
            $inventory->sendContents($this->getSource());
        }
    }

    /**
     * @return bool
     */
    public function hasExecuted() : bool{
        return $this->hasExecuted;
    }

    /**
     * @return Player|null
     */
    public function getSource() {
        return $this->source;
    }

    public function getCreationTime() {
        return $this->creationTime;
    }

    public function getInventories() {
        return $this->inventories;
    }

    public function getActions() {
        return $this->actions;
    }
}