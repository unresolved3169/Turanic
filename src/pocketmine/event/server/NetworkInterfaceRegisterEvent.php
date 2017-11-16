<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;

/**
 * Called when a network interface is registered into the network, for example the RakLib interface.
 */
class NetworkInterfaceRegisterEvent extends NetworkInterfaceEvent implements Cancellable{
    public static $handlerList = null;

}