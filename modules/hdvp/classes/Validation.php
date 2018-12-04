<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.10.2016
 * Time: 6:28
 */
class Validation extends Kohana_Validation
{
    public function setValidationImageRules($field, $size = '2MB', $formats = array('jpe','jpeg','jpg','png','tif','tiff'), $checkSignature = true){

        $this->rule($field, 'Upload::valid');
        $this->rule($field, 'Upload::not_empty');
        $this->rule($field, 'Upload::size', array(':value', $size));
        $this->rule($field, 'Upload::type', array(':value', $formats));
        $this->rule($field, 'Upload::image');
        if($checkSignature){
            $this->rule($field, 'Upload::signature');
        }

        return $this;
    }

    public function setValidationTrackingRules($field, $size = '2MB', $formats = array('jpe','jpeg','jpg','png','tif','tiff','pdf'), $checkSignature = true){

        $this->rule($field, 'Upload::valid');
        $this->rule($field, 'Upload::not_empty');
        $this->rule($field, 'Upload::size', array(':value', $size));
        $this->rule($field, 'Upload::type', array(':value', $formats));
        if($checkSignature){
            $this->rule($field, 'Upload::signature');
        }

        return $this;
    }
}