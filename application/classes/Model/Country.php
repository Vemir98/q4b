<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.10.2016
 * Time: 17:23
 */
class Model_Country extends ORM
{
    protected $_table_name = 'countries';

    /**
     * Возвращает данные в виде массива для создания html  элемента select
     * [[id => name],...]
     * @return array
     */
    public static function getKeyValuePair(Database_Result $countries){
        $output = [];
        foreach ($countries as $country){
            $output[$country->id] = $country->name;
        }
        return $output;
    }

    /**
     * Возвращает идентификатор страны для клиента
     * @return null
     * @throws Kohana_Exception
     */
    public static function getClientCountryId(Database_Result $countries){
        //$client_ip = Request::current()->client()->get_ip_env();//'37.252.93.223';
        //$client_ip = Request::current()->client()->get_ip_env() != '127.0.0.1' ?: '37.252.93.223';
        $client_ip = Request::current()->client()->get_ip_env();
        if($client_ip == 'UNKNOWN' OR !count($countries))
            return null;

        $reader   = new \GeoIp2\Database\Reader(Kohana::$config->load('local-storage')->geoip2_country_db);
        try{
            $record = $reader->city($client_ip);
        }catch(Exception $e){
            return null;
        }

        $iso2 = $record->country->isoCode;
        foreach ($countries as $country){
            if($country->iso2 == $iso2)
                return $country->id;
        }

        return null;
    }
}