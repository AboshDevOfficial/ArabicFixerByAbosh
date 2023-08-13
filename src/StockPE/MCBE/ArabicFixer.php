<?php
declare(strict_types=1);
/*
  _____ _             _    _____  ______
 / ____| |           | |  |  __ \|  ____|
| (___ | |_ ___   ___| | _| |__) | |__
 \___ \| __/ _ \ / __| |/ /  ___/|  __|
 ____) | || (_) | (__|   <| |    | |____
|_____/ \__\___/ \___|_|\_\_|    |______|
                                         */
namespace StockPE\MCBE;

//pmmp libs!
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\resourcepacks\ZippedResourcePack;

//mine libs!
use StockPE\MCBE\LangLib\LanguageDetector as isAR;

class ArabicFixer extends PluginBase implements Listener {

    private $pack;

    public function onLoad()
      {
        $this->saveResource("LavaPixelChat.devpack", true);
        $manager = $this->getServer()->getResourcePackManager();
        $this->pack = $pack = new ZippedResourcePack($this->getDataFolder() . "LavaPixelChat.devpack");
        $reflection = new \ReflectionClass($manager);
        $property = $reflection->getProperty("resourcePacks");
        $property->setAccessible(true);
        $currentResourcePacks = $property->getValue($manager);
        $currentResourcePacks[] = $pack;
        $property->setValue($manager, $currentResourcePacks);
        $property = $reflection->getProperty("uuidList");
        $property->setAccessible(true);
        $currentUUIDPacks = $property->getValue($manager);
        $currentUUIDPacks[strtolower($pack->getPackId())] = $pack;
        $property->setValue($manager, $currentUUIDPacks);
        $property = $reflection->getProperty("serverForceResources");
        $property->setAccessible(true);
        $property->setValue($manager, false);
      }

    public function onEnable()
      {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
	      $this->getServer()->getLogger()->info("[StockPE] fixed your chat, you can type arabic in the chat now.");
      }

    public function onDisable()
      {
  		  $manager = $this->getServer()->getResourcePackManager();
  		  $pack = new ZippedResourcePack($this->getFile()."resources/LavaPixelChat.devpack");
  		  $reflection = new \ReflectionClass($manager);
  		  $property = $reflection->getProperty("resourcePacks");
  		  $property->setAccessible(true);
  		  $currentResourcePacks = $property->getValue($manager);
  		  $key = array_search($pack, $currentResourcePacks);
  		  if($key !== false)
         {
  			   unset($currentResourcePacks[$key]);
  			   $property->setValue($manager, $currentResourcePacks);
  		   }
  		  $property = $reflection->getProperty("uuidList");
  		  $property->setAccessible(true);
  		  $currentUUIDPacks = $property->getValue($manager);
  		  if(isset($currentResourcePacks[strtolower($pack->getPackId())]))
         {
  			   unset($currentUUIDPacks[strtolower($pack->getPackId())]);
  			   $property->setValue($manager, $currentUUIDPacks);
  		   }
      }

    private function ar_strrev($string, $encoding = null)
      {
        if (is_null($encoding))
         {
           $encoding = mb_detect_encoding($string);
         }
        $length   = mb_strlen($string, $encoding);
        $reversed = '';
        while($length-->0)
          {
            $reversed .= mb_substr($string, $length, 1, $encoding);
          }
        return $reversed;
      }

    public function onChat(PlayerChatEvent $event)
      {
        //arabic alphabet
        $ArABic = array('  ' <= '  ',
                        'ﻻ' => 'ﻻ',
                        'الله' => 'ﻪﻠﻟﺍ',
                        'لافا' => 'ﺎﻓﻻ',
                        'بكسل' => 'ﻞﺴﻜﺑ',
                        'ض' => 'ﺿ',
                        'ﺿ*' => 'ضْ',
                        'ص' => 'ﺻ',
                        'ﺻ*' => 'صْ',
                        'ث' => 'ﺛ',
                        'ﺛ*' => 'ثْ',
                        'ق' => 'ﻗ',
                        'ﻗ*' => 'قْ',
                        'ف' => 'ﻓ',
                        'ﻓ*' => 'فْ',
                        'ڤ' => 'ڤـ',
                        'ڤـ*' => 'ڤْ',
                        'غ' => 'ﻏ',
                        'ﻏ*' => 'ﻐ',
                        'ﻐ*' => 'غْ',
                        'ع' => 'ﻋ',
                        'ﻋ*' => 'ﻌ',
                        'ﻌ*' => 'عْ',
                        'ه' => 'ﻫ',
                        'ﻫ*' => 'ﻬ',
                        'ﻬ*' => 'ﻪ',
                        'ﻪ*' => 'ﻩ',
                        'خ' => 'ﺧ',
                        'ﺧ*' => 'خْ',
                        'ح' => 'ﺣ',
                        'ﺣ*' => 'حْ',
                        'ج' => 'ﺟ',
                        'ﺟ*' => 'جْ',
                        'ش' => 'ﺷ',
                        '  ' <= '  ');
        $WRITTEN_TEXT = $event->getMessage();
        foreach(["ض" ,"ﺿ*" ,"ص" ,"ﺻ*" ,"ث" ,"ﺛ*" ,"ق" ,"ﻗ*" ,"ف" ,"ﻓ*" ,"ڤ" ,"ڤـ*" ,"غ" ,"ﻏ*" ,"ﻐ*" ,"ع" ,"ﻋ*" ,"ﻌ*" ,"ه" ,"ﻫ*" ,"ﻬ*" ,"ﻪ*" ,"خ" ,"ﺧ*" ,"ح" ,"ﺣ*" ,"ج" ,"ﺟ*" ,"ش"] as $LEttErS /*extra detection method to save on quality*/)
          {
            if(isAR::detect($WRITTEN_TEXT) == 'ar' || mb_strpos($WRITTEN_TEXT, $LEttErS, 0, 'UTF-8') !== false)
             {
               //fixer
               $FIXED_ALPHABET = str_replace(array_keys($ArABic), array_values($ArABic), $WRITTEN_TEXT);
               $FIXED_TEXT_REVERSED = $this->ar_strrev($FIXED_ALPHABET);
               $event->setMessage($FIXED_TEXT_REVERSED);
             } else
                 {
                   $event->setCancelled(false);
                 }
          }
      }
}
?>
