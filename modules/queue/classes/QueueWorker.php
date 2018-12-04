<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 1:28
 */
class QueueWorker
{
    const WORKER_DIR = Queue::QUEUE_DIR.'workers/';

    private $_queue;

    /**
     * @var QueueJob
     */
    private $_currentJob;
    
    public function __construct($queue)
    {
        $this->_queue = $queue;

    }
    
    public function makeJob(QueueJob $job){
        if( ! $this->canMakeJob()){
            return false;
        }
        $this->_currentJob = $job;
        $this->_currentJob->setWorker($this);
        try{
            $this->_work();
        }catch (Error $e){
            file_put_contents($this->_getMyFileName(),json_encode(['time' => time(), 'id' => $this->_currentJob->id(), 'status' => QueueJob::STATUS_FAILED]));
            $this->_currentJob->updateStatus(QueueJob::STATUS_FAILED);
        }

    }
    
    public function canMakeJob(){

        if(file_exists($this->_getMyFileName())) {
            try {
                $data = json_decode(file_get_contents($this->_getMyFileName()), true);
                if (!empty($data)) {
                    if ($data['status'] == QueueJob::STATUS_RUNNING) {
                        if(time() > $data['time'] + 360){
                            $this->forceFailedStatus($data['id']);
                            return true;
                        }
                        return false;
                    }
                }
            } catch (Exception $e) {}
        }
        return true;
    }

    private function _work()
    {
        file_put_contents($this->_getMyFileName(),json_encode(['time' => time(), 'id' => $this->_currentJob->id(), 'status' => QueueJob::STATUS_RUNNING]));
        $this->_currentJob->updateStatus(QueueJob::STATUS_RUNNING);
        if($this->_currentJob->perform()){
            file_put_contents($this->_getMyFileName(),json_encode(['time' => time(), 'id' => $this->_currentJob->id(), 'status' => QueueJob::STATUS_COMPLETED]));
            $this->_currentJob->updateStatus(QueueJob::STATUS_COMPLETED);
            return true;
        }else{
            file_put_contents($this->_getMyFileName(),json_encode(['time' => time(), 'id' => $this->_currentJob->id(), 'status' => QueueJob::STATUS_FAILED]));
            $this->_currentJob->updateStatus(QueueJob::STATUS_FAILED);
            return false;
        }
    }

    private function _getMyFileName(){
        if( ! is_dir(self::WORKER_DIR)){
            mkdir(self::WORKER_DIR,0777,true);
        }
        return self::WORKER_DIR.$this->_queue.'.txt';
    }

    private function forceFailedStatus($id){
        try{
            Model::factory('Queue')->update($id,['status' => QueueJob::STATUS_FAILED, 'time' => microtime(true),'attempts' => DB::expr('attempts + 1'), 'falls' => DB::expr('falls + 1')]);
            @unlink($this->_getMyFileName());
        }catch (Exception $e){}

    }
}