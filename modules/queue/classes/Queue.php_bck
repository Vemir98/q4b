<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 1:26
 */
class Queue
{
    private static $_instance;
    const QUEUE_DIR = APPPATH.'local-storage/runtime-data/queue/';

    private function __construct(){}
    private function __clone(){}
    private function __sleep(){}

    public function instance(){
        if(self::$_instance == null){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Добавление задачи в очередь
     * @param $queue - наименование очереди (тип задачи)
     * @param $class - название класса-обработчика задачи
     * @param array|null $args - параметры задачи
     * @param int $priority - приоритет
     * @param int $startTime - время выполнения задачи (тип на самом деле DECIMAL(14.4) для того чтоб можно было проследить время выполнения задачи если потребуется)
     * @param int(3) $attemptsLimit - кол-во попыток при неудаче
     * @param int(3) $attemptsInterval - интервал выполнения следующей попытки после неудачной
     * @throws Kohana_Exception
     * @return int - идентификатор задачи
     */
    public static function enqueue($queue,$class,array $args = null, $startTime = 0, $priority = 0,$attemptsLimit = 3, $attemptsInterval = 5){
        $data = [
            'queue' => $queue,
            'class' => $class,
            'args'  => $args != null ? json_encode($args) : null,
            'priority' => (int)$priority,
            'time' => (double)$startTime,
            'attempts' => (int)$attemptsLimit,
            $attemptsInterval => (int)$attemptsInterval
        ];
        //todo: Оповестить работника о новой задаче
        return Model::factory('Queue')->add($data);
    }

    /**
     * Удаление задачи из очереди
     * Resque::dequeue('default', ['My_Job']);

    // Removes job class 'My_Job' with Job ID '087df5819a790ac666c9608e2234b21e' of queue 'default'
    Resque::dequeue('default', ['My_Job' => '087df5819a790ac666c9608e2234b21e']);

    // Removes job class 'My_Job' with arguments of queue 'default'
    Resque::dequeue('default', ['My_Job' => array('foo' => 1, 'bar' => 2)]);

    // Removes multiple jobs
    Resque::dequeue('default', ['My_Job', 'My_Job2']);
     * @param $queue
     * @param $params
     */
    public static function dequeue($queue,$params){}

    public static function getQueuesList(){
        $queues = Model::factory('Queue')->findAll();
        if(!count($queues)){
            $queues = [];
        }
        return $queues;
    }
}