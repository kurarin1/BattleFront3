<?php

namespace bf3\game\games\tdm\task;

use bf3\game\games\tdm\TeamDeathMatch;
use pocketmine\scheduler\Task;

abstract class TDMTask extends Task
{

    private $game;

    public function __construct(TeamDeathMatch $tdm){
        $this->game = $tdm;
    }

    public function onRun(int $currentTick){

    }
}