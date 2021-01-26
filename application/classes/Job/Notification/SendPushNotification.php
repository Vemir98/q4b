<?php defined('SYSPATH') OR die('No direct script access.');

//require_once (DOCROOT. '/application/classes/Helpers/PushHelper.php');
use Helpers\PushHelper;

class Job_Notification_SendPushNotification
{
    public function perform(){
//        I18n::lang($this->args['lang']);
//        Language::setCurrentLang($this->args['lang']);
        $ids = DB::select('id')
            ->from('users')
            ->where('device_token','IS NOT', NULL)
            ->and_where('os_type','IS NOT', NULL)
            ->execute('persistent')
            ->as_array();
        $idsArr = [];
        foreach($ids as $x => $value) {
            $idsArr[] = $value['id'];
        }
        Queue::enqueue('notification','Job_Notification_SendPushNotification',[
            'lang' => Language::getCurrent()->iso2,
        ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);

        $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();

        foreach ($users as $user){
            switch ($user->os_type) {
                case Model_User::Ios:
                    PushHelper::sendPush($user);
                    break;
                case Model_User::Android:
                    PushHelper::sendFcm($user->device_token, ['status' => 'successs']);
                    break;
            }
        }
    }
}