<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.10.2016
 * Time: 16:55
 */
abstract class Request_Client extends Kohana_Request_Client
{
    /**
     * Возвращает ip пользователя из окружения
     * @return string
     */
    public function get_ip_env() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = false;
        if($ipaddress){
            if (!filter_var($ipaddress, FILTER_VALIDATE_IP)) {
                throw new HTTP_Exception_404();
            }
        }
        return $ipaddress;
    }

    /**
     * Возвращает ip пользователя от сервера apache
     * @return string
     */
    public function get_ip_server() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) AND $_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']) AND $_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']) AND $_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']) AND $_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = false;

        if($ipaddress){
            if (!filter_var($ipaddress, FILTER_VALIDATE_IP)) {
                throw new HTTP_Exception_404();
            }
        }
        return $ipaddress;
    }

    /**
     * Возвращает список предпочитаемых клиенту языков
     * отсортированных по приоритету
     * https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @return array
     */
    public function getAcceptLanguages(){
        $langs = array();

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);

                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '') $langs[$lang] = 1;
                }

                // sort list based on value	
                arsort($langs, SORT_NUMERIC);
            }
        }
        
        return array_change_key_case($langs,CASE_LOWER);

    }
}