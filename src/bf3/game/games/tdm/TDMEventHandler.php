<?php

namespace bf3\game\games\tdm;

use bf3\game\GameEventHandler;
use bf3\game\games\Game;
use bf3\game\games\GameEventListener;
use pocketmine\block\Air;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\GoldenApple;
use pocketmine\Player;

class TDMEventHandler extends GameEventListener
{

    /* @var $game TeamDeathMatch*/
    protected $game;

    public function onArmorChange(EntityArmorChangeEvent $event){
        $player = $event->getEntity();
        if($player instanceof Player && $this->game->isPlayer($player) && $event->getSlot() === ArmorInventory::SLOT_HEAD){//良い書き方無いかなーーーー
            $player->getInventory()->sendContents([$player]);
            $event->setCancelled(true);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($this->game->isPlayer($player)){
            $damageCause = $player->getLastDamageCause();
            if($damageCause instanceof EntityDamageByEntityEvent){
                $killer = $damageCause->getDamager();
                if($killer instanceof Player && $this->game->isPlayer($killer)){
                    $killerTeam = $this->game->getTeam($killer);
                    $playerTeam = $this->game->getTeam($player);
                    $this->game->addKillPoint($killerTeam);
                    $killer->getInventory()->addItem(new GoldenApple());
                }
            }
        }
    }

}