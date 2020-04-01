<?php

namespace bf3\hub;

use bossbarapi\bossbar\BossBar;

class HubBossBar extends BossBar
{

    const TIPS = ["Tips：このサーバーは開発段階です...", "§bTwitter§f：@MinecraftBF3", "§9Discord§f：https://discord.gg/KbHHdJa"];
    const MAX_PROGRESS = 15;
    /* @var $progress int*/
    private $progress = self::MAX_PROGRESS;
    /* @var $tip int*/
    private $tip = -1;

    public function init(){
        parent::init();
        $this->shiftTitle();
    }

    public function onUpdate(int $currentTick)
    {
        parent::onUpdate($currentTick);
        if($currentTick % 20 === 0){
            $this->progress--;
            $this->setPercentage($this->progress / self::MAX_PROGRESS);
            if($this->progress < 1){
                $this->progress = self::MAX_PROGRESS;
                $this->setPercentage(1.0);
                $this->shiftTitle();
            }
        }
    }

    private function shiftTitle(){
        $this->tip++;
        if(!isset(self::TIPS[$this->tip])) $this->tip = 0;
        $this->setTitle(self::TIPS[$this->tip]);
    }

}