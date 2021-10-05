<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/12/21
 * Time: 12:40 PM
 */
namespace Helpers;

class PushHelper {
        public static function sendFcm($token,$data=[]){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $YOUR_API_KEY = 'AAAAW8fjUqU:APA91bEqYweul8effOA4z90gAA124ZSYXqpJl6sD9B6LU5jeCyWgyuqTpirB8UUsuTwsjk_Q4lfCxwnMXyVvxt8WzMOVwcWXjp2sFimGiYkLFtJZOqcDkRf8VvreRWWEXZz91BTbGuGA'; // Server key
        $YOUR_TOKEN_ID = $token; // Client token id

        $notification = new \StdClass();
        $notification->title = 'test title';
        $notification->body = 'test message';
        $request_body = [
            'to' => $YOUR_TOKEN_ID,
            'data'=>$data,
//            'notification' => $notification,// ['title'=>$data['title'],"body"=>$data['message']],
        ];

        $fields = json_encode($request_body);
        $request_headers = [
            'Content-Type: application/json',
            'Authorization: key=' . $YOUR_API_KEY,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        \Kohana::$log->add(\Log::WARNING, json_encode([
            'name' => 'PushNotification_response',
            'token' => $token,
            'response' => $response,
        ]));

        if($httpcode == 200){
            return true;
        }
    }

    public static function sendPush($token,$data){

//        $pem_file       = $this->pemFile;
//        $pem_secret     = $this->pemSecret;
//        $apns_topic     = $this->topic ? $this->topic : 'com.OneSmartStar.Phone-IVR';
//
//
//        $dataJson = json_encode($data);
//
//
//        // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($dataJson); echo "</pre>"; exit;
//
//        $sample_alert =  $dataJson;
//        $url = $this->environment == self::ENVIRONMENT_SANDBOX ? "https://api.sandbox.push.apple.com/3/device/$token" : "https://api.push.apple.com/3/device/$token";
//
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $sample_alert);
//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array("apns-topic: $apns_topic"));
//        curl_setopt($ch, CURLOPT_SSLCERT, $pem_file);
//        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $pem_secret);
//        $response = curl_exec($ch);
//        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
//        if($httpcode == 200){
//            return true;
//        }
//        //On successful response you should get true in the response and a status code of 200
//        //A list of responses and status codes is available at
//        //https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/TheNotificationPayload.html#//apple_ref/doc/uid/TP40008194-CH107-SW1
//
//        var_dump($response);
//        var_dump($httpcode);
    }

    public function send($user)
    {
        switch ($user->os_type) {
            case \Model_User::Ios:
//                    self::sendPush($user->device_token, ['status' => 'successs']);
                break;
            case \Model_User::Android:
                self::sendFcm($user->device_token, ['status' => 'successs']);
                break;
        }
    }

    public static function checkIfQueueExists($className, $timestamp, $status)
    {
        return \DB::select()->from('queues')
            ->where('class', '=', $className)
            ->where('status', '=', $status)
            ->where('time','=', $timestamp)
            ->limit(1)
            ->execute()
            ->as_array();
    }

    public static function queueIfNotExists($time, $lang, $className, $timestamp, $status)
    {
        if (!self::checkIfQueueExists($className, $timestamp, $status))  {
            \Queue::enqueue('notification','Job_Notification_SendPushNotification',[
                'lang' => $lang,
                'time' => $time
            ],$timestamp, 0, 1);
        }
    }

    public static function test($data, $timestamp) {
        \Queue::enqueue('notification','Job_Notification_SendPushNotification', $data ,$timestamp, 0, 1);
    }
}