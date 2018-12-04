<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.11.2016
 * Time: 4:49
 */
class Settings
{
    protected static $_instance;
    protected $_data = [];

    private function __clone(){}
    private function __wakeup(){}

    private function __construct(){
        $items = ORM::factory('Settings')->find_all();
        if(count($items)){
            foreach ($items as $i){
                $key = lcfirst($i->key);
                $this->_data[$key] = new stdClass();
                $this->_data[$key]->name = $i->name;
                $this->_data[$key]->description = $i->desc;
                $this->_data[$key]->value = $i->val;
            }
        }
    }

    public static function instance(){
        if(is_null(self::$_instance)){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __get($key){
        if(!isset($this->_data[$key])){
            throw new Kohana_Exception('Invalid data key :key',[':key' => $key]);
        }
        return clone $this->_data[$key];
    }

    public static function __callStatic($name, $arguments)
    {
        if(strpos($name,'get') === 0){
            $key = lcfirst(substr($name,3));
            return self::instance()->__get($key);
        }
        throw new Kohana_Exception('Invalid method :name',[':name' => $name]);
    }
    
    public static function getValue($setting){
        return self::instance()->__get($setting)->value;
    }

}