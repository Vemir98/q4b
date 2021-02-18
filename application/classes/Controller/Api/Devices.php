<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */

class Controller_Api_Devices extends HDVP_Controller_API {
    protected $_checkToken = true;

    public function action_register() {
        try{

            $data = json_decode($this->request->body(), true);
            if (!$data['device_token'] || !$data['os_type']) {
                throw API_Exception::factory(500,'validation');
            }
            $user = Auth::instance()->get_user();
            $user->values(['device_token' => $data['device_token'], 'os_type' => $data['os_type']])->save();
            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        }catch (Exception $e){
            throw API_Exception::factory(500,$e->errors('validation'));
        }

    }
}