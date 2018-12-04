<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 14:53
 * php -f C:\OpenServer\domains\constructmngr\index.php Test_Cron --foo=bar
 */
class Task_Queue_Worker extends Minion_Task
{
    protected $_options = array(
        'queue' => null
    );

    protected $_jobData;

    protected function _execute(array $params)
    {
        if(empty($params['queue'])){
            die('Incorrect queue'.PHP_EOL);
        }
        $worker = new QueueWorker($params['queue']);
        if( ! $worker->canMakeJob()){
            die('Worker is busy'.PHP_EOL);
        }

        while ($worker->canMakeJob() AND $this->getNextJob($params['queue'])){
            $worker->makeJob(new QueueJob($this->_jobData['queue'],$this->_jobData));
            if($this->detectStopCommand($params['queue'])){
                break;
            }
            sleep(2);
        }
    }

    protected function getNextJob($queue){
        $this->_jobData = Model::factory('Queue')->find($queue);
        return !empty($this->_jobData);
    }

    protected function detectStopCommand($queue){
        try{
            $data = json_decode(file_get_contents(QueueWorkerDispatcher::DISPATCHER_FILENAME),true);
            if(!empty($data['workers']) AND !empty($data['workers'][$queue]) AND !empty($data['workers'][$queue]['command'])){
                if($data['workers'][$queue]['command'] = 'stop'){
                    return true;
                }
            }
        }catch (Exception $e){}
        return false;
    }
}