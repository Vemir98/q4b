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
            $time = $this->request->query('time');
            $dt = \Carbon\Carbon::parse($time);
            $dt->addHour();
            $ids = DB::select('id')
                ->from('users')
                ->where('device_token','IS NOT', NULL)
                ->and_where('device_token','!=', "")
                ->and_where('os_type','IS NOT', NULL)
                ->execute('persistent')
                ->as_array();
            $idsArr = [];
            foreach($ids as $x => $value) {
                $idsArr[] = $value['id'];
            }
            if (count($idsArr)) {
                $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();
                foreach ($users as $user){
                    PushHelper::send($user);
                }

                PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $dt->timestamp, 'waiting');
            }

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}