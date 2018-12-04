<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 14.06.2017
 * Time: 19:20
 */
class Security extends Kohana_Security
{
    public static function block_client(){
            Cache::instance('file')->set('client'.Request::current()->client()->get_ip_env().'blocked',true,Date::DAY * 3);
    }

    public static function client_blocked(){
        Cache::instance('file')->delete('client'.Request::current()->client()->get_ip_env().'blocked');
       return Cache::instance('file')->get('client'.Request::current()->client()->get_ip_env().'blocked',false);
    }

    public static function mousetrapLink(){
        $arr = ['wp-admin','admin','administrator','manager','manage','wp-login.php','admin.php','user','login'];
        $idx = 0;
        $size = count($arr)-1;
        for($i = rand(1,23); $i>0; $i--){
            shuffle($arr);
            $idx = rand(0,$size);
        }
        $uri = Route::url('site.security',['lang' => Language::getCurrent()->slug,'key' => $arr[$idx]]);
        return HTML::anchor($uri,'Admin Panel',['style' => 'display:none;!important','class' => 'hidden']);
    }

    public static function mousetrapRandLink(){
        $int = rand(0,9);
        $int1 = rand(10,99);
        $str = Text::random('alpha',2);
        $key = Text::random('alnum',rand(25,46));
        $uri = Route::url('site.securityRnd',['lang' => Language::getCurrent()->slug,'key' => $key, 'int' => $int, 'int1' => $int1, 'str' => $str]);
        return HTML::anchor($uri,'user '.Text::random('alnum',rand(8,12)),['style' => 'display:none;!important','class' => 'hidden']);
    }

}