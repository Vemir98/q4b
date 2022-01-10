<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.03.2020
 * Time: 18:33
 */

class VueJs
{
    private $_scripts = [];
    private $_styles = [];

    static $_instance;

    public static function instance(){
        if(self::$_instance === null){
            self::$_instance = new VueJs();
        }
        return self::$_instance;
    }

    private function __construct(){
        $this->addScript("https://cdn.jsdelivr.net/npm/vue/dist/vue.js",1);
        $this->addStyle("/media/css/vue/components.css",1);
    }

    private function __clone(){}
    private function __wakeup(){}

    public function addScript($path, $relevance = 100){
        $this->_scripts [$relevance][] = "<script src=\"{$path}\"></script>";
    }

    public function addStyle($path, $relevance = 100){
        $this->_styles[$relevance] = "<link rel=\"stylesheet\" href=\"{$path}\">";
    }

    public function render(){
        $output = "";
        if(!empty($this->_styles)){
            $output .= implode(PHP_EOL,$this->_styles).PHP_EOL.PHP_EOL.PHP_EOL;
        }
        $scripts = [];
        if(!empty($this->_scripts)){
            foreach ($this->_scripts as $scrs){
                foreach ($scrs as $s){
                    $scripts[] = $s;
                }
            }

            $output .= implode(PHP_EOL,$scripts).PHP_EOL.PHP_EOL.PHP_EOL;
        }
        return $output;
    }

    public function addComponent($name){
        $this->addScript("/media/js/vue/components/{$name}.js",20);
    }

    public function includeMultiselect(){
        $this->addScript("https://unpkg.com/vue-multiselect@2.1.6",5);
        $this->addStyle("https://unpkg.com/vue-multiselect@2.1.6/dist/vue-multiselect.min.css");
    }

    public function includeSignaturePad(){
        $this->addScript("https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js",5);
    }

    public function includeDateTimePiker(){
        $this->addScript("https://qforb.net/media/js/vue/libs/vue2-datepicker/index.js",5);
        $this->addStyle("https://qforb.net/media/js/vue/libs/vue2-datepicker/index.css",5);
    }

    public function includeCharts(){
        $this->addScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js",5);
    }

    public function includeJsPDF(){
//        $this->addScript("https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js",5);
        $this->addScript("https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js",5);
        $this->addScript("https://html2canvas.hertzen.com/dist/html2canvas.min.js",5);
    }

    public function includeAxios(){
        $this->addScript("https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js",5);
    }
}