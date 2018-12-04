<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 13:58
 */
class Job_User_ProjectNotification
{
    public function perform(){
        I18n::lang($this->args['lang']);
        Language::setCurrentLang($this->args['lang']);
        $mail = new Mail(Kohana::$config->load('mail'));
        $mail->to($this->args['email']);
        $mail->from('info@qforb.net');
        $mail->subject(__($this->args['subject']));
        $mail->body(View::factory($this->args['view'],[
            'user' => $this->args['user'],
            'projects' => $this->args['projects']
        ])->render());
        $mail->send();
    }
}