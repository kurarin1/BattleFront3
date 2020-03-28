<?php

namespace bf3\game\task;

use bf3\game\GameManager;
use pocketmine\scheduler\Task;

class GameIntervalTask extends Task{

    const INTERVAL = 1;

    private $i = 0;

    public function onRun(int $tick): void{
        if(GameManager::getNextGame()::MIN_PLAYER <= count(GameManager::getAllAppliers())){
            $this->i++;
            if($this->i >= self::INTERVAL){
                GameManager::shiftGame();
                $this->getHandler()->cancel();
                return;
            }
        }
        else{
            $this->i = 0;
        }
        GameManager::getLobbyInfoDummy()->setName("====§lBattleFront§c3§r§f====\n" .
            "§lNext>> §r§f" . GameManager::getNextGame()::GAME_NAME . "\n" .
            "§lAppliers>> §r§f" . count(GameManager::getAllAppliers()) . "人\n" .
            "§lTime>> §r§f" . ($this->i === 0 ? "--" : (self::INTERVAL - $this->i)) . "秒");
    }
}