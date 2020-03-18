<?php

namespace bf3\game\games;

use bf3\game\GameManager;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;

abstract class GameEventListener
{
    /* @var $game Game*/
    protected $game;

    public function __construct(Game $game){
        $this->game = $game;
    }

    public function onArmorChange(EntityArmorChangeEvent $event){}

    public function onPlayerDeath(PlayerDeathEvent $event){}

    public function onDamage(EntityDamageEvent $event){}

}