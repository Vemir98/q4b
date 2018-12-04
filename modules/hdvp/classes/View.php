<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 17:19
 */
class View extends Kohana_View
{
    public static function make($path,array $data = null){
        if( ! preg_match('~^web|mobile~',$path)){
            $detector = new Mobile_Detect;
            if($detector->isMobile()){
                $myPath = 'mobile/'.$path;
                if (Kohana::find_file('views', $myPath) !== FALSE)
                {
                    $path = $myPath;
                }else{
                    $path = 'web/'.$path;
                }
                View::set_global('device','mobile');
            }else{
                $path = 'web/'.$path;
                View::set_global('device','not specified');
            }
        }

        $viewpath = preg_replace('~\/[A-z0-9-_.]+$~','/',$path);
        $viewpath = ltrim(preg_replace('~^web|mobile$~','',$viewpath),'/');

        return static::factory($path,$data)->set('_VIEWPATH',$viewpath);
    }
}