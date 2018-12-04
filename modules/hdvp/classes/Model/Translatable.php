<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 05.05.2016
 * Time: 21:33
 */
class Model_Translatable extends ORM
{

    private $_tTranslationData;
    private $_tUseDefaultLang = false;
    protected $_tTranslationStructure = [
        'title' => '',
        'desc' => '',
        'meta_title' => '',
        'meta_desc' => '',
        'meta_keys' => ''
    ];

    public function __construct($id = null)
    {
        if(empty($this->_has_many['translations']))
        $this->_has_many +=[
            'translations' => [
                'model' => ucfirst(Inflector::singular($this->_table_name,1)).'Translation',
                'foreign_key' => Inflector::singular($this->_table_name,1).'_id',

            ]
        ];


        parent::__construct($id);
        if($id)
        $this->getTranslations();
    }

    public function getTranslations($forced = false){
        if( ! $this->_tTranslationData || $forced){
            $this->_tTranslationData = $this->translations->find_all();
        }
        return $this->_tTranslationData;
    }

    public function getTranslation($lang, $default = 'ru'){
        $output = null;
        if($this->getTranslations() AND $this->_tTranslationData->count()){
            foreach($this->_tTranslationData as $t){
                if($t->lang == $lang){
                    return $t;
                }
                else if($this->_tUseDefaultLang AND $t->lang == $default) $output = $t;

            }
        }
//        if($output == null){
//            $output = json_decode(json_encode($this->_tTranslationStructure));
//        }
        return $output;
    }

    public function getTranslationsCount(){
        return $this->getTranslations() ? $this->getTranslations()->count() : 0;
    }

    public function hasTranslations(){
        return (bool) $this->getTranslationsCount();
    }

    public function hasTranslation($lang){
        return (bool) $this->getTranslation($lang,null);
    }

    public function removeTranslation($lang){
        Database::instance()->begin();
        try{
            Database::instance()->begin();
            $this->getTranslation($lang)->delete();
            Database::instance()->commit();
        }catch(Exception $e){
            Database::instance()->rollback();
            throw new $e;
        }

    }

    public function removeTranslations(){
        if($this->hasTranslations()){
            try{
                Database::instance()->begin();
                foreach($this->getTranslations() as $t){
                    $t->delete();
                }
                Database::instance()->commit();
            }catch(Exception $e){
                Database::instance()->rollback();
                throw new $e;
            }
        }
    }

    public function makeTranslation($lang){
        if($this->hasTranslation($lang)){
            return $this->getTranslation($lang);
        }
        $trans = ORM::factory($this->_has_many['translations']['model']);
        $trans->lang = $lang;
        $trans->{$this->_has_many['translations']['foreign_key']} = $this->id;
        return $trans;
    }

    public function getCurrentTranslation(){
        return $this->getTranslation(WS_Lang::getCurrentLang()->iso2);
    }

    public function createOrUpdate($data){
        $vals = $data;
        unset($vals['content']);
        $this->values($vals);
        foreach($data['content'] as $lang => $arr){
            $isset = false;
            foreach($arr as $d){
                if(!empty(trim($d)))$isset = true;
            }
            if(!$isset) unset($data['content'][$lang]);
        }
        if(empty($data['content'])) throw new Exception('Контент не может быть пустым');
        try{
            Database::instance()->begin();
            $this->save();
            if(!empty($data['content'])){
                foreach($data['content'] as $lang => $content){
                    $t = $this->makeTranslation($lang);
                    $t->values($content);
                    $t->{$this->_has_many['translations']['foreign_key']} = $this->pk();
                    $t->save();
                }
            }
            Database::instance()->commit();
        }catch(Exception $e){
            Database::instance()->rollback();
            throw $e;
        }

    }

 
}