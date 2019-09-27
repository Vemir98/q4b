<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 16.11.2018
 * Time: 14:01
 */

class Task_Plan_FileMobileMinimizer extends Minion_Task
{
    protected function _execute(array $params)
    {
        set_time_limit(0);
        $planFiles = DB::query(Database::SELECT,'SELECT
  files.path,
  files.name
FROM pr_plans_files
  INNER JOIN files
    ON pr_plans_files.file_id = files.id')->execute();
        foreach ($planFiles as $file){
            $filename = strtolower($file['path'].'/'.$file['name']);
            if(!strpos($filename,'.pdf')) continue;
            $jpgPath = mb_substr($filename,0,mb_strlen($filename)-4).'.jpg';
            $pngPath = mb_substr($filename,0,mb_strlen($filename)-4).'.png';
            $jpePath = mb_substr($filename,0,mb_strlen($filename)-4).'.jpe';
            $filepath = '';
            if(file_exists($jpgPath)){
                $filepath = $jpgPath;
            }elseif (file_exists($jpePath)){
                $filepath = $jpePath;
            }elseif (file_exists($pngPath)){
                $filepath = $pngPath;
            }else{
                continue;
            }
            if(!strlen($filepath)){
                continue;
            }else{
                $filepath = DOCROOT.$filepath;
            }
            Queue::enqueue('imageForMobileMinimize','Job_Plan_ImageForMobileMinimizer',['filepath' => $filepath],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
        }
    }
}