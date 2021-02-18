<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:31
 */
use JonnyW\PhantomJs\Client;
class Job_Report_DeliveryReportsPDF
{
    public function perform(){
            $client = Client::getInstance();
            $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
            $client->getEngine()->addOption('--cookies-file=cook.txt');


            $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net', 'GET');
            $request->addHeader('Pjsbot76463', '99642');
            $response = $client->getMessageFactory()->createResponse();
            $client->send($request, $response);

            /**
             * @see JonnyW\PhantomJs\Http\CaptureRequest
             **/
            $request = $client->getMessageFactory()->createPdfRequest('https://qforb.net/he/reports/delivery/print/' . $this->args['report']);
            $request->addHeader('Pjsbot76463', '99642');

            $request->setOutputFile((DOCROOT . 'media/data/delivery-reports/' . $this->args['report'] . '/file.pdf'));
            $request->setFormat('A4');
//        $request->setOrientation('landscape');
            $request->setViewportSize(575, 1200);
            $request->setPaperSize(575, 1200);
            /**
             * @see JonnyW\PhantomJs\Http\Response
             **/
            $response = $client->getMessageFactory()->createResponse();
            //$request->setDelay(3);
            // Send the request
            $client->send($request, $response);
            exec("chmod -R 777 /home/qforbnet/www/media/data/delivery-reports/".$this->args['report'] . '/file.pdf');
    }
}