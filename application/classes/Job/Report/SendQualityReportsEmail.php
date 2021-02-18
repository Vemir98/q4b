<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.06.2019
 * Time: 8:50
 */

class Job_Report_SendQualityReportsEmail
{
    public function perform(){
        I18n::lang($this->args['lang']);
        Language::setCurrentLang($this->args['lang']);
        $mail = new Mail(Kohana::$config->load('mail'));
        foreach ($this->args['emails'] as $email){
            $mail->to($email);
        }
        $mail->from('info@qforb.net',$this->args['user']['name']);
        $mail->subject(html_entity_decode(__(':user | דו״ח | :project',[':user' => $this->args['user']['name'], ':project' => 'Quality report'])));
        $mail->reply($this->args['user']['email'],$this->args['user']['name']);
        $mail->body(View::factory($this->args['view'],[
            'link' => $this->args['link'],
            'message' => $this->args['message'],
            'user' => $this->args['user'],
            'img' =>'https://qforb.net/media/data/mailing/reports/quality//'.$this->args['report'].'.jpg'
        ])->render());
        $mail->send();

    }
}