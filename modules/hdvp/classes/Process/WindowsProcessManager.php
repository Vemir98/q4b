<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 17:25
 */
class Process_WindowsProcessManager implements Process_Interface
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
        $output = shell_exec('tasklist');
        preg_match_all('~php\.exe\s+(?<pid>\d+)\s{1}\w{3,}\s+~',$output,$fstProcList);
        popen('start /B php -f '.$this->_command,'r');
        $output = shell_exec('tasklist');
        preg_match_all('~php\.exe\s+(?<pid>\d+)\s{1}\w{3,}\s+\d{1,2}\s+\d+~',$output,$scndProcList);
        $op = array_values(array_diff($scndProcList['pid'],$fstProcList['pid']));
        $this->_pid = (int)$op[count($op)-1];
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
        $out = exec('tasklist /fi "PID eq '.$this->_pid.'"');
        preg_match_all('~php\.exe\s+(?<pid>\d+)\s{1}\w{3,}\s+~',$out,$op);
        if (!empty($op['pid'])) return true;
        else return false;
    }

    public function start()
    {
        if(!empty($this->_command))
            return $this->_runCom();
        return false;
    }

    public function stop()
    {
        exec('Taskkill /PID '.$this->_pid.' /F');
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