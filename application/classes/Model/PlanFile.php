<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.03.2017
 * Time: 0:46
 */
class Model_PlanFile extends Model_File
{
    protected $_allowed_ext = ['jpe','jpeg','jpg','png','tif','tiff','pdf'];

    protected $_has_many = [
        '__plans' => [
            'model' => 'PrPlan',
            'through' => 'pr_plans_files',
            'foreign_key' => 'file_id',
            'far_key' => 'plan_id'
        ]
    ];

    public function getPlan(){
        return $this->__plans->find();
    }

    public function getImageLink($w = null,$h = null,$crop = false){
        if(!$this->loaded()){
            return null;
        }
        $path = $this->originalFilePath();
        $plan = $this->getPlan();
        $floors = $plan->floors->find_all();
        if($this->ext == 'pdf'){
            $jpgPath = str_replace('.pdf','.jpg',$this->fullFilePath());

            if(!file_exists($jpgPath)){
                $pdf = new Pdf($this->fullFilePath());
                $pdf->setCompressionQuality(30);
                $imgPaths = $pdf->saveAllPagesAsImages(dirname($this->fullFilePath()),UTF8::str_ireplace('.pdf','',$this->name));
                foreach ($imgPaths as $idx => $p){
                    if($idx > 0){
                        $newPlan = ORM::factory('PrPlan');
                        $tmpArr = $plan->as_array();
                        unset($tmpArr['id'],$tmpArr['updated_by'],$tmpArr['approved_by']);
                        //throw new Exception(var_export($tmpArr,true));
                        $newPlan->values($tmpArr);
                        $newPlan->_setCreatedBy($plan->created_by);
                        $newPlan->_setUpdatedBy($plan->created_by);
                        $newPlan->scope = Model_PrPlan::getNewScope();
                        $newPlan->save();
                        if(count($floors)){
                            foreach ($floors as $floor){
                                $newPlan->add('floors',$floor->id);
                            }
                        }

                        $newFile = ORM::factory('PlanFile');
                        $tmpArr = $this->as_array();
                        unset($tmpArr['id']);
                        $newFile->values($tmpArr);
                        $newFile->mime = 'image/jpeg';
                        $newFile->ext = 'jpg';
                        $newFile->name = end(explode('/',$p));
                        $newFile->original_name .= ' (p-'.$idx.')';
                        $newFile->token = md5($newFile->original_name).base_convert(microtime(false), 10, 36);
                        $newFile->_setCreatedBy($plan->created_by);
                        $newFile->save();
                        $newPlan->add('files', $newFile->pk());

                    }
                }

            }
            $path = str_replace('.pdf','.jpg',$path);
        }
        if($w){
            $dim[] = 'w'.$w;
        }
        if($h){
            $dim[] = 'h'.$h;
        }

        if($crop){
            $dim []= 'c';
        }
        if(empty($dim)){
            return $path;
        }else{
            return ImageFly::create_path(str_replace('//','/',$path),implode('-',$dim));
        }


    }

    public function getName(){
        return $this->customName() ?: $this->original_name;
    }
}