<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.05.2017
 * Time: 15:47
 */
class Job_Plan_PdfToImageConverter
{
    public function perform(){
        ini_set('memory_limit', '-1');
        $file = ORM::factory('PlanFile',$this->args['fileId']);
        $plan = $file->getPlan();
        $floors = $plan->floors->find_all();
        if($file->ext != 'pdf') return;
        $jpgPath = str_replace('.pdf','.jpg',$file->fullFilePath());

        if(!file_exists($jpgPath)){
            $pdf = new Pdf($file->fullFilePath());
            $pdf->setCompressionQuality(30);
            $imgPaths = $pdf->saveAllPagesAsImages(dirname($file->fullFilePath()),UTF8::str_ireplace('.pdf','',$file->name));
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
                    $tmpArr = $file->as_array();
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
    }
}