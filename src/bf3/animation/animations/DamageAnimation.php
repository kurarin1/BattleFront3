<?php

namespace bf3\animation\animations;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Player;

class DamageAnimation extends Animation
{

    /* @var $attacker Player*/
    private $attacker;
    /* @var $entity Entity*/
    private $entity;
    /* @var $damage float*/
    private $damage;
    /* @var $eid int*/
    private $eid;

    public function __construct(Player $attacker, Entity $entity, float $damage){
        $this->attacker = $attacker;
        $this->entity = $entity;
        $this->damage = $damage;
        parent::__construct();
    }

    public function init(){
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->eid = Entity::$entityCount++;
        $pk->type = EntityIds::ITEM;
        $pk->position = $this->entity->add(mt_rand(-10, 10) * 0.01, $this->entity->getEyeHeight()/2, mt_rand(-10, 10) * 0.01);
        $pk->motion = new Vector3(mt_rand(-10, 10) * 0.005, 0.21, mt_rand(-10, 10) * 0.005);
        $pk->metadata = [
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, (1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG)],
            Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "§c§l" . round($this->damage, 2)],
            Entity::DATA_ALWAYS_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
            Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1]
        ];

        $this->attacker->dataPacket($pk);
    }

    public function fin(){
        if($this->attacker->isOnline()){
            $pk = new RemoveActorPacket();
            $pk->entityUniqueId = $this->eid;

            $this->attacker->dataPacket($pk);
        }
    }

    public function onUpdate(int $currentTick){
        parent::onUpdate($currentTick);
        if($this->age > 12){
            $this->close();
        }
    }

}