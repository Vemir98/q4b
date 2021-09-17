<?php defined('SYSPATH') OR die('No direct script access.');

//require_once (DOCROOT. '/application/classes/Helpers/PushHelper.php');
use Helpers\PushHelper;

class Job_Notification_SendPushNotification
{
    public function perform(){
        $timestamp = (int)strtotime($this->args['time']) + 3600;

        $time = date('H:i',$timestamp);
        try {
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
//            if (!count($idsArr)) {
//                return;
//            }
//            $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();
//            foreach ($users as $user){
//                PushHelper::send($user);
//            }
            $f = fopen(DOCROOT.'testNotification.txt', 'a');

            if($f) {
                fputs($f, date('H:i:s')."\n");
            }
            fclose($f);
            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');

            \Kohana::$log->add(\Log::WARNING, json_encode([
                'name' => 'PushNotification_Job_Warning',
                'args_time' => $this->args['time'],
                'time' => $dt->timestamp
            ]));

        } catch (Exception $exception) {
            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');

            \Kohana::$log->add(\Log::ERROR, json_encode([
                'name' => 'PushNotification_Job_Error',
                'args_time' => $this->args['time'],
                'time' => $dt->timestamp,
                'exception_msg' => $exception->getMessage()
            ]));
        }
    }
}