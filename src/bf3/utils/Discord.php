<?php

namespace bf3\utils;

use bf3\BattleFront3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class Discord
{

    const WEBHOOK_SERVER_CHAT = "https://discordapp.com/api/webhooks/541650717698687016/3m5GunFfaoU08ifnSDwsSZB48VkkyxwsNwNAf_h0BSVkRAPku95mZ_bn9kPLNoW_cC1Y";
    const WEBHOOK_GAMEINFO = "https://discordapp.com/api/webhooks/551401135555084288/BsDN4Qn8HtOKJfMTTDjWto1VZ2k6StXs5IfAJsvjlzFVMVNoN-jzQWIKTJNWolqfRXcW";

    public static function sendMessage(string $url, string $name, string $message, bool $async = true){//後で修正//todo
        if(BattleFront3::DEBUG) return;

        if($async){
            Server::getInstance()->getAsyncPool()->submitTask(new class($url, $name, $message, $async) extends AsyncTask{
                public function __construct(string $url, $name, $message, $async){
                    $this->url = $url;
                    $this->name = $name;
                    $this->message = str_replace(["§0", "§1", "§2", "§3", "§4", "§5", "§6", "§7", "§8", "§9", "§a", "§b", "§c", "§d", "§e", "§f", "§k", "§l", "§m", "§n", "§o", "§r"], "", $message);;
                }
                public function onRun(){
                    $options = [
                        'http' => [
                            'method' => 'POST',
                            'header' => 'Content-Type: application/json',
                            'content' => json_encode(['username' => $this->name, 'content' => $this->message]),
                        ],
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ];
                    file_get_contents($this->url, false, stream_context_create($options));
                }
            });
        }else{
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode(['username' => $name, 'content' => str_replace(["§0", "§1", "§2", "§3", "§4", "§5", "§6", "§7", "§8", "§9", "§a", "§b", "§c", "§d", "§e", "§f", "§k", "§l", "§m", "§n", "§o", "§r"], "", $message)]),
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ];
            file_get_contents($url, false, stream_context_create($options));
        }
    }

}