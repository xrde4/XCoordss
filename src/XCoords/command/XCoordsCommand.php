<?php

declare(strict_types=1);

namespace xrde4\XCoords\command;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

class XCoordsCommand extends Command{

	public function __construct($plugin){

		parent::__construct("coords", $plugin->LanguageMessage("description"), "/coords");
	 	$this->setPermission('xcoords.perms');
		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){

		if (!$sender->hasPermission('xcoords.perms')) {
            $sender->sendMessage($this->plugin->LanguageMessage("no_permissions"));
            return FALSE;
        }	

        if(!$sender instanceof Player){
        	$sender->sendMessage("§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §c Exclusively in the game.");
        	return;
        }
		
		if($this->plugin->config->getNested(strtolower($sender->getName())) == "false"){
			$sender->sendMessage($this->plugin->LanguageMessage("coords_on_message"));
			$pk = new GameRulesChangedPacket();
			$pk->gameRules = ["showcoordinates" =>  new BoolGameRule(true, true)];
			$sender->getNetworkSession()->sendDataPacket($pk);
			$this->plugin->config->setNested(strtolower($sender->getName()), "true"); 
			$this->plugin->config->save();
			return;
		}else{
			$sender->sendMessage($this->plugin->LanguageMessage("coords_off_message"));
			$pk = new GameRulesChangedPacket();
			$pk->gameRules = ["showcoordinates" =>  new BoolGameRule(false, false)];
			$sender->getNetworkSession()->sendDataPacket($pk);
			$this->plugin->config->setNested(strtolower($sender->getName()), "false"); 
			$this->plugin->config->save();		
			return;
		}				
	}
}
