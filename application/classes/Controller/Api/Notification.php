<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */
use Helpers\PushHelper;

class Controller_Api_Notification extends HDVP_Controller_API {
    protected $_checkToken = false;

    public function action_send()
    {
        try {
            $name = Arr::get($_GET, 'name');
            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
            $this->_responseData['name'] = md5($name).base_convert(microtime(false), 10, 36);
        } catch (Exception $exception) {
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}