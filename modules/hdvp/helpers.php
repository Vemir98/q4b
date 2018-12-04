<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 17:28
 */
if( ! function_exists('render')){
    function render(&$content){
        $output = '';
        if(!empty($content));
        if(is_array($content)){
            foreach ($content as $c){
                $output .= render($c).PHP_EOL;
            }
        }else{
            $output = Security::encode_php_tags($content);
        }

        return $output;
    }
}