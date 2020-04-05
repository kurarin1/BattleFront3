<?php

namespace bf3\firingrange\entity;

use bf3\npc\AngelRing;
use bf3\resource\ResourceReader;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;

class TargetV1 extends Human
{

    private static $skinCache = null;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        if(self::$skinCache === null){
            self::$skinCache = ResourceReader::generateSkin("targetv1.png", "targetv1.json", "geometry.targetvone");
        }
        $this->setSkin(self::$skinCache);
        parent::__construct($level, $nbt);
    }

    public function attack(EntityDamageEvent $source) : void
    {
        $source->call();
        $this->setLastDamageCause($source);

        if($source->getCause() === EntityDamageEvent::CAUSE_MAGIC) $this->kill();
    }


    protected function sendSpawnPacket(Player $player): void
    {
        parent::sendSpawnPacket($player);
    }

    public function setHealth(float $amount) : void{}

    public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.4) : void{}

}