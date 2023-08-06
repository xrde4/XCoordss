<?php

/**
 * GNU LESSER GENERAL PUBLIC LICENSE v3.0
 
██╗░░██╗██████╗░██████╗░███████╗██╗░░░██╗
╚██╗██╔╝██╔══██╗██╔══██╗██╔════╝██║░░░██║
░╚███╔╝░██████╔╝██║░░██║█████╗░░╚██╗░██╔╝
░██╔██╗░██╔══██╗██║░░██║██╔══╝░░░╚████╔╝░
██╔╝╚██╗██║░░██║██████╔╝███████╗░░╚██╔╝░░
╚═╝░░╚═╝╚═╝░░╚═╝╚═════╝░╚══════╝░░░╚═╝░░░
 
 If you find my plugin helpful, could you please consider giving it a star on my profile? 
 Your support means a lot to me! 🌟 Thank you! (https://github.com/xrde4)"
 */
 
declare(strict_types=1);
 
namespace XCoords;

use pocketmine\Server;
use pocketmine\utils\Config;
use XCoords\command\XCoordsCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

class XCoords extends PluginBase implements Listener
{
    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML,[
		    "language" => "UA",
			"coords_off_message_ua" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fВи успішно §cвимкнули §fвідображення координат.",
			"coords_on_message_ua" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fВи успішно §ввімкнули §fвідображення координат.",
			"no_permissions_ua" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fУ вас §cнедостатньо §fправ для використання цієї команди.",
			"description_ua" => "Вимкнути/увімкнути відображення координат.",			
			"coords_off_message" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fYou have successfully §cdisabled the §fcoordinate display.",
			"coords_on_message" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fYou have successfully §fenabled the §fcoordinate display.",	
			"no_permissions" => "§f§l§o[§c!§f] §8§l§o§fX§eC§r§8§7 :: §fYou not have permissions!",
			"description" => "Disable/enable display of coordinates."]);
	   	 $this->getServer()->getCommandMap()->register("coords", new XCoordsCommand($this));
    }
	
	 public function onJoin(PlayerJoinEvent $event){
		 if(!$this->config->exists(strtolower($event->getPlayer()->getName()))){
			 if($event->getPlayer()->hasPermission('coords.x.perms')){
				$pk = new GameRulesChangedPacket();
				$pk->gameRules = ["showcoordinates" =>  new BoolGameRule(true, true)];
				$event->getPlayer()->getNetworkSession()->sendDataPacket($pk);
				$this->config->setNested(strtolower($event->getPlayer()->getName()), "true"); 
				$this->config->save();
				return;
			 }else{
				$this->config->setNested(strtolower($event->getPlayer()->getName()), "false"); 
				$this->config->save();	
                return;				
			 }
		 }else{
			 if($this->config->getNested(strtolower($event->getPlayer()->getName())) == "true"){
				$pk = new GameRulesChangedPacket();
				$pk->gameRules = ["showcoordinates" =>  new BoolGameRule(true, true)];
				$event->getPlayer()->getNetworkSession()->sendDataPacket($pk);		
                return;				
		 }else{
				$pk = new GameRulesChangedPacket();
				$pk->gameRules = ["showcoordinates" =>  new BoolGameRule(false, false)];
				$event->getPlayer()->getNetworkSession()->sendDataPacket($pk);				 
		 }
	 }
}

	public function LanguageMessage($message){
		if($this->config->getNested("language") == "UA"){
			if($message == "coords_off_message"){
				return $this->config->getNested("coords_off_message_ua");
			}elseif($message == "coords_on_message"){
				return $this->config->getNested("coords_on_message_ua");
			}elseif($message == "no_permissions"){	
				return $this->config->getNested("no_permissions_ua");
			}elseif($message == "description"){	
				return $this->config->getNested("description_ua");				
			}
		}else{
			return $this->config->getNested($message);
		}
	}
}
	