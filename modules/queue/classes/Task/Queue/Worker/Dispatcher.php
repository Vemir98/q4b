<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 01.11.2016
 * Time: 18:34
 */
class Task_Queue_Worker_Dispatcher extends Minion_Task
{

    protected function _execute(array $params)
    {//exit;
        set_time_limit(0);
        $time = time() + (60 * 5);
        $worckerDispatcher = new QueueWorkerDispatcher();

        callOut:
        while ($worckerDispatcher->callOutWorkers()){
            sleep(3);
            $time -= 3;
        }

        $worckerDispatcher->clearParams();
        if(time() < $time) goto callOut;
        Model::factory('Queue')->clearCompletedJobs();
    }
}