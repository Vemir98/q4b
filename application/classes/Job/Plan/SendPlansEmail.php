<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 16.06.2017
 * Time: 13:38
 */
class Job_Plan_SendPlansEmail
{
    public function perform(){
        I18n::lang($this->args['lang']);
        Language::setCurrentLang($this->args['lang']);
        $mail = new Mail(Kohana::$config->load('mail'));
        foreach ($this->args['emails'] as $email){
            $mail->to($email);
        }

//        $mail->from('info@qforb.net',$this->args['user']['name']);
        $mail->from('info@qforb.net', 'Info Plans');
        $mail->subject('Project #'.$this->args['item'].' Plans');
        $mail->reply($this->args['user']['email'],$this->args['user']['name']);
        View::set_global('_SITE_URL','https://qforb.net');
        $mail->body(View::factory($this->args['view'],[
            'item' => ORM::factory('Project',$this->args['item']),
            'plans' => ORM::factory('PrPlan')->where('id','IN',DB::expr('('.implode(',',$this->args['plans']).')'))->find_all(),
            'message' => $this->args['message'],
            'user' => $this->args['user']
        ])->render());
        $mail->send();

    }
}