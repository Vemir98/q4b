<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.09.2016
 * Time: 17:11
 * Ядро HDVP (шаблон одиночка)
 */
class HDVP_Core
{
    private static $_inited = false, $_instance = null;
    private $_acl;

    private function __sleep(){}
    private function __wakeup(){}
    private function __clone(){}

    public static function instance()
    {
        if(self::$_instance == null)
            self::$_instance = new self;
        return self::$_instance;
    }

    /**
     * @param Event_Instance $event
     * @throws Kohana_Exception
     */
    public static function init(Event_Instance $event){
        if(self::$_inited) throw new Kohana_Exception('cant init core twice');
        self::instance();
        //Устанавливаем соль для кук
        if( ! file_exists(APPPATH.'config/cookie_salt')){
            Cookie::$salt = Text::random(null,32);
            Cookie::$httponly = TRUE;
            file_put_contents(APPPATH.'config/cookie_salt',Cookie::$salt);
        }else{
            Cookie::$salt = file_get_contents(APPPATH.'config/cookie_salt');
        }
        // Отключаем в заголовках kohana powered
        Kohana::$expose = FALSE;

        self::register_routes();
        self::register_vendor_autoloader();
        self::registerAppListeners();

//        self::$_inited = true;
    }

    public function acl(){
        if( ! $this->_acl){
            $this->_acl = Model::factory('Acl');
        }
        return $this->_acl;
    }

    /**
     * Регистрирует роуты системы
     */
    private static function register_routes()
    {
        self::_recursiveRequireFilesArray(Kohana::list_files('routes'));
        Event::instance()->fire('onRoutesRegistered');
    }

    /**
     * Рекурсивно подключает файлы указанные в передаваемом массиве
     * @param $arr - массив с путями файлов
     */
    protected static function _recursiveRequireFilesArray($arr){
        if(is_array($arr)){
            foreach ($arr as $a){
                self::_recursiveRequireFilesArray($a);
            }
        }else{
            if(is_file($arr))
            require_once ($arr);
        }
    }

    /**
     * Регистрирует автозагрузчик для сторонних модулей PHP
     */
    private static function register_vendor_autoloader(){
        Kohana::load(Kohana::find_file('vendor','autoload'));
        //подгружаем фейкер $faker = Faker\Factory::create();
        require_once APPPATH.'vendor/fzaninotto/faker/src/autoload.php';
    }

    private static function registerAppListeners(){
        $data = Kohana::$config->load('app-listeners');
        if(!empty($data)){
            foreach ($data as $event => $listeners){
                if(!empty($listeners)){
                    foreach ($listeners as $listener){
                        if( is_string($listener) AND strpos($listener,'::')){
                            $pieces = explode('::',$listener);
                            Event::instance()->listen($event,[$pieces[0],$pieces[1]]);
                        }else if(is_callable($listener)){
                            Event::instance()->listen($event,$listener);
                        }
                    }
                }
            }
        }
    }
}