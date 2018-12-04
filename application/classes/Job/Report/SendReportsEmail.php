<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:31
 */
class Job_Report_SendReportsEmail
{
    public function perform(){
        I18n::lang($this->args['lang']);
        Language::setCurrentLang($this->args['lang']);
        $mail = new Mail(Kohana::$config->load('mail'));
        foreach ($this->args['emails'] as $email){
            $mail->to($email);
        }
        $mail->from('info@qforb.net');
        $mail->subject($this->args['subject']);
        $mail->reply($this->args['user']['email'],$this->args['user']['name']);
        $mail->body(View::factory($this->args['view'],[
            'link' => $this->args['link'],
            'message' => $this->args['message'],
            'user' => $this->args['user']
        ])->render());
        $mail->send();
    }
}