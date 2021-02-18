<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Settings extends HDVP_Controller_API
{
    public function action_spaces_types(){
        foreach (ORM::factory('PrSpaceType')->find_all() as $st){
            $this->_responseData['items'][] = $st->as_array();
        }

    }
}