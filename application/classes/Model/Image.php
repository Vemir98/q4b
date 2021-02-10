<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 05.10.2016
 * Time: 6:29
 */
class Model_Image extends Model_File
{
    protected $_allowed_ext = ['jpe','jpeg','jpg','png','tif','tiff'];
//    public function rules()
//    {
//        $rules = parent::rules();
//        $rules['ext'][] =
//            [
//                function( Validation $valid){
//                    if(!in_array($this->ext,$this->_allowed_ext)){
//                        $valid->error('ext', 'invalid_file_ext');
//                    }
//                },
//                [':validation']
//            ]
//        ;
//        return $rules;
//    }


    public function replaceSourceWithBase64String($str,$quality = 50){
        $img = new JBZoo\Image\Image($str);
        $img->saveAs(DOCROOT.$this->originalFilePath(),$quality);
    }

    public function getPath($w = null,$h = null,$crop = false){
        $dim=[];
        if($w){
            $dim[] = 'w'.$w;
        }
        if($h){
            $dim[] = 'h'.$h;
        }

        if($crop){
            $dim []= 'c';
        }
        return ImageFly::create_path($this->originalFilePath(),implode('-',$dim));
    }

    protected function _generateThumb(){
        $path = [DOCROOT.$this->path];
        for($i = 0; $i < 3; $i++){
            $path[] = substr(strtolower($this->name),$i*2,2);
        }
        $path = implode('/',$path);
        if(!is_dir($path))
            @mkdir($path,0755,true);
        $filePath = $path.'/'.$this->name;

        $img = new JBZoo\Image\Image(DOCROOT.$this->path.'/'.$this->name);
        $img = $img->thumbnail(756,500);
        $img->saveAs($filePath,50);

    }

    public function getBigThumbPath(){
        $path = str_replace('https://fs.qforb.net/','',$this->path);
        $path =  str_replace('/','-',$path . '-' . $this->name);
        return 'https://fs.qforb.net/image/miniature/w756-h500-q50/' . $path;
    }

    public function __getBigTHumb(){
        $path = [DOCROOT.$this->path];
        for($i = 0; $i < 3; $i++){
            $path[] = substr(strtolower($this->name),$i*2,2);
        }
        $path = implode('/',$path);
        if(!is_dir($path))
            @mkdir($path,0755,true);
        $filePath = $path.'/'.$this->name;
        if(!file_exists($filePath)){
            ini_set('memory_limit', '-1');
            $img = new JBZoo\Image\Image(DOCROOT.$this->path.'/'.$this->name);
            $img = $img->thumbnail(756,500);
            $img->saveAs($filePath,50);
        }
        $filePath = str_ireplace(DOCROOT,'/',$filePath);
        return 'https://fs.qforb.net/image/miniature/w756-h500-q50/' . str_replace('/','-',$this->path . '');
    }
}