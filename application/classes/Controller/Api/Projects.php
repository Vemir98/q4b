<?php

use Helpers\PushHelper;


/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2018
 * Time: 12:30
 */
class Controller_Api_Projects extends HDVP_Controller_API
{
    /**
     * @title Список проектов
* @desc Возвращает список проектов доступных для данного пользователя
* если передать параметр <b>companies</b> будут возвращены проекты для данных компаний, если передать параметр projects где ключ <b>id</b> проекта а значение <b>updatedAt</b> то в случае
* обновлении данных на сервере будут возвращены обновленные проектов для переданного списка.
* Запрос можно отправлять методом GET или POST
* @param [companies] - Идентификаторы компаний. На пример: companies[]=15&companies[]=18
* @param <br>[projects] - Идентификаторы проектов и даты обновлений в Unix Timestamp. На пример: projects[10]=1502641402&projects[11]=1502637955
* @url http://constructmngr/api/json/v1/{token}/projects
* @throws API_Exception 500
* @method GET/POST
* @response Пусто
*/
    public function action_list(){
        $companiesData = $this->getOrPost('companies');
        $companyIds = [];
        $projectsData = $this->getOrPost('projects');
        $projectsItems = [];

        $allClientIds = [];//идентификаторы проектов
        $availableClientIds = [];//идентификаторы проектов которые доступны для данного пользователя
        $deletedClientIds = [];

        if(!empty($companiesData)){
            foreach ($companiesData as $cId){
                $companyIds[$this->getUIntParamOrDie($cId)] = $this->getUIntParamOrDie($cId);
            }
        }

        if(!empty($projectsData)){
            foreach ($projectsData as $pId => $pUpdated){
                $projectsItems[$this->getUIntParamOrDie($pId)] = $this->getUIntParamOrDie($pUpdated);
                $allClientIds[] = $this->getUIntParamOrDie($pId);
            }
        }
        if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
            $this->_responseData['items'] = Api_DBHelper::getProjectsList((int)$this->_user->client_id);
        }else{
            $this->_responseData['items'] = $tmpItems = [];
            $usrClients = Api_DBHelper::getUserClientIds($this->_user->id);
            if($this->_user->client_id){
                $usrClients[] = $this->_user->client_id;
            }
            $usrClients = array_unique($usrClients);
            foreach ($usrClients as $clientId){

                $tmpItems = Api_DBHelper::getProjectsList($clientId);
                $this->_responseData['items'] = Arr::merge($this->_responseData['items'], $tmpItems);
            }
            unset($tmpItems,$usrClients);
        }

