<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 12:19
 */
class Valid extends Kohana_Valid
{
    public static function check_file_signature($filepath,$ext){
        $ext = strtolower($ext);
        $fileMagicNumbers = Kohana::$config->load('file_magic_numbers');
        $output = false;
        try{
            if(!empty($fileMagicNumbers[$ext])){
                $fp = fopen($filepath, 'r');
                foreach ($fileMagicNumbers[$ext] as $arr){
                    fseek($fp, 0);
                    if($arr['offset'])
                        fseek($fp, $arr['offset']);
                    $signature = explode(' ',trim($arr['sig']));
                    $bytes = $data = fread($fp, count($signature));
                    if(strtolower(bin2hex($bytes)) === strtolower(implode('',$signature))){
                        $output = true;
                        break;
                    }
                }
                
            }
        }catch (Exception $e){
            $output = false;
        }finally{
            if(isset($fp) AND !is_null($fp))
                fclose($fp);
        }
        
        return $output;
    }
}