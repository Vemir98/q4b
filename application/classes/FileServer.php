<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.01.2021
 * Time: 23:31
 */

class FileServer
{
    private $_secret = '5MI26UEQW3T5OU7U';

    private $_lazyImageTaksData = array();
    private $_lazyPdfTaksData = array();
    private $_lazyFileTaksData = array();

    public function addImageTask($imageUrl,$callbackUrl){
//        $a = new GAuthenticator();
//        $code = $a->getCode('5MI26UEQW3T5OU7U');
        $request = Request::factory('https://fs.qforb.net/api/v1/download-image')
        ->method(Request::POST)
//        ->headers('Content-Type', 'text/xml')
        ->post(
            array(
                'imageUrl' => $imageUrl,
                'callbackUrl' => $callbackUrl
            ));
        $request->client()->options(array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ));
        $response = $request->execute();
//        file_put_contents(DOCROOT.'resp.txt',$response);
    }

    public function addFileTask($imageUrl,$callbackUrl){
        $request = Request::factory('https://fs.qforb.net/api/v1/download-file')
            ->method(Request::POST)
            ->post(
                array(
                    'fileUrl' => $imageUrl,
                    'callbackUrl' => $callbackUrl
                ));
        $request->client()->options(array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ));
        echo $request->execute();
    }

    public function addPdfTask($pdfUrl,$callbackUrl,$convertToJpg = 0, $mobileMinimize = 0){
        $request = Request::factory('https://fs.qforb.net/api/v1/download-pdf')
            ->method(Request::POST)
            ->post(
                array(
                    'pdfUrl' => $pdfUrl,
                    'callbackUrl' => $callbackUrl,
                    'convertToJpg' => $convertToJpg,
                    'mobileMinimize' => $mobileMinimize,
                ));
        $request->client()->options(array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ));
        $request->execute();
    }

    public function addReplaceImageTask($imageUrl,$replaceUrl,$callbackUrl){
        $request = Request::factory('https://fs.qforb.net/api/v1/replace-image')
            ->method(Request::POST)
            ->post(
                array(
                    'imageUrl' => $imageUrl,
                    'callbackUrl' => $callbackUrl,
                    'replaceUrl' => $replaceUrl,
                ));
        $request->client()->options(array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ));
        $request->execute();
    }

    public function deleteFile($path){
        $request = Request::factory('https://fs.qforb.net/api/v1/delete-file')
            ->method(Request::POST)
            ->post(
                array(
                    'fileUrl' => $path,
                ));
        $request->client()->options(array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ));
        $request->execute();
    }

    public function addLazyFileTask($fileUrl,$callbackUrl){
        $this->_lazyFileTaksData[] = ['url' => $fileUrl, 'callback' => $callbackUrl];
    }

    public function addLazyPdfTask($pdfUrl,$callbackUrl,$convertToJpg = 0, $mobileMinimize = 0){
        $this->_lazyPdfTaksData[] = ['url' => $pdfUrl, 'callback' => $callbackUrl, 'toJpg' => $convertToJpg, 'min' => $mobileMinimize];
    }

    public function addSimpleImageTask($imageUrl,$imageFileId){
        $this->addImageTask(
            $imageUrl,
            'https://qforb.net/fileserver/callbackimage?fileId=' . $imageFileId
        );
    }

    public function addLazySimpleImageTask($imageUrl,$imageFileId){
        $this->_lazyImageTaksData[] = ['url' => $imageUrl, 'id' => $imageFileId];
    }

    public function sendLazyTasks(){
        foreach ($this->_lazyImageTaksData as $img){
            $this->addSimpleImageTask($img['url'],$img['id']);
        }

        foreach ($this->_lazyPdfTaksData as $pdf){
            $this->addPdfTask($pdf['url'],$pdf['callback'],$pdf['toJpg'],$pdf['min']);
        }

        foreach ($this->_lazyFileTaksData as $file){
            $this->addFileTask($file['url'],$file['callback']);
        }
    }

}