<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */

class Controller_Api_Notification extends HDVP_Controller_API {
    protected $_checkToken = false;
    public function action_send()
    {
        Queue::enqueue('notification','Job_Notification_SendPushNotification',[
            'lang' => Language::getCurrent()->iso2,
        ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
    }
}