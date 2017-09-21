<?php

namespace pocketmine\network\mcpe\protocol;

use pocketmine\inventory\SimpleTransactionData;

class InventoryTransactionPacket extends DataPacket {

    const NETWORK_ID = ProtocolInfo::INVENTORY_TRANSACTION_PACKET;

    const TRANSACTION_TYPE_NORMAL = 0;
    const TRANSACTION_TYPE_INVENTORY_MISMATCH = 1;
    const TRANSACTION_TYPE_ITEM_USE = 2;
    const TRANSACTION_TYPE_ITEM_USE_ON_ENTITY = 3;
    const TRANSACTION_TYPE_ITEM_RELEASE = 4;

    const INV_SOURCE_TYPE_CONTAINER = 0;
    const INV_SOURCE_TYPE_GLOBAL = 1;
    const INV_SOURCE_TYPE_WORLD_INTERACTION = 2;
    const INV_SOURCE_TYPE_CREATIVE = 3;

    const ITEM_RELEASE_ACTION_RELEASE = 0;
    const ITEM_RELEASE_ACTION_USE = 1;

    const ITEM_USE_ACTION_PLACE = 0;
    const ITEM_USE_ACTION_USE = 1;
    const ITEM_USE_ACTION_DESTROY = 2;

    const ITEM_USE_ON_ENTITY_ACTION_INTERACT = 0;
    const ITEM_USE_ON_ENTITY_ACTION_ATTACK = 1;
    const ITEM_USE_ON_ENTITY_ACTION_ITEM_INTERACT = 2;

    public $transactionType;
    public $transactions;
    public $actionType;
    public $position;
    public $face;
    public $slot;
    public $item;
    public $fromPosition;
    public $clickPosition;
    public $entityId;

    public function decode() {
        var_dump(__CLASS__);
        $this->transactionType = $this->getVarInt();
        $this->transactions = $this->getTransactions();
        $this->getComplexTransactions();
    }

    public function encode() {
        $this->reset();
    }

    private function getTransactions() {
        $transactions = [];
        $actionsCount = $this->getVarInt();
        for ($i = 0; $i < $actionsCount; $i++) {
            $tr = new SimpleTransactionData();
            $sourceType = $this->getVarInt();
            switch ($sourceType) {
                case self::INV_SOURCE_TYPE_CONTAINER;
                    $tr->inventoryId = $this->getSignedVarInt();
                    break;
                case self::INV_SOURCE_TYPE_GLOBAL: // ???
                    break;
                case self::INV_SOURCE_TYPE_WORLD_INTERACTION:
                    $this->getVarInt(); // flags NoFlag = 0 WorldInteraction_Random = 1
                    break;
                case self::INV_SOURCE_TYPE_CREATIVE:
                    $tr->inventoryId = ContainerSetContentPacket::SPECIAL_CREATIVE;
                    break;
                default:
                    continue;
            }
            $tr->slot = $this->getVarInt();
            $tr->oldItem = $this->getSlot();
            $tr->newItem = $this->getSlot();
            $transactions[] = $tr;
        }
        return $transactions;
    }

    private function getComplexTransactions() {
        switch ($this->transactionType) {
            case self::TRANSACTION_TYPE_NORMAL:
            case self::TRANSACTION_TYPE_INVENTORY_MISMATCH:
                return;
            case self::TRANSACTION_TYPE_ITEM_USE:
                $this->actionType = $this->getVarInt();
                $this->position = [
                    'x' => $this->getSignedVarInt(),
                    'y' => $this->getVarInt(),
                    'z' => $this->getSignedVarInt()
                ];
                $this->face = $this->getSignedVarInt();
                $this->slot = $this->getSignedVarInt();
                $this->item = $this->getSlot();
                $this->fromPosition = [
                    'x' => $this->getLFloat(),
                    'y' => $this->getLFloat(),
                    'z' => $this->getLFloat()
                ];
                $this->clickPosition = [
                    'x' => $this->getLFloat(),
                    'y' => $this->getLFloat(),
                    'z' => $this->getLFloat()
                ];
                return;
            case self::TRANSACTION_TYPE_ITEM_USE_ON_ENTITY:
                $this->entityId = $this->getVarInt();
                $this->actionType = $this->getVarInt();
                $this->slot = $this->getSignedVarInt();
                $this->item = $this->getSlot();
                $this->fromPosition = [
                    'x' => $this->getLFloat(),
                    'y' => $this->getLFloat(),
                    'z' => $this->getLFloat()
                ];
                return;
            case self::TRANSACTION_TYPE_ITEM_RELEASE:
                $this->actionType = $this->getVarInt();
                $this->slot = $this->getSignedVarInt();
                $this->item = $this->getSlot();
                $this->fromPosition = [
                    'x' => $this->getLFloat(),
                    'y' => $this->getLFloat(),
                    'z' => $this->getLFloat()
                ];
                return;
        }
    }
}