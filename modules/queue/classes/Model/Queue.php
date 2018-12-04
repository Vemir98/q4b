<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 3:11
 */
class Model_Queue extends Model
{
    /**
     * Добавляет задачу и возвращает её идентификатор
     * @param $params ['class','args','queue','priority','start','attempts_limit','attempts_interval']
     * @return int - идентификатор задачи
     * @throws Kohana_Exception
     */
    public function add($params){
        $data = array_diff(Arr::extract($params,['class','args','queue','priority','time','attempts_limit','attempts_interval']),array(''));
        return DB::insert('queues',array_keys($data))
            ->values($data)
            ->execute('persistent')[0];
    }

    public function findAll(){
        return DB::select()->from('queues')->order_by('time','ASC')->order_by('priority','DESC')->execute('persistent')->as_array();
    }

    public function find($queue){
        $res =  DB::select()->from('queues')
            ->where('queue','=',':queue')
            ->and_where_open()
            ->and_where('status','=',QueueJob::STATUS_WAITING)
            ->or_where_open()
            ->or_where('status','=',QueueJob::STATUS_FAILED)
            ->and_where('falls','<',DB::expr('attempts_limit'))
            ->and_where(DB::expr('time + (falls * attempts_interval)'),'<=',microtime(true))
            ->or_where_close()
            ->and_where_close()
            ->and_where('time','<=',microtime(true))
            ->and_where('attempts','<=',DB::expr('attempts_limit'))
            ->bind(':queue',$queue)
            ->order_by('time','ASC')->order_by('priority','DESC')->execute('persistent')->as_array();
        if(!empty($res)){
            return $res[0];
        }else{
            return null;
        }
    }

    public function update($id,array $values){
        $query = DB::update('queues');
        foreach ($values as $key => $val){
            $query->value($key,$val);
        }
        return $query->where('id','=',(int)$id)->execute('persistent');
    }

    public function getAllQueues(){
        return DB::select('queue')
            ->distinct(TRUE)
            ->from('queues')
            ->where('time','<=',microtime(true))
            ->and_where_open()
            ->and_where('status','=',QueueJob::STATUS_WAITING)
            ->or_where_open()
            ->or_where('status','=',QueueJob::STATUS_FAILED)
            ->and_where('falls','<',DB::expr('attempts_limit'))
            //->and_where(DB::expr('time + (falls * attempts_interval)'),'<=',microtime(true))
            ->or_where_close()
            ->and_where_close()
            ->and_where('attempts','<=',DB::expr('attempts_limit'))->execute('persistent')->as_array();
    }

    public function clearCompletedJobs(){
        //return DB::delete('queues')->where('status','=',QueueJob::STATUS_COMPLETED)->and_where('time','>',time()-(Date::DAY * 3))->execute();
        return DB::query(Database::DELETE,"DELETE FROM queues WHERE time > '".(time()-(Date::DAY * 3))."' AND status = '".QueueJob::STATUS_COMPLETED."'")->execute();
    }
}