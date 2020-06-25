<?php

declare(strict_types=1);

namespace TPE\ShortCommands\Commands;

use TPE\ShortCommands\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\ItemFactory;
use pocketmine\lang\TranslationContainer;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class GiveItem extends PluginCommand {
    private $plugin;
    public function __construct(string $name, Main $plugin)
    {
        parent::__construct($name, $plugin);
        $this->setDescription("Give items to yourself or other players");
        $this->setPermission("shortcommands.giveitem");
        $this->setUsage("/giveitem [player] [item] [amount]");
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            return true;
        }
        if(count($args) < 2) {
            throw new InvalidCommandSyntaxException();
        }
        $player = $sender->getServer()->getPlayer($args[0]);
        if($player === null) {
            $sender->sendMessage(TextFormat::RED . "Player not found!");
            return true;
        }
        try{
            $item = ItemFactory::fromString($args[1]);
        } catch(\InvalidArgumentException $e){
            $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.give.item.notFound", [$args[1]]));
            return true;
        }
        if(!isset($args[2])){
            $item->setCount($item->getMaxStackSize());
        } else {
            $item->setCount((int) $args[2]);
        }

        if(isset($args[3])){
            $tags = $exception = null;
            $data = implode(" ", array_splice($args, 3));
            try{
                $tags = JsonNbtParser::parseJson($data);
            } catch(\Exception $ex) {
                $exception = $ex;
            }

            if(!($tags instanceof CompoundTag) or $exception !== null) {
                $sender->sendMessage(new TranslationContainer("commands.give.tagError", [$exception !== null ? $exception->getMessage() : "Invalid tag conversion"]));
                return true;
            }

            $item->setNamedTag($tags);

        }
        $player->getInventory()->addItem(clone $item);
        return true;
    }
}

