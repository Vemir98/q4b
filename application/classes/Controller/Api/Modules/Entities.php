<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Modules_Entities extends HDVP_Controller_API
{

    public function action_modules_get(){
        try {
            $modules = Api_DBModules::getModules();
            $this->_responseData = [
                'status' => 'success',
                'items' => $modules
            ];
        } catch (Exception $e) {
            throw API_Exception::factory(500,'Operation Error');
        }

    }
}