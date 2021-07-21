<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 17:22
 */
class Process_LinuxProcessManager implements Process_Interface
{
    private $_pid;
    private $_command;
    
    public function __construct($cl = false)
    {
        if ($cl !== false){
            $this->_command = $cl;
            $this->_runCom();
        }
    }

    private function _runCom(){
        $command = 'nohup /opt/cpanel/ea-php74/root/usr/bin/php -f '.$this->_command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $this->_pid = (int)$op[0];
        return (bool) $this->_pid;
    }

    public function setPid($pid)
    {
        $this->_pid = $pid;
        return $this;
    }

    public function getPid()
    {
        return $this->_pid;
    }

    public function status()
    {
        exec('ps -p '.$this->_pid,$op);
        return isset($op[1]);
    }

    public function start()
    {
        if(!empty($this->_command))
            return $this->_runCom();
        return false;
    }

    public function stop()
    {
        exec('kill '.$this->_pid);
        return !$this->status();
    }

    public function setCommand($command){
        $this->_command = $command;
        return $this;
    }

    public function getCommand(){
        return $this->_command;
    }
}