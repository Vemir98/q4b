<?php defined('SYSPATH') OR die('No direct script access.');

//require_once (DOCROOT. '/application/classes/Helpers/PushHelper.php');
use Helpers\PushHelper;

class Job_Notification_SendPushNotification
{
    public function perform(){

        try {
            $time = $this->args['time'];
            $dt = \Carbon\Carbon::parse($time);
            $dt->addDay();
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
            if (!count($idsArr)) {
                return;
            }
            $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();
            foreach ($users as $user){
                PushHelper::send($user);
            }
            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $dt->timestamp, 'waiting');

            \Kohana::$log->add(\Log::WARNING, json_encode([
                'name' => 'PushNotification_Job_Warning',
                'args_time' => $this->args['time'],
                'time' => $dt->timestamp
            ]));

        } catch (Exception $exception) {
            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $dt->timestamp, 'waiting');

            \Kohana::$log->add(\Log::ERROR, json_encode([
                'name' => 'PushNotification_Job_Error',
                'args_time' => $this->args['time'],
                'time' => $dt->timestamp,
                'exception_msg' => $exception->getMessage()
            ]));
        }
    }
}