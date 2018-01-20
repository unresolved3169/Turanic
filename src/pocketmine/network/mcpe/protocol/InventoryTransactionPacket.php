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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;

class InventoryTransactionPacket extends DataPacket{
    const NETWORK_ID = ProtocolInfo::INVENTORY_TRANSACTION_PACKET;

    const TYPE_NORMAL = 0;
    const TYPE_MISMATCH = 1;
    const TYPE_USE_ITEM = 2;
    const TYPE_USE_ITEM_ON_ENTITY = 3;
    const TYPE_RELEASE_ITEM = 4;

    const USE_ITEM_ACTION_CLICK_BLOCK = 0;
    const USE_ITEM_ACTION_CLICK_AIR = 1;
    const USE_ITEM_ACTION_BREAK_BLOCK = 2;

    const RELEASE_ITEM_ACTION_RELEASE = 0; //bow shoot
    const RELEASE_ITEM_ACTION_CONSUME = 1; //eat food, drink potion

    const USE_ITEM_ON_ENTITY_ACTION_INTERACT = 0;
    const USE_ITEM_ON_ENTITY_ACTION_ATTACK = 1;

    /** @var int */
    public $transactionType;

    /** @var string
     *
     * NOTE: THIS FIELD DOES NOT EXIST IN THE PROTOCOL.
     *
     */
    public $inventoryType = "";

    /** @var NetworkInventoryAction[] */
    public $actions = [];

    /** @var \stdClass */
    public $trData;

    protected function decodePayload(){
        $this->transactionType = $this->getUnsignedVarInt();

        for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
            $this->actions[] = (new NetworkInventoryAction())->read($this);
        }

        $this->trData = new \stdClass();

        switch($this->transactionType){
            case self::TYPE_NORMAL:
            case self::TYPE_MISMATCH:
                //Regular ComplexInventoryTransaction doesn't read any extra data
                break;
            case self::TYPE_USE_ITEM:
                $this->trData->actionType = $this->getUnsignedVarInt();
                $this->getBlockPosition($this->trData->x, $this->trData->y, $this->trData->z);
                $this->trData->face = $this->getVarInt();
                $this->trData->hotbarSlot = $this->getVarInt();
                $this->trData->itemInHand = $this->getSlot();
                $this->trData->playerPos = $this->getVector3();
                $this->trData->clickPos = $this->getVector3();
                break;
            case self::TYPE_USE_ITEM_ON_ENTITY:
                $this->trData->entityRuntimeId = $this->getEntityRuntimeId();
                $this->trData->actionType = $this->getUnsignedVarInt();
                $this->trData->hotbarSlot = $this->getVarInt();
                $this->trData->itemInHand = $this->getSlot();
                $this->trData->vector1 = $this->getVector3();
                $this->trData->vector2 = $this->getVector3();
                break;
            case self::TYPE_RELEASE_ITEM:
                $this->trData->actionType = $this->getUnsignedVarInt();
                $this->trData->hotbarSlot = $this->getVarInt();
                $this->trData->itemInHand = $this->getSlot();
                $this->trData->headPos = $this->getVector3();
                break;
            default:
                throw new \UnexpectedValueException("Unknown transaction type $this->transactionType");
        }
    }

    protected function encodePayload(){
        $this->putUnsignedVarInt($this->transactionType);

        $this->putUnsignedVarInt(count($this->actions));
        foreach($this->actions as $action){
            $action->write($this);
        }

        switch($this->transactionType){
            case self::TYPE_NORMAL:
            case self::TYPE_MISMATCH:
                break;
            case self::TYPE_USE_ITEM:
                $this->putUnsignedVarInt($this->trData->actionType);
                $this->putBlockPosition($this->trData->x, $this->trData->y, $this->trData->z);
                $this->putVarInt($this->trData->face);
                $this->putVarInt($this->trData->hotbarSlot);
                $this->putSlot($this->trData->itemInHand);
                $this->putVector3($this->trData->playerPos);
                $this->putVector3($this->trData->clickPos);
                break;
            case self::TYPE_USE_ITEM_ON_ENTITY:
                $this->putEntityRuntimeId($this->trData->entityRuntimeId);
                $this->putUnsignedVarInt($this->trData->actionType);
                $this->putVarInt($this->trData->hotbarSlot);
                $this->putSlot($this->trData->itemInHand);
                $this->putVector3($this->trData->vector1);
                $this->putVector3($this->trData->vector2);
                break;
            case self::TYPE_RELEASE_ITEM:
                $this->putUnsignedVarInt($this->trData->actionType);
                $this->putVarInt($this->trData->hotbarSlot);
                $this->putSlot($this->trData->itemInHand);
                $this->putVector3($this->trData->headPos);
                break;
            default:
                throw new \UnexpectedValueException("Unknown transaction type $this->transactionType");
        }
    }
}