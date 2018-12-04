<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2018
 * Time: 14:14
 */
class HDVPApiDocGen
{
    protected $_apiPath = APPPATH.'classes/Controller/Api/';

    protected $_currentContent;

    public function generate(){
        $output = [];
        $files = scandir($this->_apiPath);
        if(count($files) <= 2) return;

        foreach ($files as $key => $val){
            if($val == '.' OR $val == '..'){
                unset($files[$key]);
            }else{
                $key = reset(explode('.',$val));
               $tmpArr = $this->parseFile($this->_apiPath.$val);
//               $tmpArr = array_diff($tmpArr,array(),array(array()));
               if(!empty($tmpArr)){
                   foreach ($tmpArr as $a){
                       if(!empty($a)){
                        $output [$key][]= $a;
                       }
                   }

               }
            }
        }

//        return $output;
        return  View::factory('api/theme',['items' => $output]);
    }

    public function parseFile($fileName){
//        $content = file_get_contents($fileName);
//        $this->_currentContent = explode("\n",$content);
////        var_dump($this->_currentContent);
//        $output = [];
//        foreach ($this->_currentContent as $idx => $c){
//            if(strpos($c,'public function action_') !== false){
//                $output [] = $this->_reconstructComment($this->_parseComment($idx-1));
//            }
//        }

        $file = new SplFileObject($fileName);
        $this->_currentContent = [];
        while (!$file->eof()) {
            $this->_currentContent[] = $file->fgets();
        }
        $file = null;
        $output = [];
        foreach ($this->_currentContent as $idx => $c){
            if(strpos($c,'public function action_') !== false){
                preg_match('~action_([^( ]+)\(?\s?~',$c,$matches);
                $output [] = Arr::merge($this->_reconstructComment($this->_parseComment($idx-1)),['func' => $matches[1]]);
            }
        }
        return $output;
    }

    private function _parseComment($idx)
    {
        $output = [];
        for ($i = $idx; $i > 0; $i--){
            if(empty(trim($this->_currentContent[$i]))) continue;
            if($i != $idx){
                if(strpos($this->_currentContent[$i],'**') !== false){
                    $output[] = '/**';
                    return array_reverse($output);
                }
                if(strpos($this->_currentContent[$i],'*') === false){
                    return null;
                }
            }

            $output[] = $this->_currentContent[$i];
        }
        return $output;
    }

    private function _reconstructComment($comment){
        $output = [];
        $key = null;
        foreach ($comment as $c){
            preg_match('~\@(\S+)\s(.*)~ui',$c,$matches);
            if(count($matches) == 3){
                $key = $matches[1];
                if(!empty(trim($matches[2]))){
                    $output[$key][] = '<span class="'.$key.'">'.trim($matches[2]).'</span>';
                }
            }else{
                preg_match('~\*\s(.*)~ui',$c,$matches);
                if(count($matches) == 2){
                    if(!empty(trim($matches[1]))) {
                        $output[$key][] = '<span class="'.$key.'">'.trim($matches[1]).'</span>';
                    }
                }
            }

        }

        foreach ($output as $k => $o){
            $output[$k] = implode("\n",$output[$k]);
        }

        return $output;
    }
}