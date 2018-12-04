<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.12.2017
 * Time: 14:19
 */
class Task_Mailing_ProjectNotifications extends Minion_Task
{

    protected function _execute(array $params)
    {
        $items = ORM::factory('QualityControl')->where('created_at','>',strtotime(date('d-m-Y'))-86400)->find_all();
        if(count($items)){
            foreach ($items as $i){
                $tmp[$i->project_id] = $i->project_id;
            }
            $up = DB::query(Database::SELECT,'SELECT * FROM users_projects up WHERE notify_changes = 1 AND project_id IN ('.implode(',',$tmp).')')->execute()->as_array();
            if( ! count($up)) return;
            $tmp = [];
            foreach ($up as $u){
                $tmp[$u['user_id']][] = $u['project_id'];
            }
            $users = ORM::factory('User')->where('id','IN',DB::expr('('.implode(',',array_keys($tmp)).')'))->find_all();
            if(count($users)){
                foreach ($users as $user){
                    $projects = ORM::factory('Project')->where('id','IN',DB::expr('('.implode(',',$tmp[$user->id]).')'))->find_all();
                    $prj = [];
                    foreach ($projects as $project){
                        $prj []= $project->as_array();
                    }
                    Queue::enqueue('mailing','Job_User_ProjectNotification',[
                        'email' => $user->email,
                        'subject' => 'Q4b New quality control forms',
                        'user' => ['name' => $user->name, 'email' => $user->email],
                        'projects' => $prj,
                        'view' => 'emails/user/project-notification',
                        'lang' => $user->lang,
                    ],\Carbon\Carbon::now()->addSeconds(1)->timestamp);

                }
            }
        }
    }
}