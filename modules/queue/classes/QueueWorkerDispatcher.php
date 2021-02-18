<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 1:30
 */
class QueueWorkerDispatcher
{
    /**
     * Список обработчиков заданий
     * @var array
     */
    private $_workers;

    /**
     * Максимальное время выполнения в секундах
     * @var int
     */
    private $_maxExecutionTime = 60;

    /**
     * Параметры диспечера
     * @var array
     */
    private $_params = [];

    /**
     * Файл с настройками диспечера
     */
    const DISPATCHER_FILENAME = Queue::QUEUE_DIR.'workerDispatcher.txt';

    public function __construct(){

        try{
            $this->_params = Arr::extract(json_decode(file_get_contents(self::DISPATCHER_FILENAME,true)),['workers']);
        }catch(Exception $e){
            $this->_params = ['workers' => null];
        }

        if(!empty($this->_params)){
            $this->_workers = $this->_params['workers'];
        }

    }

    public function callOutWorkers(){
        //Model::factory('Queue')->clearCompletedJobs();
        $result = Model::factory('Queue')->getAllQueues();
        if(!empty($result)){
            foreach ($result as $job){
                if(isset($this->_workers[$job['queue']]) AND !empty($this->_workers[$job['queue']]['pid'])){
                    $proc = new Process();
                    $proc->setPid($this->_workers[$job['queue']]['pid']);
                    if($proc->status()){
                        continue;
                    }
                }
                //$proc = new Process('C:\OpenServer\domains\constructmngr\index.php Queue_Worker --queue='.$job['queue']);
                //$proc = new Process('/home/qforbnet/public_html/index.php Queue_Worker --queue='.$job['queue']);
                $proc = new Process('/var/www/vhosts/sunrisedvp.systems/qforb.sunrisedvp.systems/index.php Queue_Worker --queue='.$job['queue']);

                $pid = $proc->getPid();

                if(!isset($this->_workers[$job['queue']])){
                    $this->_workers[$job['queue']] = ['pid' => $pid];
                }else{
                    $this->_workers[$job['queue']]['pid'] = $pid;
                }
            }
            $this->_saveParams();
            return true;
        }
        $this->_saveParams();
        return false;
    }

    public function stopDispatcher(){
        $canStop = true;
        if(!empty($this->_workers)){
            foreach ($this->_workers as $queue => $worker){
                $worker['command'] = 'stop';
                $proc = new Process();
                $proc->setPid((int)$worker['pid']);
                if($proc->status()){
                    $canStop = false;
                }else{
                    unset($this->_workers[$queue]);
                }
            }
        }
        $this->_saveParams();
        return $canStop;

    }

    public function clearParams(){
        $this->_saveParams();
    }

    private function _saveParams()
    {
        $this->_params['workers'] = $this->_workers;
        file_put_contents(self::DISPATCHER_FILENAME,json_encode($this->_params));
    }


}