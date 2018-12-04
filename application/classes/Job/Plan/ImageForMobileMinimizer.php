<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 15.11.2018
 * Time: 12:17
 */

class Job_Plan_ImageForMobileMinimizer
{
    public function perform(){
        $filepath = $this->args['filepath'];
        if(file_exists(mb_substr($filepath,0,mb_strlen($filepath)-4).'-mobile'.mb_substr($filepath,-4,4))){
            return;
        }
        $img = new JBZoo\Image\Image($filepath);
        $img->bestFit(4096,4096);
        $img->saveAs(mb_substr($filepath,0,mb_strlen($filepath)-4).'-mobile'.mb_substr($filepath,-4,4));
    }
}