        if(!empty($this->_responseData['items'])){
            if(!empty($projectsItems)){
                foreach ($this->_responseData['items'] as $key => $item){
                    if(empty($projectsItems[$item['id']])){
                        unset($this->_responseData['items'][$key]);
                    }else{
                        $availableClientIds[] = $item['id'];
                    }
                    if($projectsItems[$item['id']] >= $item['updatedAt']){
                        unset($this->_responseData['items'][$key]);
                    }
                }
            }elseif (!empty($companyIds)){
                foreach ($this->_responseData['items'] as $key => $item){
                    if(empty($companyIds[$item['cmpId']])){
                        unset($this->_responseData['items'][$key]);
                    }else{
                        $availableClientIds[] = $item['id'];
                    }
                }

            }
            $usrProjects = ORM::factory('UserProjectsRelation')->getProjectIdsForUser($this->_user->id);
            if($this->_user->getRelevantRole('priority') > Enum_UserPriorityLevel::Corporate){
                //если компании
                if($this->_user->getRelevantRole('priority') <= Enum_UserPriorityLevel::Company){
                    foreach($this->_responseData['items'] as $key => $item){
                        if($item['cmpId'] != $this->_user->company_id){
                            if(!in_array($item['id'],$usrProjects)){
                                unset($this->_responseData['items'][$key]);
                            }
                        }
                    }
                }else{//если проект
                    foreach($this->_responseData['items'] as $key => $item){
                        if(!in_array($item['id'],$usrProjects)){
                            unset($this->_responseData['items'][$key]);
                        }
                    }
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

            if(count($this->_responseData['items'])){
                foreach($this->_responseData['items'] as $key => $item){
                    $this->_responseData['items'][$key]['usedTasks'] = Api_DBHelper::getProjectUsedTasks($item['id']);
                }
            }


            $this->_responseData['items'] = array_values($this->_responseData['items']);
        }
        $this->_responseData['updated'] = time();

    }

    public function action_test(){
        $generator = new HDVPApiDocGen();
        var_dump($generator->generate());
    }

    /**
     * @title Список сооружений
     * @desc Возвращает список доступных сооружений указанного проекта для данного пользователя
     * если передать параметр items где ключ id сооружения а значение updatedAt дата обновления то в случае
     * обновлении данных на сервере будут возвращены обновленные сооружения для переданного списка. Если переданные идентификаторы в запросе не доступный
     * то в ответе для данного элемента <strong>status будет deleted</strong>
     * Запрос можно отправлять методом GET или POST
     * @param [items] - Идентификаторы и даты обновлений в Unix Timestamp. На пример: items[10]=1502641402&items[11]=1502637955
     * @url http://constructmngr/api/json/v1/{token}/projects/objects/<projectID>
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_objects(){
        $project = ORM::factory('Project',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $project->loaded()){
            throw API_Exception::factory(500,'Incorrect identifier');
        }

        $clientData = $this->getOrPost('items');
        $clientItems = [];
        $allClientIds = [];//идентификаторы объектов
        $availableClientIds = [];//идентификаторы объектов которые доступны для данного пользователя
        $deletedClientIds = [];

        if(!empty($clientData)){
            foreach ($clientData as $oId => $cUpdated){
                $clientItems[$this->getUIntParamOrDie($oId)] = $this->getUIntParamOrDie($cUpdated);
                $allClientIds[] = $this->getUIntParamOrDie($oId);
            }
        }
        $this->_responseData['items'] = Api_DBHelper::getProjectObjectsList($project->id,$this->_client->id);
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
        $this->_responseData['updated'] = time();
    }

    /**
     * @title Структура сооружения
     * @desc Возвращает структуру указанного сооружения, этажи, помещения и комнаты(отделения)
     * Запрос можно отправлять методом GET или POST
     * @param [items] - этажи.
     * @url http://constructmngr/api/json/v1/{token}/projects/object_struct/<objectId>
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_object_struct(){
        $objectId = $this->getUIntParamOrDie($this->request->param('id'));
        if( ! Api_DBHelper::objectIssetForClient($objectId,$this->_client->id)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $floors = Api_DBHelper::getObjectFloors($objectId);
        $floorItems = $floorIds = $placeIds = $floorPlanIds = [];
        if (count($floors)) {
            foreach ($floors as $f) {
                $floorItems[$f['id']] = [
                    'id' => $f['id'],
                    'number' => $f['number'],
                    'places' => [],
                    'plans' => []
                ];
                array_push($floorIds, $f['id']);
            }
            $floorPlanIds = Api_DBHelper::getFloorsPlanIds($floorIds);
            if (!empty($floorPlanIds)) {
                foreach ($floorPlanIds as $f) {
                    $floorItems[$f['floor_id']]['plans'][] = $f['plan_id'];
                }
            }
            $places = Api_DBHelper::getFloorsPlaces($floorIds);
            if (count($places)) {

                foreach ($places as $p) {
                    $floorsPlaces[$p['id']] = [
                        'id' => $p['id'],
                        'name' => $p['name'],
                        'icon' => $p['icon'],
                        'type' => $p['type'],
                        'number' => $p['number'],
                        'customNumber' => $p['custom_number'],
                        'ordering' => $p['ordering'],
                        'spaces' => [],
//                            'plans' => []
                    ];
                    array_push($placeIds, $p['id']);
                }


                $spaces = Api_DBHelper::getPlacesSpaces($placeIds);
                if (count($spaces)) {
                    foreach ($spaces as $s) {
                        $floorsPlaces[$s['place_id']]['spaces'][] = [
                            'id' => $s['id'],
                            'description' => $s['desc'],
                            'type' => $s['type'],
                            'typeId' => $s['typeId']
                        ];
                    }
                }

                foreach ($places as $p) {
                    $floorItems[$p['floor_id']]['places'][] = $floorsPlaces[$p['id']];
                }


            }

            $this->_responseData['items'] = array_values($floorItems);
            }
        $this->_responseData['updated'] = time();
    }

    /**
     * @title Задачи
     * @desc Возвращает список задач для указанного проекта
     * Запрос можно отправлять методом GET или POST
     * @param [items] - задачи.
     * @url http://constructmngr/api/json/v1/{token}/projects/tasks/<projectId>
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_tasks(){
        $project = ORM::factory('Project',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $project->loaded()){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $this->_responseData['items'] = Api_DBHelper::getProjectObjectsList($project->id,$this->_client->id);

        $tasks = Api_DBHelper::getProjectTasks($project->id);
        $taskItems = $taskIds = [];
        if(count($tasks)){
            foreach ($tasks as $task){
                $taskItems[$task['id']] = [
                    'id' => $task['id'],
                    'name' => $task['name'],
                    'status' => $task['status'],
                    'crafts' => []
                ];

                $taskIds[] = $task['id'];
            }
            $crafts = Api_DBHelper::getFloorsCrafts($taskIds);
            if(count($crafts)){
                foreach ($crafts as $craft){
                    $taskItems[$craft['task_id']]['crafts'][] = [
                        'id' => $craft['id'],
                        'status' => $craft['status']
                    ];
                }
            }
        }

        $this->_responseData['items'] = array_values($taskItems);
        $this->_responseData['updated'] = time();
    }

    /**
     * @title Планы
     * @desc Возвращает список планов для указанного проекта
     * Запрос можно отправлять методом GET или POST
     * @param [items] - планы.
     * @url http://constructmngr/api/json/v1/{token}/projects/plans/<projectId>
     * @throws API_Exception 500
     * @method GET/POST
     * @response Пусто
     */
    public function action_plans(){
        $project = ORM::factory('Project',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $project->loaded()){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $this->_responseData['items'] = Api_DBHelper::getProjectObjectsList($project->id,$this->_client->id);
        $plans = Api_DBHelper::getProjectPlans($project->id);
        $planItems = $planIds = [];
        if(count($plans)){
            foreach ($plans as $plan){
                $planItems[$plan['id']] = [
                    'id' => $plan['id'],
                    'name' => $plan['name'],
                    'file' => $this->planFileLink($plan),
                    'createdAt' => $plan['createdAt'],
                    'createdBy' => $plan['createdBy'],
                    'updatedAt' => $plan['updatedAt'],
                    'updatedBy' => $plan['updatedBy'],
                    'approvedAt' => $plan['approvedAt'],
                    'approvedBy' => $plan['approvedBy'],
                    'approvalStatus' => $plan['approvalStatus'],
                    'date' => $plan['date'],
                    'description' => $plan['description'],
                    'edition' => $plan['edition'],
                    'scale' => $plan['scale'],
                    'placeId' => $plan['placeId'],
                    'objectId' => $plan['objectId'],
                    'professionId' => $plan['professionId'],
                    'status' => $plan['status'],
                    'crafts' => [],
                    'floors' => [],
                ];
                $planIds[] = $plan['id'];
            }
            $crafts = Api_DBHelper::getPlansCrafts($planIds);
            if(count($crafts)){
                foreach ($crafts as $craft){
                    $planItems[$craft['plan_id']]['crafts'][] = [
                        'id' => $craft['id']
                    ];
                }
            }
            $floors = Api_DBHelper::getPlansFloors($planIds);
            if(count($floors)){
                foreach ($floors as $floor){
                    $planItems[$floor['plan_id']]['floors'][] = [
                        'id' => $floor['id']
                    ];
                }
            }
        }

        $this->_responseData['items'] = array_values($planItems);
        $this->_responseData['updated'] = time();
    }

    private function planFileName($plan)
    {
        if(!empty($plan['fileCustomName'])){
            return $plan['fileCustomName'];
        }
        if(!empty($plan['fileOriginalName'])){
            return $plan['fileOriginalName'];
        }

        return $plan['name'];
    }

    private function planFileLink($plan){
        if((empty($plan['fileName']) OR empty($plan['filePath'])) AND empty($plan['mobilePath']) AND empty($plan['imagePath'])){
            return null;
        }
        if(!empty($plan['mobilePath'])) return $plan['mobilePath'];
        if(!empty($plan['imagePath'])) return $plan['imagePath'];

        return implode('/',[$plan['filePath'],$plan['fileName']]);
    }

    /**
     * @title Создание FKK
     * @desc создает fkk по заданным параметрам
     * @param <br>[space_id] - идентификатор комнаты
     * @param <br>[status] - статус ("qualityControlStatus")
     * @param <br>[project_stage] - этап ("projectStage")
     * @param <br>[due_date] - срок сдачи (d/m/Y)
     * @param <br>[description] - описание
     * @param <br>[tasks] - идентификатооры задач
     * @param <br>[profession_id] -идентификатор профессии
     * @param <br>[craft_id] - идентификатор специальности
     * @param <br>[severity_level] - уровень проблемы ("qualityControlConditionLevel") - отправляется в том случае если статус "invalid"
     * @param <br>[condition_list] - список условий ("qualityControlConditionList") - отправляется в том случае если статус "invalid"
     * @param <br>[plan_id] - идентификатор плана
     * @param <br>[images[]] - изображения
     * @url http://constructmngr/api/json/v1/{token}/projects/create_qcontrol/{placeId}
     * @throws API_Exception 500
     * @method POST
     * @response Пусто
     */
    public function action_create_qcontrol(){
        $place = ORM::factory('PrPlace',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $place->loaded()){
            throw new HTTP_Exception_404;
        }
        $clientData = Arr::extract($_POST,
            [
                'space_id',
                'status',
                'project_stage',
                'due_date',
                'description',
                'tasks',
                'profession_id',
                'craft_id',
                'severity_level',
                'condition_list',
                'plan_id',
                'message',
                'unique_token',
                'del_rep_id',
                'el_approval_id'
            ]);
        if(empty($clientData['el_approval_id']) OR !(int)$clientData['el_approval_id']){
            unset($clientData['el_approval_id']);
        }else{
            $clientData['el_approval_id'] = (int) $clientData['el_approval_id'];
        }

        $isDebug = Arr::get($_POST,'debug',false);
        $clientData['plan_id'] *=1;
        if(empty($clientData['del_rep_id']) OR !(int)$clientData['del_rep_id']){
            unset($clientData['del_rep_id']);
        }else{
            $clientData['del_rep_id'] = (int) $clientData['del_rep_id'];
        }
        $clientData['unique_token'] = (int) $clientData['unique_token'];
        if($clientData['unique_token']){
            $tmpQc = ORM::factory('QualityControl',['unique_token' => $clientData['unique_token']]);
            if($tmpQc->loaded()){
                throw API_Exception::factory(403,'Forbiden');
            }
        }else{
            unset($clientData['unique_token']);
        }
        $clientData['tasks'] = array_values($clientData['tasks']);
        $project = $place->project;
        if(!empty(trim($clientData['description'])))
        $clientData['description'] = '['.date('d/m/Y').'] '.$clientData['description'].PHP_EOL;
        $date = DateTime::createFromFormat('d/m/Y',$clientData['due_date']);
        if($date == null){
            file_put_contents(DOCROOT.'date_err.log',$clientData['due_date']);
            throw API_Exception::factory(500,'Incorrect date format');
        }
        $clientData['due_date'] = $date->getTimestamp();
        $clientData['space_id'] = $place->spaces->where('id','=',(int)$clientData['space_id'])->find()->id;
        $message = $clientData['message'];
        if($clientData['status'] != Enum_QualityControlStatus::Invalid){
            $clientData['severity_level']= $clientData['condition_list'] = null;
        }
        try{
            Database::instance()->begin();
            if(empty($clientData['tasks'])){
                throw API_Exception::factory(500,'Empty Tasks');
            }
            $project->makeProjectPaths();
            $files = $this->_pFArr();
            if(!empty($files) AND !empty($files['images'])){
                foreach ($files['images'] as $key => $image){
                    $uploadedFiles[] = [
                        'name' => str_replace($project->qualityControlPath().DS,'',Upload::save($image,null,$project->qualityControlPath())),
                        'original_name' => $image['name'],
                        'ext' => Model_File::getFileExt($image['name']),
                        'mime' => $image['type'],
                        'path' => str_replace(DOCROOT,'',$project->qualityControlPath()),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                }
            }
            $qc = ORM::factory('QualityControl');
            $qc->values($clientData);
            $qc->place_id = $place->id;
            $qc->project_id = $place->project_id;
            $qc->object_id = $place->object_id;
            $qc->floor_id = $place->floor_id;
            $qc->place_type = $place->type;
            $qc->save();
            $fs = new FileServer();
            if(!empty($uploadedFiles)){
                foreach ($uploadedFiles as $idx => $image){
                    $image = ORM::factory('Image')->values($image)->save();
                    $qc->add('images', $image->pk());

//                    $img = new JBZoo\Image\Image($project->qualityControlPath().DS.$image->name);
//                    $img->saveAs($project->qualityControlPath().DS.$image->name,50);
                    $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                }
            }
            $imgData = $this->_GNormPArr('images');

            if(!empty($imgData)){
                foreach ($imgData as $img){
                    if(isset($img['name'])){
                        $imgData = Arr::extract($img,['source','name']);
                        $image = $this->saveBase64Image($imgData['source'],$imgData['name'],$qc->project->qualityControlPath());
                        $qc->add('images', $image->pk());
                        $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                    }else{
                        if(!isset($img['id'])) throw  new HTTP_Exception_404;
                        $imgData = Arr::extract($img,['source','id']);
                        $file = ORM::factory('PlanFile',$imgData['id']);
                        if( ! $file->loaded()) throw API_Exception::factory(500,'Incorrect file Identifier');
                        $filename = $file->getName();
                        $tmp = explode('.',$filename);
                        if(count($tmp) > 1){
                            unset($tmp[count($tmp)-1]);
                        }
                        $filename = implode('.',$tmp).'.png';
                        $image = $this->saveBase64Image($imgData['source'],$filename,$qc->project->qualityControlPath());
                        $qc->add('images', $image->pk());
                        $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                    }

                }
            }
            $qc->add('tasks',$clientData['tasks']);
            if(!empty(trim($message)))
                ORM::factory('QcComment')->values(['message' => $message, 'qcontrol_id' => $qc->pk()])->save();
            Database::instance()->commit();

//            if($clientData['status'] != Enum_QualityControlStatus::Existing && $clientData['status'] != Enum_QualityControlStatus::Normal){
//                if($clientData['approval_status'] != Enum_QualityControlApproveStatus::Approved) {
                    $this->sendNotificationToUsers($place->project->users->find_all());
//                }
//            }
            $fs->sendLazyTasks();
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    public function action_update_qcontrol(){
        $qcId = (int)$this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        if( ! $qc->loaded() OR !$this->_user->canUseProject($qc->project)){
            throw new HTTP_Exception_404;
        }
        $clientData = Arr::extract($_POST,
            [
                'approval_status',
                'status',
                'due_date',
                'description',
                'severity_level',
                'condition_list',
                'plan_id',
                'project_stage',
                'craft_id',
                'tasks',
                'profession_id',
                'craft_id',
                'message'
            ]);
        $clientData['plan_id'] *= 1;

        $clientData['tasks'] = array_values($clientData['tasks']);
        $project = $qc->project;
        if(!empty(trim($clientData['description'])))
            $clientData['description'] = '['.date('d/m/Y').'] '.$clientData['description'].PHP_EOL;
        $date = DateTime::createFromFormat('d/m/Y',$clientData['due_date']);
        if($date == null){
            file_put_contents(DOCROOT.'date_err.log',$clientData['due_date']);
            throw API_Exception::factory(500,'Incorrect date format');
        }
        $clientData['due_date'] = $date->getTimestamp();
        $message = $clientData['message'];
//        if($clientData['status'] != Enum_QualityControlStatus::Invalid && $clientData['status'] != Enum_QualityControlStatus::Repaired){
//            $clientData['severity_level']= $clientData['condition_list'] = null;
//        }
        try{
            Database::instance()->begin();
            if(empty($clientData['tasks'])){
                throw API_Exception::factory(500,'Empty Tasks');
            }
            $project->makeProjectPaths();
            $files = $this->_pFArr();
            if(!empty($files) AND !empty($files['images'])){
                foreach ($files['images'] as $key => $image){
                    $uploadedFiles[] = [
                        'name' => str_replace($project->qualityControlPath().DS,'',Upload::save($image,null,$project->qualityControlPath())),
                        'original_name' => $image['name'],
                        'ext' => Model_File::getFileExt($image['name']),
                        'mime' => $image['type'],
                        'path' => str_replace(DOCROOT,'',$project->qualityControlPath()),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                }
            }
            if($qc->userHasExtraPrivileges($this->_user)){
                if($qc->craft_id != (int)$clientData['craft_id']){
                    if(empty($data['craft_id'])){
                        throw new HDVP_Exception('Speciality can not be empty');
                    }
                    $qc->craft_id = (int)$data['craft_id'];
                }
            }
            $qc->approval_status = $clientData['approval_status'];
            $qc->due_date = $clientData['due_date'];
            $qc->status = $clientData['status'];
            $qc->severity_level = $clientData['severity_level'];
            $qc->condition_list = $clientData['condition_list'];
            $qc->description = $clientData['description'];
            $qc->plan_id = $clientData['plan_id'];
            $qc->profession_id = $clientData['profession_id'];
            $qc->project_stage = $clientData['project_stage'];
            $qc->approved_by = Auth::instance()->get_user()->id;
            $qc->approved_at = time();
            $qc->save();
            $fs = new FileServer();
            if(!empty($uploadedFiles)){
                foreach ($uploadedFiles as $idx => $image){
                    $image = ORM::factory('Image')->values($image)->save();
                    $qc->add('images', $image->pk());

//                    $img = new JBZoo\Image\Image($project->qualityControlPath().DS.$image->name);
//                    $img->saveAs($project->qualityControlPath().DS.$image->name,50);
                    $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                }
            }
            $imgData = $this->_GNormPArr('images');
            if(!empty($imgData)){
                foreach ($imgData as $img){
                    if(isset($img['name'])){
                        $imgData = Arr::extract($img,['source','name']);
                        $image = $this->saveBase64Image($imgData['source'],$imgData['name'],$qc->project->qualityControlPath());
                        $qc->add('images', $image->pk());
                        $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                    }else{
                        if(!isset($img['id'])) throw  new HTTP_Exception_404;
                        $imgData = Arr::extract($img,['source','id']);
                        $file = ORM::factory('PlanFile',$imgData['id']);
                        if( ! $file->loaded()) throw API_Exception::factory(500,'Incorrect file Identifier');
                        $filename = $file->getName();
                        $tmp = explode('.',$filename);
                        if(count($tmp) > 1){
                            unset($tmp[count($tmp)-1]);
                        }
                        $filename = implode('.',$tmp).'.png';
                        $image = $this->saveBase64Image($imgData['source'],$filename,$qc->project->qualityControlPath());
                        $qc->add('images', $image->pk());
                        $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                    }

                }
            }
            $qc->remove('tasks');
            $qc->add('tasks',$clientData['tasks']);
            if(!empty(trim($message)))
                ORM::factory('QcComment')->values(['message' => $message, 'qcontrol_id' => $qc->pk()])->save();
            Database::instance()->commit();

//            if($clientData['status'] != Enum_QualityControlStatus::Existing && $clientData['status'] != Enum_QualityControlStatus::Normal){
//                if($clientData['approval_status'] != Enum_QualityControlApproveStatus::Approved) {
                    $this->sendNotificationToUsers($qc->project->users->find_all());
//                }
//            }

            $fs->sendLazyTasks();
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    protected function _GNormPArr($arrKey){
        $output = [];
        foreach ($this->$_POST as $key => $value){
            if(preg_match('~'.$arrKey.'_(?<isNew>\+)?(?<id>[0-9a-z]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $output['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $output[$matches['id']][$matches['field']] = $value;
                }

        }
        return $output;
    }

    public function action_qc_by_statuses()
    {
        $id = (int)$this->request->param('id');
        $statuses = $this->request->query('statuses');
        $approvalStatuses = $this->request->query('approval_statuses');
        $project = ORM::factory('Project',$id);
        if( $id AND (!$project->loaded() OR !$this->_user->canUseProject($project))){
            throw new HTTP_Exception_404;
        }
        $this->_responseData = [];
        $this->_responseData['items'] = [];

        $usrProjects = ORM::factory('UserProjectsRelation')->getProjectIdsForUser($this->_user->id);
        $qc = ORM::factory('QualityControl')
            ->with('project')
            ->where('qualitycontrol.project_id','=', $id);
        if ($statuses AND count($statuses)) {
            $qc = $qc->where('qualitycontrol.status','IN',$statuses);

        }
        if ($approvalStatuses AND count($approvalStatuses)) {
            $qc = $qc->where('qualitycontrol.approval_status','IN',$approvalStatuses);
        }

        $qc = $qc->find_all();

        foreach ($qc as $q){
            $qcIds[] = $q->id;
            $this->_responseData['items'][] = [
                'id' => $q->id,
                'projId' => $q->project_id,
                'cmpId' => $q->project->company_id,
                'objId' => $q->object_id,
                'floorId' => $q->floor_id,
                'placeId' => $q->place_id,
                'spaceId' => $q->space_id,
                'planId' => $q->plan_id,
                'elementId' => Api_DBElApprovals::getElApprovalElementByElAppId($q->el_approval_id)[0]['id'],
                'tasks' => [],
                'files' => $this->getQualityControlImages($q),
                'professionId' => $q->profession_id,
                'craftId' => $q->craft_id,
                'placeType' => $q->place_type,
                'projectStage' => $q->project_stage,
                'severityLevel' => $q->severity_level,
                'conditionList' => $q->condition_list,
                'description' => $q->description,
                'status' => $q->status,
                'dueDate' => $q->due_date,
                'createdAt' => $q->created_at,
                'updatedAt' => $q->updated_at,
                'approvedAt' => $q->approved_at,
                'createdBy' => $q->createUser->email,
                'updatedBy' => $q->updateUser->email,
                'approvedBy' => $q->approveUser->email,
                'approvalStatus' => $q->approval_status,
            ];
        }
        if(count($qcIds)){
            $qcIds = '('.implode(',',$qcIds).')';
            $res = DB::query(Database::SELECT,'SELECT * FROM qcontrol_pr_tasks WHERE qcontrol_id IN '.$qcIds)->execute()->as_array();
            $qcIds = [];
            if(count($res)){
                foreach ($res as $r){
                    $qcIds[$r['qcontrol_id']][] = $r['task_id'];
                }

                for ($i=0; $i < count($this->_responseData['items']); $i++){
                    if(!empty($qcIds[$this->_responseData['items'][$i]['id']])){
                        $this->_responseData['items'][$i]['tasks'] = $qcIds[$this->_responseData['items'][$i]['id']];
                    }
                }


                if($this->_user->getRelevantRole('priority') > Enum_UserPriorityLevel::Corporate){
                    //если компании
                    if($this->_user->getRelevantRole('priority') <= Enum_UserPriorityLevel::Company){
                        foreach($this->_responseData['items'] as $key => $item){
                            if($id AND $item['projId'] != $project->id){
                                unset($this->_responseData['items'][$key]);
                                continue;
                            }
                            if($item['cmpId'] != $this->_user->company_id){
                                if(!in_array($item['projId'],$usrProjects)){
                                    unset($this->_responseData['items'][$key]);
                                }
                            }
                        }
                    }else{//если проект
                        foreach($this->_responseData['items'] as $key => $item){
                            if($id AND $item['projId'] != $project->id){
                                unset($this->_responseData['items'][$key]);
                                continue;
                            }
                            if(!in_array($item['projId'],$usrProjects)){
                                unset($this->_responseData['items'][$key]);
                            }
                        }
                    }

                }elseif ($id){
                    foreach($this->_responseData['items'] as $key => $item){
                        if($item['projId'] != $project->id){
                            unset($this->_responseData['items'][$key]);
                            continue;
                        }
                    }
                }
            }
        }
    }

    public function action_invalid_qc(){
        $id = (int)$this->request->param('id');
        $project = ORM::factory('Project',$id);
        if( $id AND (!$project->loaded() OR !$this->_user->canUseProject($project))){
            throw new HTTP_Exception_404;
        }
        $this->_responseData = [];//$this->_responseData['updated']
        $this->_responseData['items'] = [];
        $companies = $this->_user->availableCompanies();
        $cmpIds = [];
        foreach ($companies as $c){
            $cmpIds[] = $c->id;
        }

        if(count($cmpIds) > 0){
            $cmpIds = '('.implode(',',$cmpIds).')';
        }

        $projects = ORM::factory('Project')->where('id','IN',DB::expr($cmpIds))->find_all();
        $usrProjects = ORM::factory('UserProjectsRelation')->getProjectIdsForUser($this->_user->id);
//        if(count($projects)){
//            foreach ($projects as $p){
//                $prjIds[] = $p->id;
//            }
//            $prjIds = Arr::merge($prjIds,$usrProjects);
//            $prjIds = array_unique($prjIds);
//            $prjIds = '('.implode(',',$prjIds).')';
//        }
        $qc = ORM::factory('QualityControl')
            ->with('project')
//            ->where('qualitycontrol.project_id','IN',DB::expr($prjIds))
            ->where('qualitycontrol.status','=',Enum_QualityControlStatus::Invalid)->find_all();
        $qcIds = [];
        foreach ($qc as $q){
            $qcIds[] = $q->id;
            $this->_responseData['items'][] = [
                'id' => $q->id,
                'projId' => $q->project_id,
                'cmpId' => $q->project->company_id,
                'objId' => $q->object_id,
                'floorId' => $q->floor_id,
                'placeId' => $q->place_id,
                'spaceId' => $q->space_id,
                'planId' => $q->plan_id,
                'tasks' => [],
                'files' => $this->getQualityControlImages($q),
                'professionId' => $q->profession_id,
                'craftId' => $q->craft_id,
                'placeType' => $q->place_type,
                'projectStage' => $q->project_stage,
                'severityLevel' => $q->severity_level,
                'conditionList' => $q->condition_list,
                'description' => $q->description,
                'status' => $q->status,
                'dueDate' => $q->due_date,
                'createdAt' => $q->created_at,
                'updatedAt' => $q->updated_at,
                'approvedAt' => $q->approved_at,
                'createdBy' => $q->createUser->email,
                'updatedBy' => $q->updateUser->email,
                'approvedBy' => $q->approveUser->email,
                'approvalStatus' => $q->approval_status,
            ];
        }
        if(count($qcIds)){
            $qcIds = '('.implode(',',$qcIds).')';
            $res = DB::query(Database::SELECT,'SELECT * FROM qcontrol_pr_tasks WHERE qcontrol_id IN '.$qcIds)->execute()->as_array();
            $qcIds = [];
            if(count($res)){
                foreach ($res as $r){
                    $qcIds[$r['qcontrol_id']][] = $r['task_id'];
                }

                for ($i=0; $i < count($this->_responseData['items']); $i++){
                    if(!empty($qcIds[$this->_responseData['items'][$i]['id']])){
                        $this->_responseData['items'][$i]['tasks'] = $qcIds[$this->_responseData['items'][$i]['id']];
                    }
                }


                if($this->_user->getRelevantRole('priority') > Enum_UserPriorityLevel::Corporate){
                    //если компании
                    if($this->_user->getRelevantRole('priority') <= Enum_UserPriorityLevel::Company){
                        foreach($this->_responseData['items'] as $key => $item){
                            if($id AND $item['projId'] != $project->id){
                                unset($this->_responseData['items'][$key]);
                                continue;
                            }
                            if($item['cmpId'] != $this->_user->company_id){
                                if(!in_array($item['projId'],$usrProjects)){
                                    unset($this->_responseData['items'][$key]);
                                }
                            }
                        }
                    }else{//если проект
                        foreach($this->_responseData['items'] as $key => $item){
                            if($id AND $item['projId'] != $project->id){
                                unset($this->_responseData['items'][$key]);
                                continue;
                            }
                            if(!in_array($item['projId'],$usrProjects)){
                                unset($this->_responseData['items'][$key]);
                            }
                        }
                    }

                }elseif ($id){
                    foreach($this->_responseData['items'] as $key => $item){
                        if($item['projId'] != $project->id){
                            unset($this->_responseData['items'][$key]);
                            continue;
                        }
                    }
                }
            }
        }
    }

    private function getQualityControlImages($qc)
    {
        $output = [];

        foreach($qc->images->where('status','=',Enum_FileStatus::Active)->find_all() as $f){
            $output []= $f->path .'/'. $f->name;
        }
        return $output;

    }

    public function action_create_space(){
        $projectId = (int)$this->request->param('id');
        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded() OR !$this->_user->canUseProject($project)){
            throw new HTTP_Exception_404;
        }
        $data = Arr::extract($_POST,['place_id','desc','type_id']);
        $place = $project->places->where('id','=',$data['place_id'])->find();
        if( ! $place->loaded()){
            throw new HTTP_Exception_404;
        }
        try{
            $space = ORM::factory('PrSpace');
            $space->values($data);
            $space->save();
            $this->_responseData['id'] = $space->pk();
            Database::instance()->commit();
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            //throw API_Exception::factory(500,'Incorrect data');
            throw API_Exception::factory(500,$e->errors('validation'));
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            //throw API_Exception::factory(500,'Incorrect data');
            throw API_Exception::factory(500,$e->getMessage());
        }catch (Exception $e){
            Database::instance()->rollback();
            //throw API_Exception::factory(500,'Operation Error');
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    private function sendNotificationToUsers($usersList) {

        $usersDeviceTokens = [];
//
        foreach ($usersList as $user) {
            if($user->device_token) {
                array_push($usersDeviceTokens, $user->device_token);
            }
        }

//        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$usersDeviceTokens]); echo "</pre>"; exit;
//
//        Kohana::$log->add(Log::ERROR, 'from elApprovals: ' . json_encode([$usersDeviceTokens], JSON_PRETTY_PRINT));
//

        $timestamp = time();

//        $usersDeviceTokens = ['f5bWjICSSMiE40tO7w5RF2:APA91bGGAwSYAYz5t7b1l8jnC385xjLGne5FkWh2LxHQ9W19AflFCnNHsLo8nF1Ydn9_w3dd2a1BmhGFPfLlmGMrWmB0z3k5hQ77bq0zljFxPQAasA9tBjA45rXHb-uXZ6NFgQKklP0i'];

        PushHelper::test([
            'lang' => \Language::getCurrent()->iso2,
            'action' => 'qc',
            'usersDeviceTokens' => $usersDeviceTokens
        ], $timestamp );

    }
}