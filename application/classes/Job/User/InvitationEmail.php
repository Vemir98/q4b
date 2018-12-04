<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 13:58
 */
class Job_User_InvitationEmail
{
    public function perform(){
        $mail = new Mail(Kohana::$config->load('mail'));
        $mail->to($this->args['email']);
        $mail->from('info@qforb.net');
        $mail->subject('Q4b user invite You to company team');
        $mail->body(View::factory($this->args['view'],[
            'currentUser' => $this->args['currentUser'],
            'url' => $this->args['url']
        ])->render());
        $mail->send();
    }
}