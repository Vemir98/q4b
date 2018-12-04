<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 18.11.2016
 * Time: 6:27
 */
class Job_Auth_ResetPasswordEmail
{
    public function perform(){
        $mail = new Mail(Kohana::$config->load('mail'));
        $mail->to($this->args['email']);
        $mail->from('info@qforb.net');
        $mail->subject('Reset Password Request');
        $mail->body(View::factory($this->args['view'],[
            'url' => $this->args['url']
        ])->render());
        $mail->send();
    }
}