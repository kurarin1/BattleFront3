<?php

namespace bf3\game\games\tdm\bossbar;

use bossbarapi\bossbar\BossBar;
use pocketmine\Player;

class TDMBossBar extends BossBar
{

    public function __construct(Player $player){
        parent::__construct($player);
    }

}