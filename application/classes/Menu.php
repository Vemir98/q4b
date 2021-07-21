<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.05.2021
 * Time: 10:53
 */

class Menu
{
    public static function setActiveItems(&$items, $parentIndex = false){

        foreach ($items as $key => &$item){

            if(strpos($item['slug'],':') !== false){
                preg_match('~\/?\:(?<param>[^/]+)~',$item['slug'],$matches);
                if(!empty($matches['param']) AND !empty(Request::current()->param($matches['param']))){
                    $item['slug'] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['slug']);
                  // var_dump($item['slug']);
                }else{
                    $item['disabled'] = true;
                }
            }

            if((!empty($item['slug']) AND strpos(URL::site(Request::detect_uri(), TRUE) . URL::query(),URL::site($item['slug'])) !== false)){

                $item['active'] = true;
                if($parentIndex !== false){
                    $items[$parentIndex]['active'] = true;
                }
            }else{
                $item['active'] = false;
            }

            if(!empty($item['children'])){
               self::setActiveItems($item['children'],$key);
            }

            if($item['disabled']){
                $item['slug'] = '#';
            }
        }

    }
}