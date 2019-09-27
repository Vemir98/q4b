<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 29.04.2019
 * Time: 17:25
 */

class ReportQuery
{
    protected $_rawParams;
    protected $_params;

    public function __construct($query = null)
    {
        if($query){
            $this->_rawParams = $query;
        }
        $this->processParams();
    }

    private function processParams()
    {
        if(empty($this->_rawParams)) return;
        if( ! is_array($this->_rawParams)){
            parse_str($this->_rawParams,$this->_params);
        }else{
            $this->_params = $this->_rawParams;
        }

    }

    public function getParams(){
        return $this->_params;
    }
}