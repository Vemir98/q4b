<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 14:53
 * php -f C:\OpenServer\domains\constructmngr\index.php Test_Cron --foo=bar
 */
class Task_Test_Cron extends Minion_Task
{
//    protected $_options = array(
//        'foo' => null
//    );
    protected function _execute(array $params)
    {
        Auth::instance();
        set_time_limit(0);
        try{
            $a = (int)file_get_contents(DOCROOT.'testcron.txt');
        }catch (Exception $e){
            $a = 0;
        }
        file_put_contents(DOCROOT.'testcron.txt',++$a.' '.getmygid());

//        if(!empty($params)) {
//            //echo shell_exec( "tasklist.exe" );
//            while (true){
//                file_put_contents(DOCROOT.'testcron.txt',++$a.'sec');
//                usleep(1000000);
//            }
//        }
    }
}