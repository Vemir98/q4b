<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.11.2016
 * Time: 4:42
 */
class Language
{
    private static $_instance;
    private $_data = [];
    private $_default;
    private $_current;
    private function __clone(){}
    private function __wakeup(){}
    
    const PARAM_SLUG = 'slug', PARAM_ISO2 = 'iso2', PARAM_LOCALE = 'locale';

    private function __construct()
    {
        $defaultLang = Settings::getValue('defaultLang');
        $items = ORM::factory('Language')->where('status','=','active')->find_all();
        
        foreach ($items as $i){
            $tmp = new stdClass();
            foreach ($i->as_array() as $key => $val){
                $tmp->{$key} = $val;
            }
            $this->_data []= $tmp;

            if(is_null($this->_default) AND $defaultLang == $tmp->slug){
                $this->_default = $tmp;
                if(is_null($this->_current)){
                    $this->_current = $this->_default;
                }
            }
        }
    }

    public static function instance(){
        if(!self::$_instance){
            self::$_instance = new static;
        }
        return self::$_instance;
    }

    public static function getDefault(){
        return self::instance()->_default;
    }   

    public static function getAll(){
        return self::instance()->_data;
    }

    public static function getLangByIso2($iso2){    
        foreach(self::instance()->_data as $lang){
            if($lang->iso2 == $iso2) return $lang;
        }
        return null;
    }

    public static function getLangBySlug($slug){
        foreach(self::instance()->_data as $lang){
            if($lang->slug == $slug) return $lang;
        }
        return null;
    }

    public static function getLangByLocale($locale){
        foreach(self::instance()->_data as $lang){
            if($lang->locale == $locale) return $lang;
        }
        return null;
    }

    public static function getRouteRule(){
        $output = [];
        foreach(self::instance()->_data as $lang){
            if($lang->slug != self::getDefault()->slug){
                $output [] = $lang->slug;
            }
        }
        return !empty($output) ? ['lang' => implode('|',$output)] : [];
    }   
    
    public static function getCurrent(){
        return self::instance()->_current;
    }

    public static function setCurrentLang($lang,$byParam = 'iso2'){
        if($byParam == 'iso2')
            self::instance()->_current = self::getLangByIso2($lang);
        elseif($byParam == 'slug')
            self::instance()->_current = self::getLangBySlug($lang);
        elseif($byParam == 'locale')
            self::instance()->_current = self::getLangByLocale($lang);

        return self::instance();
    }

    public static function getUrlUriSegment(){
        $defaultLang = self::getDefault();
        $currentLang = self::getCurrent();
        if($defaultLang->iso2 == $currentLang->iso2){
            return null;
        }else{
            return $currentLang->slug;
        }
    }
}