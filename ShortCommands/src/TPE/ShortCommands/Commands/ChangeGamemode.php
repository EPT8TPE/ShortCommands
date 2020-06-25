<?php

declare(strict_types=1);

namespace TPE\ShortCommands\Commands;

use TPE\ShortCommands\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ChangeGamemode extends PluginCommand {
    private $plugin;

    public function __construct(string $name, Main $plugin) {
        parent::__construct($name, $plugin);
        $this->setDescription("Change your gamemode or the gamemode of others");
        $this->setPermission("shortcommands.gm");
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)){
            return true;
        }
        if(count($args) === 0) {
            throw new InvalidCommandSyntaxException();
        }
        $gameMode = Server::getGamemodeFromString($args[0]);
        if($gameMode === -1){
            $sender->sendMessage(TextFormat::RED . "Unknown gamemode");
            return true;
        }
        if(isset($args[1])) {
            $target = $sender->getServer()->getPlayer($args[1]);
            if($target === null) {
                $sender->sendMessage(TextFormat::RED . "Player not found");
                return true;
            }
        } elseif($sender instanceof Player){
            $target = $sender;
        } else {
            throw new InvalidCommandSyntaxException();
        }
        $target->setGamemode($gameMode);
        if($gameMode !== $target->getGamemode()) {
            $sender->sendMessage(TextFormat::RED . "Game mode change for " . $target->getName() . "failed!");
        } else {
            if($target === $sender) {
                $target->sendMessage("§l§2..§e[§dGM§e]§2..§r§f You have changed your gamemode to " . Server::getGamemodeString($gameMode));
            } else {
                $target->sendMessage("§l§2..§e[§dGMS§e]§2..§r§f Your gamemode has been changed to " . Server::getGamemodeString($gameMode) . "by " . $sender->getName());
            }
        }
        return true;
    }
}