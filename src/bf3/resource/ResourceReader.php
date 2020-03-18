<?php

namespace bf3\resource;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use bf3\BattleFront3;
use pocketmine\entity\Skin;

class ResourceReader
{

    public static function getSkinData(string $filename) : string {
        $path = BattleFront3::RESOURCE_PATH . "skin" . DIRECTORY_SEPARATOR . "image" .DIRECTORY_SEPARATOR . $filename;
        $img = @imagecreatefrompng($path);
        $skinImage = "";
        $lx = (int) @getimagesize($path)[0];
        $ly = (int) @getimagesize($path)[1];
        for ($y = 0; $y < $ly; $y++) {
            for ($x = 0; $x < $lx; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $skinImage .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        return $skinImage;
    }

    public static function getGeometoryData(string $filename) : string {
        $path = BattleFront3::RESOURCE_PATH . "skin" . DIRECTORY_SEPARATOR . "geometory" .DIRECTORY_SEPARATOR . $filename;
        return file_get_contents($path);
    }

    public static function generateSkin(string $imageFile, string $geometoryFile, string $geometoryName, $capeFile = null){
        $skinId = str_replace(".png", "", $imageFile);
        $skinData = self::getSkinData($imageFile);
        $capeData = $capeFile === null ? "" : self::getSkinData($capeFile);
        $geometoryData = self::getGeometoryData($geometoryFile);

        return new Skin($skinId, $skinData, $capeData, $geometoryName, $geometoryData);
    }

}