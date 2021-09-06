<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Companies_Entities extends HDVP_Controller_API
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
    public function action_list_get(){
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
            //file_put_contents($filename,json_encode($this->_responseData['items']));
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
     * Get crafts list for company
     * if you pass in get param "fields" property list devided by "," will returned only that property list
     * https://qforb.net/api/json/v1/<appToken>/companies/<companyId>/entities/crafts?fields=name,projectId,typeId...
     * fields must be in camelCase then in code need to convert it to underscore for make request to db
     */
    public function action_crafts_get(){
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = array_map(function ($item){
                return Inflector::underscore(Inflector::decamelize($item));
            },$fields);
        }
        $companyId = $this->getUIntParamOrDie($this->request->param('companyId'));
        $crafts = Api_DBCompanies::getCompanyCrafts($companyId, $fields);
        $this->_responseData['items'] = $crafts;
    }
    public function action_projects_get(){
        $id = $this->getUIntParamOrDie($this->request->param('companyId'));

        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }

        $items = Api_DBCompanies::getCmpProjects($id);
        $this->_responseData['items'] = $items;
    }
    public function action_modules_get(){
//        $modules = Api_DBModules::getModules();
        try {
            $modules = Api_DBModules::getModulesForTasks();
            $this->_responseData = [
                'status' => 'success',
                'items' => $modules
            ];
        } catch (Exception $e) {
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
        }

    }
}