<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 17:19
 */
interface Process_Interface
{
    public function __construct($cl = false);

    public function setPid($pid);

    public function getPid();

    public function status();

    public function start();

    public function stop();
    
    public function setCommand($command);
    
    public function getCommand();
    
}