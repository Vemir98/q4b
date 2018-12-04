<?php defined('SYSPATH') OR die('No direct script access.');
use \Carbon\Carbon as Carbon;
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.05.2017
 * Time: 15:12
 */
class Listener_Report_QueryToken
{
    /**
     * Проверка на истечение срока токена для отчетов
     * @param $event
     * @param $sender
     * @param Model_ReportQueryToken $token
     */
    public static function registerExpiresCheckJob($event,$sender,Model_ReportQueryToken $token){
        Queue::enqueue('reportQueryTokenClearing','Job_Report_QueryTokenCleaner',null, Carbon::now()->addDays(7)->timestamp);
    }
}