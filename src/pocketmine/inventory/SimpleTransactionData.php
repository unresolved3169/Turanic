<?php

namespace pocketmine\inventory;

use pocketmine\item\Item;

class SimpleTransactionData{

    /** @var int */
    public $inventoryId = -1;
    /** @var integer */
    public $slot = -1;
    /** @var Item */
    public $oldItem;
    /** @var Item */
    public $newItem;

    public function __construct() {
        $this->oldItem = Item::get(Item::AIR);
        $this->newItem = Item::get(Item::AIR);
    }

}