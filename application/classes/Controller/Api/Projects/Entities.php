<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Projects_Entities extends HDVP_Controller_API
{
    /**
    * Get single project
    'existing && for_repair' => 'Ex.For repair',
    'percent'=>'percent',
    'speciality'=>'speciality',
    'average value'=>'average value',
    'structure'=>'structure',
    'No data for show'=>'No data for show',
    'valid_link_date'=>'The link is active until',
    'sort_by_crafts'=>'Sort by speciality',
    'Types of materials'=>'Types of materials',
    'Select all'=>'Select all',
    'Deselect all'=>'Deselect all',
    'New value'=>'New value',
    'Copy to'=>'Copy to',
    'Choose a company'=>'Choose a company',
    'Select project(s)'=>'Select project(s)',
    'Material name'=>'Material name',
    'Transferable items'=>'Transferable items',
    'Item name'=>'Item name',
    'Texts'=>'Texts',
    'New text'=>'New text',
    'Text type'=>'Text type',
    'Reserve Materials'=>'Types of materials',
    'Types of spaces' => 'Types of spaces',
    'New type' => 'New type',
    'Names of spaces' => 'Names of spaces',
    'Power of attorney' => 'Power of attorney',
    'Text before signature' => 'Text before signature',
    'Delivery report' => 'Delivery report',
    'Report ID' => 'Report ID',
    'Customer name' => 'Customer name',
    'Report date' => 'Report date',
    'Delivery form' => 'Delivery form',
    * * if you pass in get param "fields" property list devided by "," will returned only that property list
    * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/entities?fields=name,projectId,typeId...
    * fields must be in camelCase then in code need to convert it to underscore for make request to db
    */
    public function action_project_get() {
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = Arr::decamelize($fields);
        }
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);

        $this->_responseData['item'] = !empty($project) ? $project[0] : [];

    }
    /**
     * Get single labtest
     * * if you pass in get param "fields" property list devided by "," will returned only that property list
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/entities/labtest?fields=name,projectId,typeId...
     * fields must be in camelCase then in code need to convert it to underscore for make request to db
     */
    public function action_labtest_get() {
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = Arr::decamelize($fields);
        }
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $labtest = Api_DBLabtests::getLabtestWithRelations($labtestId);

        $this->_responseData = !empty($labtest) ? $labtest[0] : [];

    }
    public function action_labtest_ticket_get() {
        $ticket = [];
        $ticketId = $this->getUIntParamOrDie($this->request->param('id'));
        $ticket = [];
        if ($ticketId) {
            $data = Api_DBTickets::getTicketById($ticketId);
            if (!empty($data)) {
                $ticket = [
                    'id' => $data[0]['id'],
                    'labtest_id' => $data[0]['labtest_id'],
                    'created_at' => $data[0]['created_at'],
                    'created_by' => $data[0]['created_by'],
                    'updated_at' => $data[0]['updated_at'],
                    'updated_by' => $data[0]['updated_by'],
                    'created_by_name' => $data[0]['created_by_name'],
                    'updated_by_name' => $data[0]['updated_by_name'],
                    'description' => $data[0]['description'],
                    'notes' => $data[0]['notes'],
                    'number' => $data[0]['number'],
                    'roll_strength' => $data[0]['roll_strength'],
                    'fresh_strength' => $data[0]['fresh_strength'],
                    'status' => $data[0]['status'],
                    'images' => []
                ];
                foreach($data as $item) {
                    if ($item['fId']) {
                        $ticket['images'][] = [
                            'id' => $item['fId'],
                            'fileName' => $item['fileName'],
                            'fileOriginalName' => $item['fileOriginalName'],
                            'filePath' => $item['filePath'],
                        ];
                    }
                }
            }
        }
        $this->_responseData = $ticket;
    }
    public function action_labtest_plan_get() {
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $labtest = Api_DBLabtests::getLabtestById($labtestId);
        $data = !empty($labtest) && $labtest[0]['plan_id'] ? Api_DBLabtests::getLabtestPlan($labtest[0]['plan_id']) : [];

        $this->_responseData = !empty($data) ? $data[0] : [];

    }

    /**
     * Get Objects list for project
     * if you pass in get param "fields" property list devided by "," will returned only that property list
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/entities/objects?fields=name,projectId,typeId...
     * fields must be in camelCase then in code need to convert it to underscore for make request to db
     */
    public function action_objects_get(){
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
//            $fields = Arr::decamelize($fields);
        }
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
//        $model = ORM::factory('PrObject');
//        $items = $model->where('project_id','=',$projectId)->find_all();
        $items = Api_DBHelper::getProjectObjectsList($projectId, null);
        $response = [
            'items' => [],
            'count' => count($items)
        ];
        if(!empty($items)){
            foreach ($items as $item){
                if( ! count($fields)){
                    $obj = $item;
                }else{
                    $obj = Arr::extract($item, $fields);
                }
                array_walk($obj,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $obj;
            }
        }
        $this->_responseData = $response;
    }
    /**
     * Get floors list for object
     * if you pass in get param "fields" property list devided by "," will returned only that property list
     * https://qforb.net/api/json/v1/<appToken>projects/entities/objects/<id>/floors?fields=id,projectId...
     * fields must be in camelCase then in code need to convert it to underscore for make request to db
     */
    public function action_floors_get(){
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = array_map(function ($item){
                return Inflector::underscore(Inflector::decamelize($item));
            },$fields);
        }
        $objectId = $this->getUIntParamOrDie($this->request->param('id'));
        $floors = Api_DBHelper::getObjectFloors($objectId);

        $this->_responseData['items'] = $floors;
    }
    /**
     * Get places list for floor
     * if you pass in get param "fields" property list devided by "," will returned only that property list
     * https://qforb.net/api/json/v1/<appToken>projects/entities/floors/<id>/places?fields=id,name,projectId...
     * fields must be in camelCase then in code need to convert it to underscore for make request to db
     */
    public function action_places_get(){
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = array_map(function ($item){
                return Inflector::underscore(Inflector::decamelize($item));
            },$fields);
        }
        $floorId = $this->getUIntParamOrDie($this->request->param('id'));
        $places = Api_DBHelper::getFloorsPlaces([$floorId]);
        $this->_responseData['items'] = $places;

    }
    public function action_labtest_craft_params_get() {
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $labtestCraftParams = Api_DBLabtests::getLabCraftParams($labtestId);
        foreach($labtestCraftParams as &$p) {
            $p['name_en'] = I18n::get($p['clpName'], 'en');
            $p['name_he'] = I18n::get($p['clpName'], 'he');
            $p['name_ru'] = I18n::get($p['clpName'], 'ru');
        }

        $this->_responseData = !empty($labtestCraftParams) ? $labtestCraftParams : [];
    }

    public function action_labtests_projects_get()
    {
        $limit = 12;
        $count = 0;
        $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));

        if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
            if($this->_user->client_id){
                $filterParams['client_id'] = [(int)$this->_user->client_id];
            }
            $count = count(Api_DBProjects::getProjectsListTotal($filterParams));
            $pagination = Pagination::factory(array(
                'total_items'    => $count,
                'items_per_page' => $limit,
            ))
                ->route_params($params);

            $items = Api_DBProjects::getProjectsListPaginate($pagination->items_per_page, $pagination->offset, $filterParams);
        } else {
            if( ! $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Company) AND $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Project)){
                $userProjects = Api_DBProjects::getUserProjects($this->_user->id);
                $projectIds = [];
                if (!empty($userProjects)) {
                    foreach ($userProjects as $userProject) {
                        $projectIds[] = $userProject['project_id'];
                    }
                }
                $filterParams['projectIds'] = $projectIds;
                $count = count(Api_DBProjects::getProjectsListTotal($filterParams));
                $pagination = Pagination::factory(array(
                    'total_items'    => $count,
                    'items_per_page' => $limit,
                ))
                    ->route_params($params);

                $items = Api_DBProjects::getProjectsListPaginate($pagination->items_per_page, $pagination->offset, $filterParams);
            } else {
                $usrClients = Api_DBHelper::getUserClientIds($this->_user->id);
                if($this->_user->client_id){
                    $usrClients[] = $this->_user->client_id;
                }
                $usrClients = array_unique($usrClients);
                $filterParams['client_id'] = $usrClients;
                $count = count(Api_DBProjects::getProjectsListTotal($filterParams));
                $pagination = Pagination::factory(array(
                        'total_items'    => $count,
                        'items_per_page' => $limit,
                    )
                )
                    ->route_params($params);

                $items = Api_DBProjects::getProjectsListPaginate($pagination->items_per_page, $pagination->offset, $filterParams);

            }
        }

        //Убираем кавычки
        array_walk_recursive($items,function (&$value) {
            $value = htmlspecialchars_decode($value);
        });

        $this->_responseData = ['pagination' => ['total' => $count, 'limit' => $limit], 'items' => $items];
    }

    public function action_labtests_all_projects_list_get()
    {
        if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
            if($this->_user->client_id){
                $filterParams['client_id'] = [(int)$this->_user->client_id];
            }
            $items = Api_DBProjects::getAllProjectsList($filterParams);
        }

        else{
            if( ! $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Company) AND $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Project)){
                $userProjects = Api_DBProjects::getUserProjects($this->_user->id);
                $projectIds = [];
                if (!empty($userProjects)) {
                    foreach ($userProjects as $userProject) {
                        $projectIds[] = $userProject['project_id'];
                    }
                }
                $filterParams['projectIds'] = $projectIds;
                $count = count(Api_DBProjects::getProjectsListTotal($filterParams));

                $items = Api_DBProjects::getAllProjectsList($filterParams);
            } else {
                $usrClients = Api_DBHelper::getUserClientIds($this->_user->id);
                if($this->_user->client_id){
                    $usrClients[] = $this->_user->client_id;
                }
                $usrClients = array_unique($usrClients);
                $filterParams['client_id'] = $usrClients;

                $items = Api_DBProjects::getAllProjectsList($filterParams);
            }
        }
        //Убираем кавычки
        array_walk_recursive($items,function (&$value) {
            $value = htmlspecialchars_decode($value);
        });
        $this->_responseData = ['items' => $items];
    }
}