<?php defined('SYSPATH') OR die('No direct script access.');
use \Carbon\Carbon as Carbon;
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.05.2017
 * Time: 15:42
 */
class Listener_Plan_File
{
    /**
     * Конвертация pdf в изображение
     * @param $event
     * @param $sender
     * @param Model_PlanFile $file
     */
    public static function pdfToImage($event,$sender,Model_PlanFile $file){
        if($file->ext == 'pdf')
        Queue::enqueue('pdfToImage','Job_Plan_PdfToImageConverter',['fileId' => $file->id],Carbon::now()->addSeconds(30)->timestamp);
    }
}