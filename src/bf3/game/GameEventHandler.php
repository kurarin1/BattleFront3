<?php

namespace bf3\game;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class GameEventHandler implements Listener{

    /**
     * @priority NORMAL
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event){
        if(GameManager::isGaming()){
            if(GameManager::isPlayer($event->getPlayer())) GameManager::getGame()->quit($event->getPlayer());
        }
    }

    /**
     * @priority NORMAL
     * @param EntityArmorChangeEvent $event
     */
    public function onArmorChange(EntityArmorChangeEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onArmorChange($event);
        }
    }

    /**
     * @priority NORMAL
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onPlayerDeath($event);
        }
    }

    /**
     * @priority NORMAL
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onDamage($event);
        }
    }

    /**
     * @priority NORMAL
     * @param PlayerRespawnEvent $event
     */
    public function onRespawn(PlayerRespawnEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onRespawn($event);
        }
    }

}