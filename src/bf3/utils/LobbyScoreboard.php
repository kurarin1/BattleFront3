<?php

namespace bf3\utils;

use pocketmine\Player;
use scoreboardapi\scoreboard\Scoreboard;

class LobbyScoreboard extends Scoreboard
{

    public function __construct(Player $player)
    {
        parent::__construct($player);
        $this->setDisplayName("BattleFront§c3§f");
        $this->setLine(0,"     §3v0.1.0    ");
        //$this->setLine(1,"§7-----------");
    }

}