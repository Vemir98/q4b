<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 12:50
 */

class Model_Customer extends ORM
{
    protected $_table_name = 'customers';

    public function dir(){
        $dir = DOCROOT.'media/data/customers';
        if( ! is_dir($dir)){
            mkdir($dir,0777,true);
        }
        return $dir;
    }
}