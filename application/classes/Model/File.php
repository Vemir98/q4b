<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 05.10.2016
 * Time: 5:24
 */
abstract class Model_File extends MORM
{
    protected $_table_name = 'files';
    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_allowed_ext = [];

    protected $_has_one = [
        'custom_name' => [
            'model' => 'FileCustomName',
            'foreign_key' => 'file_id'
        ]
    ];
    

    /**
     * Перегрузка метода инициализации
     * Добавляем информацию о тех кто работал с текущей записью
     */
    protected function _initialize(){
        if(Auth::instance()->get_user()){
            $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
        }else{
            $this->_created_by_column = ['column' => 'created_by'];
        }

        parent::_initialize();
    }

    public function customName($name = null){
        $customName = $this->custom_name;
        if($name === null){
            if($customName->loaded()){
                return $customName->name;
            }else{
                return null;
            }
        }else{
            if($customName->loaded()){
                $customName->name = $name;
                $customName->save();
            }else{
                $customName = ORM::factory('FileCustomName');
                $customName->name = $name;
                $customName->file_id = $this->pk();
                $customName->save();
            }
        }
    }

    public function hasCustomName(){
        return $this->custom_name->loaded();
    }

    public function rules()
    {
        return [
            'name' => [
//                ['not_empty'],
            ],
            'original_name' => [
//                ['not_empty'],
            ],
            'token' => [
//                ['not_empty'],
            ],
            'mime' => [
//                ['not_empty'],
//                [
//                    function( Validation $valid){
//                        $mimes = Kohana::$config->load('mimes.'.strtolower($this->ext));
//                        if(empty($mimes) OR !in_array($this->mime,$mimes)){
//                            $valid->error('mime', 'invalid_mime_type');
//                        }
//                    },
//                    [':validation']
//                ]
            ],
            'ext' => [
//                ['not_empty'],
//                [
//                    function( Validation $valid){;
//                        if(!in_array(strtolower($this->ext),$this->_allowed_ext)){
//                            $valid->error('ext', 'invalid_file_ext');
//                        }
//                    },
//                    [':validation']
//                ]
            ],
            'path' => [
//                ['not_empty'],
            ]
        ];
    }
    
    public function isImage(){
        return in_array($this->ext,['jpe','jpeg','jpg','png','tif','tiff']);
    }

    public static function getFileExt($fileName){
        $arr = explode('.',$fileName);
        if(count($arr) >= 2){
            $ext = end($arr);
        }else{
            $ext = false;
        }

        return $ext ?: null;
    }

    public function originalFilePath(){
        return DS.$this->path.DS.$this->name;
    }

    public function fullFilePath(){
        return DOCROOT.$this->path.DS.$this->name;
    }
    public function filters()
    {
        return [
            true => [
                ['htmlentities'],
                ['strip_tags']
            ]
        ];
    }
}