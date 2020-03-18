<?php

namespace bf3\game;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class GameEventHandler implements Listener{

    /**
     * @priority LOW
     * @param EntityArmorChangeEvent $event
     */
    public function onArmorChange(EntityArmorChangeEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onArmorChange($event);
        }
    }

    /**
     * @priority LOW
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onPlayerDeath($event);
        }
    }

    /**
     * @priority LOW
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event){
        if(GameManager::isGaming()){
            GameManager::getGame()->getEventListener()->onDamage($event);
        }
    }

}