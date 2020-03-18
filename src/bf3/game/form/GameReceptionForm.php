<?php

namespace bf3\game\form;

use bf3\game\GameManager;
use pocketmine\Player;
use pocketmine\form\Form;

class GameReceptionForm implements Form
{
    /* @var $player Player*/
    private $player;
    /* @var $title string*/
    private $title;

    public function __construct(Player $player, $title = "マッチ受付"){
        $this->player = $player;
        $this->title = $title;
    }

    public function handleResponse(Player $player, $data): void{
        if($data !== true) return;//nullの場合もあるので

        if(GameManager::isGaming()){
            GameManager::getGame()->join($player);
        }
        else{
            GameManager::isApplied($this->player) ? GameManager::unApply($player) : GameManager::apply($player);
        }
    }

    public function jsonSerialize(){
        $content = "";
        $button1 = "";
        if(GameManager::isGaming()){
            $content = "§a" . GameManager::getGame()->getName() . "§fが現在行われています";
            $button1 = "途中参加する";
        }
        else{
            $content = "§a" . GameManager::getNextGame()::GAME_NAME . "§fの参加申請を受付中です";
            $button1 = GameManager::isApplied($this->player) ? "参加申請を取り消す" : "参加申請";
        }
        return [
            'type' => 'modal',
            'title' => $this->title,
            'content' => $content,
            'button1' => $button1,
            'button2' => '閉じる'
        ];
    }
}