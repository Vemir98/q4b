<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.09.2016
 * Time: 16:41
 */
class UserDebug
{
    protected static $_instance;
    private function __sleep(){}
    private function __wakeup(){}
    private function __clone(){}

    /**
     * @var Auth
     */
    private $_auth;

    /**
     * @var Model_User
     */
    private $_user;

    /**
     * @var \GeoIp2\Record\Country
     */
    private $_country;

    private $_acceptLangs = "";

    /**
     * @var array
     */
    private $_user_roles = [];

    private function __construct(){
        $this->_auth = Auth::instance();
        $this->_user = $this->_auth->get_user();
        if( ! $this->_auth->logged_in()){
            $this->_user_roles = ['guest'];
            $this->_user = ORM::factory('User');
        }else{
            foreach ($this->_user->roles->find_all() as $role){
                $this->_user_roles []= $role->name;
            }
        }
        $reader   = new \GeoIp2\Database\Reader(APPPATH.'local-storage'.DS.'GeoLite2-City.mmdb');
        //Request::current()->client()->get_ip_server()
        $record = $reader->city(Request::current()->client()->get_ip_env() != '127.0.0.1' ?: '37.252.93.223');
        $this->_country = $record->country;

        $this->_acceptLangs = '<span style="color:#B70000;">';
        foreach (Request::$current->client()->getAcceptLanguages() as $lang => $pers){
            $this->_acceptLangs .= $lang.' - '.($pers * 100).'% ';
        }
        $this->_acceptLangs .= '</span>';

    }

    public static function instance(){
        if(self::$_instance == null){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function getData(){
        return sprintf("Роли пользователя: (%s). Email: %s. Страна - %s, Код страны ISO2 - %s. предпочитаемые языки - %s",implode(', ',$this->_user_roles), $this->_user->email ?: '-', $this->_country->name, $this->_country->isoCode, $this->_acceptLangs);
    }
    public function __toString(){

        return "<div style='background-color: black; color: #009d00;'><pre style='margin: 0; padding: 10px;'>User debug bar\n" .$this->getData()."</pre></div>";
    }
}