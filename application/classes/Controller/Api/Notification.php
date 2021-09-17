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

//            $timestamp = (int)$this->request->query('time');

            $timestamp = (int)strtotime($this->request->query('time')) + 3600;
            $time = date('H:i',$timestamp);


//            $timestamp = (int)strtotime($this->request->query('time'));
//            $time = date('H:i',$timestamp + 120);




//            $time = new DateTime($time);

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$timestamp, $time]); echo "</pre>"; exit;
//            $time->add(new DateInterval('PT' . 2 . 'M'));

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r(date('H:i', (int)strtotime($time) +120)); echo "</pre>"; exit;
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$time]); echo "</pre>"; exit;
//            $dt = \Carbon\Carbon::parse($time);
//            $dt->addminute();

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($dt); echo "</pre>"; exit;
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
//            if (count($idsArr)) {
//                $users = ORM::factory('User')->where('id','IN', $idsArr)->find_all()->as_array();
//                foreach ($users as $user){
//                    PushHelper::send($user);
//                }
//

            $f = fopen(DOCROOT.'testNotification.txt', 'a');

            if($f) {
//                echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r(DOCROOT.'testNotification.txt'); echo "</pre>"; exit;

                fputs($f, date('H:i:s')."\n");
            }
            fclose($f);

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($dt); echo "</pre>"; exit;
            PushHelper::queueIfNotExists($time, \Language::getCurrent()->iso2, 'Job_Notification_SendPushNotification', $timestamp, 'waiting');
//            }

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}