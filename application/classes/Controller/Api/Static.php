<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.04.2018
 * Time: 2:31
 */
class Controller_Api_Static extends HDVP_Controller_API
{
    private $_langs = ['he','en','ru'];

    protected $_responseDataItemsForcedAsArray = false;
    /**
     * @title Интернеционализация
     * @desc Возвращает список ключей и их переводов использующихся в проекте
     * Запрос можно отправлять методом GET или POST
     * @param [items] - языки.
     * @url http://constructmngr/api/json/v1/{token}/static/translation
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_translation(){
        $langs = $this->getOrPost('lang');
        if(!empty($langs)){
            if( ! is_array($langs)){
                $langs = [$langs];
            }
            foreach ($langs as $l){
                if(!in_array($l,$this->_langs)){
                    throw API_Exception::factory(500,'Incorrect data');
                }
            }
        }else{
            $langs = $this->_langs;
        }

        foreach ($langs as $lang){
            $this->_responseData['items'][$lang] = Kohana::load(APPPATH.'i18n/'.$lang.'.php');
        }
    }

    /**
     * @title Константы
     * @desc Возвращает список констант использующихся в проекте
     * Запрос можно отправлять методом GET или POST
     * @param [items] - константы.
     * @url http://constructmngr/api/json/v1/{token}/static/not-sensitive
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_notSensitive(){

        $this->_responseData['items']['projectStage'] = array_values(Enum_ProjectStage::toArray());
        $this->_responseData['items']['qualityControlConditionLevel'] = array_values(Enum_QualityControlConditionLevel::toArray());
        $this->_responseData['items']['qualityControlConditionList'] = array_values(Enum_QualityControlConditionList::toArray());
        $this->_responseData['items']['qualityControlApproveStatus'] = array_values(Enum_QualityControlApproveStatus::toArray());
        $this->_responseData['items']['qualityControlStatus'] = array_values(Enum_QualityControlStatus::toArray());
    }
}