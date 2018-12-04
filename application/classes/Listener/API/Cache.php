<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 27.04.2018
 * Time: 5:08
 */

class Listener_API_Cache
{
    const CACHE_DIR = APPPATH.'local-storage/api/';

    public static function clear($event,$sender,$item,$clientID = null){

        //$signal = $event->get_signal()->signal();
        if($item instanceof Model_Company){
            $clientId = ((int)$item->client->id);
            $filename = self::CACHE_DIR.'/companies/list-'.$clientId.'.json';
            if(file_exists($filename)){
                unlink($filename);
            }

            if($clientId != 0){
                $filename = self::CACHE_DIR.'/companies/list-0.json';
                if(file_exists($filename)){
                    unlink($filename);
                }
            }
        }elseif ($item instanceof Model_CmpCraft OR $item instanceof Model_CmpProfession){
            $filename = self::CACHE_DIR.'/companies/crafts-professions/list-'.((int)$item->company_id).'.json';
            if(file_exists($filename)){
                unlink($filename);
            }
        }elseif ($item instanceof Model_Project){
            $clientId = ((int)$item->client_id);
            $filename = self::CACHE_DIR.'/projects/list-'.$clientId.'.json';
            if(file_exists($filename)){
                unlink($filename);
            }
            if($clientId != 0){
                $filename = self::CACHE_DIR.'/projects/list-0.json';
                if(file_exists($filename)){
                    unlink($filename);
                }
            }
        }elseif ($item instanceof Model_PrObject){
            $filename = self::CACHE_DIR.'/projects/'.((int)$item->project_id).'/objects/list-'.$clientID.'.json';
            if(file_exists($filename)){
                unlink($filename);
            }
            if((int)$clientID != 0){
                $filename = self::CACHE_DIR.'/projects/'.((int)$item->project_id).'/objects/list-0.json';
                if(file_exists($filename)){
                    unlink($filename);
                }
            }

            $filename = self::CACHE_DIR.'/projects/object-structures/list-'.$item->id.'.json';
            if(file_exists($filename)){
                unlink($filename);
            }
        }elseif ($item instanceof Model_PrPlan){
            $filename = self::CACHE_DIR.'/projects/plans/list-'.((int)$item->project_id).'.json';
            if(file_exists($filename)){
                unlink($filename);
            }
        }
    }

    public static function clearTasksCache($event,$sender,$item){
        $filename = self::CACHE_DIR.'/projects/tasks/list-'.$item->id.'.json';
        if(file_exists($filename)){
            unlink($filename);
        }
    }

}