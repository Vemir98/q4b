<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 13.05.2020
 * Time: 10:55
 */
//convert -background white -alpha remove 2.pdf 2.jpg
class PDFConverter {
    private $_pdfPath;
    private $_outFilePath;
    private $_dir;
    private $_name;
    private $_output;
    private $_outputFormat = 'jpg';
    public function __construct($filepath)
    {
        $this->_pdfPath = $filepath;
        $pathinfo = pathinfo($filepath);
        $this->_name = $pathinfo['filename'];
        $this->_dir = $pathinfo['dirname'].'/';
        $this->_outFilePath = $this->_dir.$this->_name.'.jpg';
    }

    public function convertToJPG(){
        $cmd = sprintf('convert -background white -alpha remove -quality 30 %1$s %2$s',$this->_pdfPath,$this->_outFilePath);
        $files1 = scandir($this->_dir);
        $resp = shell_exec($cmd);
        $files2 = scandir($this->_dir);
        $this->_output = array_diff($files2,$files1);
        if(count($this->_output)){
            $key = array_search($this->_name.'-0.'.$this->_outputFormat,$this->_output);
            if(!empty($key)){
                rename($this->_dir.$this->_output[$key], $this->_dir.$this->_name.'.'.$this->_outputFormat);
                $this->_output[$key] = $this->_name.'.'.$this->_outputFormat;
            }
            $tmpOutput = [];
            foreach ($this->_output as $k => $v){
                if($k == $key){
                    $tmpOutput[0] = $v;
                }else{
                    $arr = explode('-',$v);
                    $idx = (int)$arr[1];
                    $tmpOutput[$idx] = $v;
                }
            }
            $this->_output = $tmpOutput;
            ksort($this->_output);
            return true;
        }else{
            return false;
        }
    }

    public function getOutputFiles(){
        return $this->_output;
    }

//Добавить переименование первого файла
    public function test($filename = DOCROOT.'qpdf/2.pdf'){
        $pathinfo = pathinfo($filename);
        $ext = $pathinfo['extension'];
        $name = $pathinfo['filename'];
        $dir = $pathinfo['dirname'].'/';
        $out = $dir.$name.'.jpg';

        $cmd = sprintf('convert -background white -alpha remove -quality 30 %1$s %2$s',$filename,$out);
        $files1 = scandir($dir);
        shell_exec($cmd);
        $files2 = scandir($dir);
        $outputArr = array_diff($files2,$files1);
        if(count($outputArr)){
            $key = array_search($name.'-0.jpg',$outputArr);
            rename($dir.$outputArr[$key], $dir.$name.'.jpg');
            $outputArr[$key] = $name.'.jpg';
        }

        return $outputArr;
    }
}