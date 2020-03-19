<?php

namespace bf3\game\games\tdm;

use bf3\BattleFront3;
use bf3\game\GameEventHandler;
use bf3\game\GameManager;
use bf3\game\games\Game;
use bf3\game\games\GameEventListener;
use bfguns\event\EntityDamageByWeaponEvent;
use pocketmine\block\Air;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\GoldenApple;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\LeatherCap;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;

class TDMEventHandler extends GameEventListener
{

    /* @var $game TeamDeathMatch*/
    protected $game;

    public function onArmorChange(EntityArmorChangeEvent $event){
        $player = $event->getEntity();
        if($player instanceof Player && $this->game->isPlayer($player) && $event->getSlot() === ArmorInventory::SLOT_HEAD && !$event->getNewItem() instanceof LeatherCap){//良い書き方無いかなーーーー
            $event->setCancelled(true);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($this->game->isPlayer($player)){
            $player->getInventory()->remove(Item::get(ItemIds::GOLDEN_APPLE, 0, 64));
            $damageCause = $player->getLastDamageCause();
            if($damageCause instanceof EntityDamageByEntityEvent){
                $killer = $damageCause->getDamager();
                if($killer instanceof Player && $this->game->isPlayer($killer)){
                    $killerTeam = $this->game->getTeam($killer);
                    $playerTeam = $this->game->getTeam($player);
                    $this->game->addKillPoint($killerTeam);
                    $killer->getInventory()->addItem(new GoldenApple());

                    $cause = $damageCause instanceof EntityDamageByWeaponEvent ? $damageCause->getWeapon()->getName() : "KILL";
                    $event->setDeathMessage("§c§l⚔§r§7[§f" . $killer->getDisplayName() . "§r§7]§8 ---> §7[§f" . $cause . "§r§7]§8 --->§7 [§r§f" . $player->getDisplayName() . "§r§7]§r");
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $event){
        if($event instanceof EntityDamageByEntityEvent){
            $player = $event->getEntity();
            $attacker = $event->getDamager();
            if($player instanceof Player && $attacker instanceof Player && $this->game->isPlayer($player) && $this->game->isPlayer($attacker)){
                if($this->game->getTeam($player) !== $this->game->getTeam($attacker) && $this->game->getTimeTableStatus() === 0){
                    $event->setCancelled(false);
                }
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event){
        $player = $event->getPlayer();
        if($this->game->isPlayer($player)){
            BattleFront3::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function ($currentTick) use ($player) : void{
                    if($player->isOnline() && GameManager::isPlayer($player)){
                        $player->addEffect(new EffectInstance(Effect::getEffect(10), 20 * 7, 10, false));
                        $player->addEffect(new EffectInstance(Effect::getEffect(11), 20 * 7, 10, false));
                    }
                }
            ),1);
        }
    }

}