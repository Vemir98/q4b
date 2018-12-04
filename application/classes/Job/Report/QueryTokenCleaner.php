<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.05.2017
 * Time: 15:17
 */
class Job_Report_QueryTokenCleaner
{
    public function perform(){
        $tokens = ORM::factory('ReportQueryToken')->where('expires','<',time())->find_all();
        if(count($tokens)){
            foreach ($tokens as $t){
                $t->delete();
            }
        }
    }
}