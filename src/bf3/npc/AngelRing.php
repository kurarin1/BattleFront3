<?php

namespace bf3\npc;

use bf3\game\form\GameReceptionForm;
use bf3\hub\TeleportForm;
use bf3\resource\ResourceReader;
use dummyapi\dummy\Dummy;
use dummyapi\DummyAPI;
use pocketmine\level\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\utils\UUID;

class AngelRing extends Dummy
{

    /* @var $owner Player*/
    private $owner;

    public function __construct(Location $location, Player $player){
        $this->owner = $player;
        parent::__construct(UUID::fromRandom(), $location, ResourceReader::generateSkin("angelring.png", "take.json", "geometry.take"), "");
    }

    public function onUpdate(int $currentTick){
        parent::onUpdate($currentTick);
        if(!$this->owner->isOnline()){
            DummyAPI::getInstance()->unregisterDummy($this);
            return;
        }

        $pk = new MovePlayerPacket();
        $pk->entityRuntimeId = $this->getEid();
        $pk->position = $this;
        $pk->pitch = 0;
        $pk->yaw = 0;
        $pk->headYaw = ($currentTick * 24) % 360;
        $pk->ridingEid = $this->owner->getId();

        $this->owner->dataPacket($pk);
    }
}