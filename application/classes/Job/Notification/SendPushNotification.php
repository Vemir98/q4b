<?php defined('SYSPATH') OR die('No direct script access.');

//require_once (DOCROOT. '/application/classes/Helpers/PushHelper.php');
use Helpers\PushHelper;

class Job_Notification_SendPushNotification
{
    public function perform(){

        try {

            $usersDeviceTokens = $this->args['usersDeviceTokens'] ?: null;

            Kohana::$log->add(Log::ERROR, 'JOB PERFORM() try: ' . json_encode([$usersDeviceTokens], JSON_PRETTY_PRINT));


            $fpns = new HDVP\FirebasePushNotification();

//            foreach ($usersDeviceTokens as $token) {
//                if($token) {
            $fpns->notify($usersDeviceTokens, ['action' => $this->args['action']]);

            $f = fopen(DOCROOT.'testNotification.txt', 'a');
            if($f) {
                fputs($f, 'from job perform() action['.$this->args['action'].'] - '.json_encode([$usersDeviceTokens], JSON_PRETTY_PRINT)."\n");
            }
            fclose($f);
//                }
//            }
//            $f = fopen(DOCROOT.'testNotification.txt', 'a');
//            if($f) {
//                fputs($f, 'from job perform() action['.$this->args['action'].'] - '.json_encode([$usersDeviceTokens], JSON_PRETTY_PRINT)."\n");
//            }
//            fclose($f);


        } catch (Exception $exception) {

            Kohana::$log->add(Log::ERROR, 'JON PERFORM() catch: ' . json_encode([$usersDeviceTokens], JSON_PRETTY_PRINT));

        }








//        $timestamp = (int)strtotime($this->args['time']) + 3600;
////        $timestamp = (int)strtotime($this->args['time']  + 3600);
//        $time = date('H:i', $timestamp);
//        try {
////            $time = new DateTime($time);
////            $time->add(new DateInterval('PT' . 2 . 'M'));
////            $timestamp = ;
//
////            $time = date('H:i',$timestamp + 120);
////            $time = $this->args['time'];
////            $dt = \Carbon\Carbon::parse($time);
////            $dt->addMinutes(2);
//            $ids = DB::select('id')
//                ->from('users')
//                ->where('device_token','IS NOT', NULL)
//                ->and_where('device_token','!=', "")
//                ->and_where('os_type','IS NOT', NULL)
//                ->execute('persistent')
//                ->as_array();
//
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
//
//            $f = fopen(DOCROOT.'testNotification.txt', 'a');
//
//            if($f) {
//                fputs($f, 'from cron - '.date('H:i:s')."\n");
//            }
//            fclose($f);
//
//            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');
//
//            \Kohana::$log->add(\Log::WARNING, json_encode([
//                'name' => 'PushNotification_Job_Warning',
//                'args_time' => $time,
//                'time' => $time
//            ]));
//
//        } catch (Exception $exception) {
//            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');
//
//            \Kohana::$log->add(\Log::ERROR, json_encode([
//                'name' => 'PushNotification_Job_Error',
//                'args_time' => $time,
//                'time' => $time,
//                'exception_msg' => $exception->getMessage()
//            ]));
//        }
    }
}