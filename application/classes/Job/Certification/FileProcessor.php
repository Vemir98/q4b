<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.05.2017
 * Time: 15:47
 */
class Job_Certification_FileProcessor
{
    public function perform(){
        ini_set('memory_limit', '-1');
        $filePath = $this->args['filePath'];
        $filePieces = explode('.',$filePath);
        $ext = strtolower(end($filePieces));
        $imagePath = preg_replace('~.pdf$~','.jpg',implode('.',$filePieces));
        if($ext == 'pdf' AND !file_exists($imagePath)){
            $converter = new PDFConverter($filePath.'[0]');
            $converter->convertToJPG();
            $imgPaths = $converter->getOutputFiles();
        }

        $img = new JBZoo\Image\Image($imagePath);
        $img->bestFit(4096,4096);
        $img->saveAs($imagePath);

    }
}