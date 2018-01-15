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

namespace pocketmine\inventory\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;

class EnchantTransaction extends InventoryTransaction{

    public function canExecute(): bool{
        $rm = [];
        foreach($this->getActions() as $action){
            if($action instanceof SlotChangeAction){
                if($action->getSlot() == -1){
                    $rm[] = $action;
                }
            }
        }

        foreach($rm as $action){
            $key = array_search($action, $this->actions);
            unset($this->actions[$key]);
        }
        $this->squashDuplicateSlotChanges();
        if (count($rm) > 0) {
            return true;
        } else {
            return parent::canExecute();
        }
    }
}