<?php

declare(strict_types=1);

namespace TPE\ShortCommands;
use TPE\ShortCommands\Commands\ChangeGamemode;
use TPE\ShortCommands\Commands\GiveItem;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("gm", new ChangeGamemode("gm", $this));
        $this->getServer()->getCommandMap()->register("giveitem", new GiveItem("giveitem", $this));

    }
}