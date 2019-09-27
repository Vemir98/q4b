<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.02.2018
 * Time: 17:23
 */
class Controller_Api_Companies extends HDVP_Controller_API
{
    /**
     * @title Список компаний
     * @desc Возвращает список компаний доступных для данного пользователя
     * если передать параметр items где ключ id компании а значение updatedAt дата обновления то в случае
     * обновлении данных на сервере будут возвращены обновленные компании для переданного списка. Если переданные идентификаторы в запросе не доступный
     * то в ответе для данного элемента <strong>status будет deleted</strong>
     * @param [items] - Идентификаторы и даты обновлений в Unix Timestamp. На пример: items[10]=1502641402&items[11]=1502637955
     * @url http://constructmngr/api/json/v1/{token}/companies
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_list(){
        $this->_setUsrMinimalPriorityLvl(Enum_UserPriorityLevel::Company);

        $clientData = $this->getOrPost('items');
        $clientItems = [];
        $allClientIds = [];//идентификаторы компаний
        $availableClientIds = [];//идентификаторы компаний которые доступны для данного пользователя
        $deletedClientIds = [];

        if(!empty($clientData)){
            foreach ($clientData as $cId => $cUpdated){
                $clientItems[$this->getUIntParamOrDie($cId)] = $this->getUIntParamOrDie($cUpdated);
                $allClientIds[] = $this->getUIntParamOrDie($cId);
            }
        }
        $filename = self::CACHE_DIR.'/companies/list-'.((int)$this->_client->id).'.json';
        if( file_exists($filename)){
            $this->_responseData['items'] = json_decode(file_get_contents($filename),true);
        }else {
            $this->_responseData['items'] = Api_DBHelper::getCompaniesList($this->_client->id);
            if(!is_dir(self::CACHE_DIR.'/companies/')){
                mkdir(self::CACHE_DIR.'/companies/', 0777, true);
            }
            file_put_contents($filename,json_encode($this->_responseData['items']));
        }
        if(!empty($this->_responseData['items']) AND !empty($clientItems)){
            foreach ($this->_responseData['items'] as $key => $item){
                if(empty($clientItems[$item['id']])){
                    unset($this->_responseData['items'][$key]);
                }else{
                    $availableClientIds[] = $item['id'];
                }
                if($clientItems[$item['id']] >= $item['updatedAt']){
                    unset($this->_responseData['items'][$key]);
                }
            }
            if(count($allClientIds) != count($availableClientIds)){
                $deletedClientIds = array_diff($allClientIds,$availableClientIds);
            }

            if(!empty($deletedClientIds)){
                foreach ($deletedClientIds as $id){
                    $this->_responseData['items'][$id] = [
                        'id' => $id,
                        'status' => "deleted"
                    ];
                }
            }

            $this->_responseData['items'] = array_values($this->_responseData['items']);
        }

        if(!empty($this->_responseData['items'])){
            if($this->_user->getRelevantRole('priority') > Enum_UserPriorityLevel::Corporate){
                foreach($this->_responseData['items'] as $key => $item){
                    if($item['id'] != $this->_user->company_id){
                        unset($this->_responseData['items'][$key]);
                    }
                }
            }
        }

        $this->_responseData['updated'] = filemtime($filename);
    }


    /**
     * @title Професси и специальности
     * @desc Возвращает список профессий и специальностей доступных для указанной категории
     * @url http://constructmngr/api/json/v1/{token}/companies/crafts_and_professions/{companyId}
     * @throws API_Exception 500
     * @method GET/POST
     * @response пусто
     */
    public function action_crafts_and_professions(){

        $company = ORM::factory('Company',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $company->loaded()){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $filename = self::CACHE_DIR.'/companies/crafts-professions/list-'.((int)$company->id).'.json';
        if( file_exists($filename)){
            $data = json_decode(file_get_contents($filename),true);
            $this->_responseData['professions'] = $data['professions'];
            $this->_responseData['crafts'] = $data['crafts'];
            $this->_responseData['relation'] = $data['relation'];
        }else {
            $professions = Api_DBHelper::getCompanyProfessions($company->id);
            $crafts = Api_DBHelper::getCompanyCrafts($company->id);
            $profCraftsRel = Api_DBHelper::getCompanyProfCraftRelation($company->id);

            $this->_responseData['professions'] = $professions;
            $this->_responseData['crafts'] = $crafts;
            $this->_responseData['relation'] = $profCraftsRel;

            if(!is_dir(self::CACHE_DIR.'/companies/crafts-professions')){
                mkdir(self::CACHE_DIR.'/companies/crafts-professions', 0777, true);
            }
            file_put_contents($filename,json_encode(['professions' => $this->_responseData['professions'], 'crafts' => $this->_responseData['crafts'],'relation' => $this->_responseData['relation']]));
        }
        $this->_responseData['updated'] = filemtime($filename);
    }

}