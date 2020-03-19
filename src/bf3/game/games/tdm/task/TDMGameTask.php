<?php

namespace bf3\game\games\tdm\task;

use bf3\game\GameManager;
use bf3\game\games\tdm\TeamDeathMatch;
use pocketmine\scheduler\Task;

class TDMGameTask extends TDMTask
{

    private $time = TeamDeathMatch::TIME;

    public function onRun(int $currentTick){
        if($this->game->getTimeTableStatus() !== 0){
            $this->getHandler()->cancel();
            return;
        }

        if($this->time < 0){
            $this->game->TimeTable();
            return;
        }

        GameManager::getLobbyInfoDummy()->setName("====§lBattleFront§c3§r§f====\n" .
            "§lGame>> §r§f" . TeamDeathMatch::GAME_NAME . "\n" .
            "§lPlayers>> §r§f" . count($this->game->getPlayers()) . "人\n" .
            "§lTime>> §r§f" .  str_pad(floor($this->time / 60), 2, "0", STR_PAD_LEFT) . " ： " . str_pad(round($this->time % 60), 2, "0", STR_PAD_LEFT));

        $this->time--;
    }
}