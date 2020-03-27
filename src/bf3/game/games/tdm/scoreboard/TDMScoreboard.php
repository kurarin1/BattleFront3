<?php

namespace bf3\game\games\tdm\scoreboard;

use pocketmine\Player;
use scoreboardapi\scoreboard\Scoreboard;

class TDMScoreboard extends Scoreboard
{

    public function __construct(Player $player){
        parent::__construct($player);
    }

}