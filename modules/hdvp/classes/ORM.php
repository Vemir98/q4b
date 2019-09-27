<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.06.2017
 * Time: 13:55
 */
class ORM extends Kohana_ORM
{
    public function list_columns()
    {
        try {
            $cache_lifetime = 360000;// 100 часов
            $cache_key = $this->_table_name . "_structure";
            Cache::instance('file')->delete($cache_key);
            $result = Cache::instance('file')->get($cache_key);

            if ($result) {
                $columns_data = $result;
            }

            if (!isset($columns_data)) {
                $columns_data = $this->_db->list_columns($this->_table_name);
                Cache::instance()->set($cache_key, $columns_data, $cache_lifetime);
            }
        }catch (Exception $e){
            $columns_data = $this->_db->list_columns($this->_table_name);
        }
        return $columns_data;
    }
}