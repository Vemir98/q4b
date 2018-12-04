<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.08.2017
 * Time: 10:51
 */
class Listener_Settings_Craft
{
    const filePath = APPPATH.'local-storage/settings.txt';
    /**
     * Конвертация pdf в изображение
     * @param $event
     * @param $sender
     * @param Model_Craft $item
     */
    public static function added($event,$sender,Model_Craft $item){
        if(file_exists(APPPATH.'local-storage/settings.txt')) {
            $rawData = file_get_contents(self::filePath);
        }else{
            $rawData = false;
        }
        $data = null;
        if($rawData !== false){
            $data = json_decode($rawData,true);
        }
        $data['crafts']['added'][] = $item->id;
        file_put_contents(self::filePath,json_encode($data));
    }
}