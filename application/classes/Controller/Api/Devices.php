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

            $usersWithSameDeviceToken = Api_DBUsers::getUsersByDeviceToken($data['device_token']);

            $queryData = [
                'device_token' => null,
                'os_type' => null
            ];

            foreach ($usersWithSameDeviceToken as $userId) {
                DB::update('users')
                    ->set($queryData)
                    ->where('id', '=', $userId)
                    ->execute($this->_db);
            }

            $currentUserId = Auth::instance()->get_user()->id;

            $queryData = [
              'device_token' => $data['device_token'],
              'os_type' => $data['os_type']
            ];

            DB::update('users')
                ->set($queryData)
                ->where('id', '=', $currentUserId)
                ->execute($this->_db);

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        }catch (Exception $e){
            throw API_Exception::factory(500,$e->errors('validation'));
        }

    }
}