<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.10.2016
 * Time: 12:05
 */
class TestJob1
{
    public function perform(){
        sleep(2);
        throw new Exception('dsf');
        file_put_contents('C:\OpenServer\domains\constructmngr\application\local-storage/runtime-data/queue/testjob.txt','1');
    }
}