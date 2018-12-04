<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 17:14
 * Класс предназначенный для работы с процессами
 * основные возможности запуск и остановка процессов, проверка статуса процесса
 */
class Process implements Process_Interface
{
    /**
     * @var Process_Interface
     */
    protected $_manager;

    /**
     * Process constructor.
     * @param string|Process_Interface|bool $cl
     */
    public function __construct($cl = false)
    {

        if($cl instanceof Process_Interface){
            $this->_manager = $cl;
        }
        else
        {
            //примитивная проверка ос
            //todo:: в продакшене вообще убрать и работать только с линукс
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $this->_manager = new Process_WindowsProcessManager($cl);
            } else {
                $this->_manager = new Process_LinuxProcessManager($cl);
            }
        }

    }

    public function setPid($pid)
    {
        $this->_manager->setPid($pid);
        return $this;
    }

    public function getPid()
    {
        return $this->_manager->getPid();
    }

    public function status()
    {
        return $this->_manager->status();
    }

    public function start()
    {
        return $this->_manager->start();
    }

    public function stop()
    {
        return $this->_manager->stop();
    }

    public function setCommand($command)
    {
        return $this->_manager->setCommand($command);
    }

    public function getCommand()
    {
        return $this->_manager->getCommand();
    }
}