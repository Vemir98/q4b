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

            $fpns = new HDVP\FirebasePushNotification();

            $token = 'cNh86BsYT-ujq49Kt5AEEZ:APA91bFdKan-3VMqdFLCoiZsmK1NbjEOJ9VL7hWhC4luGavuvfFK1bU5ZcOLlxvP-UO2jByDZC-39qTGBxJerAJ9Q3ggR-XmgmKZ86zQC4R5iAMuFTk6gMnxnMWz4qkXmmoWjWuxte8N';

            $fpns->notify([$token], ['action' => 'elApproval']);

            $f = fopen(DOCROOT.'testNotification.txt', 'a');
            if($f) {
                fputs($f, 'from request action_send()');
            }
            fclose($f);

//------------------------------------------------
//            $timestamp = time() + 60;
//
//            $users = Api_DBElApprovals::getElApprovalUsersListForNotify(81);
////
//            $usersDeviceTokens = [];
//
//            foreach ($users as $user) {
//                if($user['deviceToken']) {
//                    array_push($usersDeviceTokens, $user['deviceToken']);
//                }
//            }
//
//            PushHelper::test([
//                'lang' => \Language::getCurrent()->iso2,
//                'action' => 'elApproval',
//                'usersDeviceTokens' => $usersDeviceTokens
//            ], $timestamp );
//
//            $f = fopen(DOCROOT.'testNotification.txt', 'a');
//
//            if($f) {
//                fputs($f, 'from action_send() - '.date('H:i:s')."\n");
//            }
//            fclose($f);
//
//            $this->_responseData = [
//                'status' => 'success'
//            ];

 // ----------------------------


//            $time = $this->request->query('time');
//            $dt = \Carbon\Carbon::parse($time);
//            $dt->addMinutes(2);
//            $timestamp = (int)strtotime($this->request->query('time')) + 3600;
//            $time = date('H:i',$timestamp);
//
//            $ids = DB::select('id')
//                ->from('users')
//                ->where('device_token','IS NOT', NULL)
//                ->and_where('device_token','!=', "")
//                ->and_where('os_type','IS NOT', NULL)
//                ->execute('persistent')
//                ->as_array();
//            $idsArr = [];
//            foreach($ids as $x => $value) {
//                $idsArr[] = $value['id'];
//            }
//
//            if (count($idsArr)) {
//                $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();
//                foreach ($users as $user){
//                    PushHelper::send($user);
//                }
//
//                $f = fopen(DOCROOT.'testNotification.txt', 'a');
//
//                if($f) {
//                    fputs($f, 'from request - '.date('H:i:s')."\n");
//                }
//                fclose($f);
//
//            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');
//            }
//
//            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}