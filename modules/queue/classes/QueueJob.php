<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 1:28
 */
class QueueJob
{
    const STATUS_WAITING = 'waiting', STATUS_RUNNING = 'running', STATUS_FAILED = 'failed', STATUS_COMPLETED = 'completed';
    /**
     * Очередь к которой принадлежит задача
     * @var string
     */
    protected $_queue;

    /**
     * Ссылка на экземпляр класса обработчика текущей задачи
     * @var QueueWorker
     */
    protected $_worker;

    protected $_jobInstance;

    protected $_errorMsg;

    /**
     * Данные о текущей задаче
     * @var array
     */
    protected $_jobData;

    public function __construct($queue,$jobData)
    {
        $this->_queue = $queue;
        $this->_jobData = $jobData;
        if(!empty($this->_jobData['args']) AND !is_array($this->_jobData['args'])){
            $this->_jobData['args'] = json_decode($this->_jobData['args'],true);
        }
    }

    public function jobInstance(){
        if($this->_jobInstance == null){
            $class = $this->_jobData['class'];
            $this->_jobInstance = new $class();
            $this->_jobInstance->args = $this->_jobData['args'];
            $this->_jobInstance->queue = $this->_queue;
        }
        return $this->_jobInstance;

    }

    /**
     * Возвращает аргументы для текущего задания
     * @return array|mixed
     */
    public function getArguments(){
        if(empty($this->_jobData['args'])){
            return [];
        }else{
            return $this->_jobData['args'];
        }
    }

    public function setWorker(QueueWorker $worker){
        $this->_worker = $worker;
    }

    /**
     * Установки параметров до запуска задачи
     * @return null
     */
    public function setUp(){}

    /**
     * Выполнение работы
     */
    public function perform(){
        try{
            $instance = $this->jobInstance();
            if(method_exists($instance,'setUp')){
                $instance->setUp();
            }

            $instance->perform();

            if(method_exists($instance,'tearDown')){
                $instance->tearDown();
            }

        }catch(Exception $e){
            $this->_errorMsg = $e->getMessage();
            return false;
        }
        return true;
    }
    
    /**
     * Очистка установленных настроек после выполнения задачи
     * @return mixed
     */
    public function tearDown(){}
    
    
    public function getQueue(){
        return $this->_queue;
    }

    public function hash(){
        return MD5($this->_queue.$this->_jobData['class'].json_encode($this->_jobData['args']));
    }
    
    public function id(){
        return (int)$this->_jobData['id'];
    }
    
    public function updateStatus($status){
        switch ($status){
            case self::STATUS_RUNNING : {
                $this->setStatusRunning();
            }
                break;
            case self::STATUS_COMPLETED : {
                $this->setStatusCompleted();
            }
                break;
            case self::STATUS_FAILED : {
                $this->setStatusFailed();
            }
                break;
        }
    }
    
    private function setStatusRunning(){
        return Model::factory('Queue')->update($this->id(),['status' => QueueJob::STATUS_RUNNING, 'time' => microtime(true),'attempts' => ++$this->_jobData['attempts'], 'fall_reason' => $this->_errorMsg]);
    }

    private function setStatusCompleted(){
        return Model::factory('Queue')->update($this->id(),['status' => QueueJob::STATUS_COMPLETED, 'time' => microtime(true)]);
    }

    private function setStatusFailed(){
        return Model::factory('Queue')->update($this->id(),['status' => QueueJob::STATUS_FAILED, 'time' => microtime(true),'falls' => ++$this->_jobData['falls'], 'fall_reason' => $this->_errorMsg]);
    }
}