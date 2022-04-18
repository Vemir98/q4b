<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:31
 */
class Job_Report_Test
{
    public function perform(){

        try {

            $startTime = $this->args['start'];

            $duration = 30;

            if(abs(time() - $startTime) > $duration) {
                $f = fopen(DOCROOT . 'testTimestamp', 'a');
                if ($f) {
                    fputs($f, ' [NOTICE] ['.date("Y-m-d h:i:sa").'] [STOP] ' . "\n");
                }
                fclose($f);
            } else {
                $f = fopen(DOCROOT . 'testTimestamp', 'a');
                if ($f) {
                    fputs($f, ' [NOTICE] ['.date("Y-m-d h:i:sa").']' . "\n");
                }
                fclose($f);
                Queue::enqueue('Test','Job_Report_Test',[
                    'start' => $startTime,
                ],\Carbon\Carbon::now()->addSeconds(3)->timestamp);
            }


            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            $f = fopen(DOCROOT . 'testTimestamp', 'a');
            if ($f) {
                fputs($f, ' [ ERROR ] ['.date("Y-m-d h:i:sa").'] ['.$exception->getMessage().'] ' . "\n");
            }
            fclose($f);
        }
    }
}