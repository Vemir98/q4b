<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:31
 */
class Job_Report_CheckDeliveryReportsQcIntegrity
{
    public function perform(){

        try {

            $deliveryReportId = $this->args['reportId'];
            $startTime = $this->args['startTime'];

            $duration = 60;
            $qcCountCheckDelay = 30;
            $createPdfDelay = 10;

            $deliveryReportQcCount = Api_DBDelivery::getDeliveryReportQcsCount($deliveryReportId)[0]['count'];
            $deliveryReportQcExpectedCount = Api_DBDelivery::getDeliveryReportQcsExpectedCount($deliveryReportId)[0]['count'];

            $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
            if ($f) {
                fputs($f, ' --- [NEW CRON]  ['.date("Y-m-d h:i:sa").'] [id='.$deliveryReportId.'] [currentCount='.$deliveryReportQcCount.'] [expectedCount='.$deliveryReportQcExpectedCount.'] ' . "\n");
            }
            fclose($f);

            if(!is_null($startTime)) {

            } else {

            }
            if(abs(time() - $startTime) > $duration) {
                $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
                if ($f) {
                    fputs($f, ' [NOTIFY] ['.date("Y-m-d h:i:sa").'] ['.$deliveryReportId.'] [STOP] ' . "\n");
                }
                fclose($f);
            } else {
                if(!is_null($deliveryReportQcExpectedCount) && !is_null($deliveryReportQcCount)) {

                    // updating current qcs count
                    DB::update('delivery_reports')
                        ->set(['qc_count' => $deliveryReportQcCount])
                        ->where('id', '=', $deliveryReportId)
                        ->execute($this->_db);


                    if( ((int)$deliveryReportQcExpectedCount) > ((int)$deliveryReportQcCount) ) {

                        $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
                        if ($f) {
                            fputs($f, '['.date("Y-m-d h:i:sa").'] [QC COUNT ERROR]' . "\n\n");
                        }
                        fclose($f);

                        Queue::enqueue('CheckDeliveryReportsQcIntegrity','Job_Report_CheckDeliveryReportsQcIntegrity',[
                            'reportId' => $deliveryReportId,
                        ],\Carbon\Carbon::now()->addSeconds($qcCountCheckDelay)->timestamp);
                    } else {
//                    $deliveryQcList = Api_DBDelivery::getDeliveryReportQcs($deliveryReportId);
//
//                    $qcImagePaths = [];
//                    foreach ($deliveryQcList as $qc) {
//                        $qcImages = Api_DBQualityControl::getQcImages($qc['id']);
//                        foreach ($qcImages as $qcImage) {
//                            $qcImagePaths[] = $qcImage['path'];
//                        }
//                    }
//                    $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
//                    if ($f) {
//                        fputs($f, '['.date("Y-m-d h:i:sa").'] [id='.$deliveryReportId.']'.json_encode($qcImagePaths, JSON_PRETTY_PRINT) . "\n");
//                    }
//                    fclose($f);
//
//                    $hasWrongPath = false;
//                    foreach ($qcImagePaths as $path) {
//                        if (!(strpos($path, 'https://fs.qforb.net') === 0)) {
//                            $hasWrongPath = true;
//                        }
//                    }
//
//                    if($hasWrongPath) {
//                        $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
//                        if ($f) {
//                            fputs($f, '['.date("Y-m-d h:i:sa").'] [WRONG PATH]' . "\n\n");
//                        }
//                        fclose($f);
//                        Queue::enqueue('CheckDeliveryReportsQcIntegrity','Job_Report_CheckDeliveryReportsQcIntegrity',[
//                            'reportId' => $deliveryReportId,
//                        ],\Carbon\Carbon::now()->addSeconds(10)->timestamp);
//                    } else {
//
                        $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
                        if ($f) {
                            fputs($f, '['.date("Y-m-d h:i:sa").'] [PDF success]' . "\n\n");
                        }
                        fclose($f);

                        Queue::enqueue('DeliveryReportsPDF','Job_Report_DeliveryReportsPDF',[
                            'report' => $deliveryReportId,
                        ],\Carbon\Carbon::now()->addSeconds($createPdfDelay)->timestamp);
//                    }
                    }
                }
            }

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
            if ($f) {
                fputs($f, ' [ ERROR ] ['.date("Y-m-d h:i:sa").'] ['.$deliveryReportId.'] ['.$exception->getMessage().'] ' . "\n");
            }
            fclose($f);
        }
    }
}