<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.08.2017
 * Time: 12:08
 */
class Listener_Settings_Task
{
    const filePath = APPPATH.'local-storage/settings.txt';
    /**
     * Конвертация pdf в изображение
     * @param $event
     * @param $sender
     * @param Model_Task $item
     */
    public static function added($event,$sender,Model_Task $item){
        if(file_exists(APPPATH.'local-storage/settings.txt')) {
            $rawData = file_get_contents(self::filePath);
        }else{
            $rawData = false;
        }
        $data = null;
        if($rawData !== false){
            $data = json_decode($rawData,true);
        }
        $data['tasks']['added'][] = $item->id;
        file_put_contents(self::filePath,json_encode($data));
    }
}