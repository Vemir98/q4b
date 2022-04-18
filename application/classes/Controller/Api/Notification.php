<?php
ini_set('max_execution_time', 0);

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

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';

        } catch (Exception $exception) {
            Database::instance()->rollback();
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}