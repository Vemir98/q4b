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
//        $mail->from('info@qforb.net',$this->args['user']['name']);
        $mail->from('info@qforb.net', 'Info Reports');
        $mail->subject(html_entity_decode(__(':user | דו״ח | :project',[':user' => $this->args['user']['name'], ':project' => $this->args['project']])));
        $mail->reply($this->args['user']['email'],$this->args['user']['name']);
        $mail->body(View::factory($this->args['view'],[
            'link' => $this->args['link'],
            'message' => $this->args['message'],
            'user' => $this->args['user'],
            'expires' => $this->args['expires'],
            'image' => $this->args['image']
        ])->render());
        $mail->send();
    }
}