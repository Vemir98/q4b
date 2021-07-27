<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Projects_Tasks extends HDVP_Controller_API {

    /**
     * returns list of task for current project
     * if you pass in get param "fields" property list divided by "," will returned only that property list
     * if passed params module or speciality the returned list will be filtered by that params
     * url /api/json/v2/<appToken>/projects/<projectId>/tasks(/<id>)(/module/<moduleId>)(/craft/<craftId>)(/page/<page>)
     */
    public function action_tasks_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $taskId = $this->getUIntParamOrDie($this->request->param('id'));
        $moduleId = $this->getUIntParamOrDie($this->request->param('moduleId'));
        $craftId = $this->getUIntParamOrDie($this->request->param('craftId'));
        $project = Api_DBProjects::getProjectById($projectId);
        if(empty($project)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        if ($taskId) {
            $res = Api_DBTasks::getProjectTaskById($taskId);
        } else {
            $res = Api_DBTasks::getProjectTasks($projectId, $moduleId, $craftId);
        }
        $this->_responseData['items'] = $res;
    }

    /**
     * adds new tasks to project
     */
    public function action_tasks_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $tasks = $_POST;
        if(empty($project)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }

        try {
            if (!empty($tasks)) {

                for ($i=count($tasks)-1; $i>=0; $i--) {
                    $t = $tasks[$i];
                    $data = [
                        'project_id' => $projectId,
                        'name' => Arr::get($t,'name'),
                        'status' => Enum_Status::Enabled
                    ];
                    $valid = Validation::factory($data);
                    $valid
                        ->rule('project_id', 'not_empty')
                        ->rule('name', 'not_empty')
                        //проверка на длину и условие
                        ->rule('name', 'max_length', [':value', '1000']);
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    $task = DB::insert('pr_tasks')
                        ->columns(array_keys($data))
                        ->values(array_values($data))
                        ->execute($this->_db);

                    $dataModuleTaskCrafts = [];
                    foreach ($t['crafts'] as $key=>$value) {
                        $dataTaskCrafts = [
                            'task_id' => $task[0],
                            'craft_id' => $key,
                        ];
                        $valid = Validation::factory($dataTaskCrafts);
                        $valid
                            ->rule('task_id', 'not_empty')
                            ->rule('craft_id', 'not_empty');
                        if (!$valid->check()) {
                            throw API_ValidationException::factory(500, 'Incorrect data');
                        }
                        $taskCraft = DB::insert('pr_tasks_crafts')
                            ->columns(array_keys($dataTaskCrafts))
                            ->values(array_values($dataTaskCrafts))
                            ->execute($this->_db);
                        foreach ($value['modules'] as $m) {
                            $dataModuleTaskCrafts[] = [$m, $taskCraft[0]];
                        }
                    }

                    if (!empty($dataModuleTaskCrafts)) {
                        $query = DB::insert('modules_tasks_crafts', array('module_id', 'tc_id'));
                        foreach ($dataModuleTaskCrafts as $val) {
                            $query->values($val);
                        }
                        $query->execute($this->_db);
                    }
                }
            }
//            $tasks = Api_DBTasks::getProjectTasks($projectId);
            $this->_responseData = 'success';
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * update tasks
     */
    public function action_tasks_put(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $tasks = Arr::get($this->put(), 'tasks');
        if(empty($project)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        try {
            if (!empty($tasks)) {
                $taskIds = $taskCraftIds = [];
                foreach($tasks as $task) {
                    $taskIds[] = $task['id'];
                    $data = [
                        'id' => $task['id'],
                        'name' => $task['name'],
                        'status' => $task['status']
                    ];

                    $valid = Validation::factory($data);
                    $valid
                        ->rule('name', 'not_empty')
                        ->rule('status', 'not_empty')
                        //проверка на длину и условие
                        ->rule('name', 'max_length', [':value', '1000']);
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }

                    DB::update('pr_tasks')
                        ->set(["name" => $data['name'], "status" => $data['status']])
                        ->where('id', '=', $data['id'])->execute($this->_db);
                }

                $crafts = Api_DBTasks::getTasksCrafts($taskIds);
                foreach($crafts as $c) {
                    $taskCraftIds[] = $c['id'];
                }
                DB::delete('pr_tasks_crafts')->where('task_id', 'IN', $taskIds)->execute($this->_db);
                DB::delete('modules_tasks_crafts')->where('tc_id', 'IN', $taskCraftIds)->execute($this->_db);
                for ($i=count($tasks)-1; $i>=0; $i--) {
                    $t = $tasks[$i];
                    $dataModuleTaskCrafts = [];
                    foreach ($t['crafts'] as $key=>$value) {
                        $dataTaskCrafts = [
                            'task_id' => $t['id'],
                            'craft_id' => $key,
                        ];
                        $valid = Validation::factory($dataTaskCrafts);
                        $valid
                            ->rule('task_id', 'not_empty')
                            ->rule('craft_id', 'not_empty');
                        if (!$valid->check()) {
                            throw API_ValidationException::factory(500, 'Incorrect data');
                        }
                        $taskCraft = DB::insert('pr_tasks_crafts')
                            ->columns(array_keys($dataTaskCrafts))
                            ->values(array_values($dataTaskCrafts))
                            ->execute($this->_db);
                        foreach ($value['modules'] as $m) {
                            $dataModuleTaskCrafts[] = [$m, $taskCraft[0]];
                        }
                    }

                    if (!empty($dataModuleTaskCrafts)) {
                        $query = DB::insert('modules_tasks_crafts', array('module_id', 'tc_id'));
                        foreach ($dataModuleTaskCrafts as $val) {
                            $query->values($val);
                        }
                        $query->execute($this->_db);
                    }
                }

            }
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns list of crafts witch bonded to tasks
     * if you pass in get param "fields" property list divided by "," will returned only that property list
     */
    public function action_tasks_crafts_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = Arr::decamelize($fields);
        }
        $tasks = Api_DBTasks::getProjectTasks($projectId);
        $items = $taskIds = [];
        if(count($tasks)){
            foreach ($tasks as $t){
                $taskIds[] = $t['id'];
            }
           if (!empty($taskIds)) {
               $items = Api_DBTasks::getTaskCrafts($taskIds, $fields);
           }
        }

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
     * updates task, crafts and modules relation with themself
     */
    public function action_tasks_crafts_put(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $tasks = Arr::get($this->put(), 'tasks');

        if(empty($project)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        try {
            if (!empty($tasks)) {
                $taskData = [];

                foreach($tasks as $task) {
                    DB::delete('pr_tasks_crafts')->where('task_id', '=', $task['id'])->execute($this->_db);
                    $d = [
                        'task_id' => $task['id'],
                        'craft_id' => $task['craft_id'],
                        'status' => $task['status']
                    ];

                    $valid = Validation::factory($d);
                    $valid
                        ->rule('task_id', 'not_empty')
                        ->rule('craft_id', 'not_empty')
                        ->rule('status', 'not_empty');
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    $taskData[] = $d;
                }
                if (!empty($taskData)) {

                    $query = DB::insert('pr_tasks_crafts')->columns(['task_id', 'craft_id', 'status']);
                    foreach ($taskData as $d) {
                        $query->values(array_values($d));
                    }
                    $query->execute($this->_db);
                }
            }
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    public function action_test_get() {
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = ORM::factory('Project',$projectId);
        $modTasks = $project->getTasksByModuleName('Quality Control');
    }

    /**
     * copy tasks with relations to another project
     */
    public function action_copy_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $data = $_POST;
        $taskIds = $data['ids'];
        $projectToId = $data['projectId'];
        $projectTo = Api_DBProjects::getProjectById($projectToId);
        if(empty($project) || empty($projectTo)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }

        if (!empty($taskIds)) {
            try {
                $tasksToCopy = Api_DBTasks::getProjectTasksByIds($taskIds);
                if(count($tasksToCopy)){
                    $taskItems = $taskCraftsIds = [];
                    foreach ($tasksToCopy as $task){

                        if (!isset($taskItems[$task['id']])) {
                            $taskItems[$task['id']] = [
                                'id' => $task['id'],
                                'name' => ltrim(rtrim($task['name'])),
                                'status' => $task['status'],
                            ];
                            $taskItems[$task['id']]['crafts'] = [];
                            $taskItems[$task['id']]['crafts'][$task['craft_id']] = [
                                'modules' => [$task['module_id']],
                                'craft_name' => $task['craftName']
                            ];
                        } else {
                            if (!isset($taskItems[$task['id']]['crafts'])) {
                                $taskItems[$task['id']]['crafts'] = [];
                                $taskItems[$task['id']]['crafts'][$task['craft_id']] = [
                                    'modules' => [$task['module_id']],
                                    'craft_name' => $task['craftName']
                                ];
                            } else {
                                if (!isset($taskItems[$task['id']]['crafts'][$task['craft_id']])) {
                                    $taskItems[$task['id']]['crafts'][$task['craft_id']]['modules'] = [$task['module_id']];
                                    $taskItems[$task['id']]['crafts'][$task['craft_id']]['craft_name'] = $task['craftName'];

                                } else {
                                    $taskItems[$task['id']]['crafts'][$task['craft_id']]['modules'][] = $task['module_id'];
                                }
                            }
                        }
                    }
                }
                foreach ($taskItems as $task) {
                    $task['project_id'] = $projectToId;
                    $modulesDataToInsert = [];
                    $taskExist = Api_DBTasks::getProjectTaskByName($projectToId, $task['name']);
                    if (!empty($taskExist)) {
                        foreach($taskExist as $t) {
                            DB::update('pr_tasks')
                                ->set(["status" => Enum_Status::Disabled])
                                ->where('id', '=', $t['id'])->execute($this->_db);
                        }
                    }
                    $newTask = DB::insert('pr_tasks')
                        ->columns(['project_id', 'name', 'status'])
                        ->values([$task['project_id'], $task['name'], Enum_Status::Enabled])
                        ->execute($this->_db);
                    foreach ($task['crafts'] as $key => $c) {
                        $craft = Api_DBCompanies::getCompanyCraftByName($projectTo[0]['company_id'], trim($c['craft_name']));

                        if (empty($craft) || $craft[0]['status'] === Enum_Status::Disabled ) {
                            $newCraft = DB::insert('cmp_crafts')
                                ->columns(['company_id', 'name', 'status'])
                                ->values([$projectTo[0]['company_id'], $c['craft_name'], Enum_Status::Enabled])
                                ->execute($this->_db);
                            $newTaskCraft = DB::insert('pr_tasks_crafts')
                                ->columns(['task_id', 'craft_id'])
                                ->values([$newTask[0], $newCraft[0]])
                                ->execute($this->_db);
                            foreach ($c['modules'] as $mId) {
                                $modulesDataToInsert[] = [
                                    'module_id' => $mId,
                                    'tc_id' => $newTaskCraft[0]
                                ];
                            }
                        } else {

                            $newTaskCraft = DB::insert('pr_tasks_crafts')
                                ->columns(['task_id', 'craft_id'])
                                ->values([$newTask[0], $craft[0]['id']])
                                ->execute($this->_db);
                            foreach ($c['modules'] as $mId) {

                                $modulesDataToInsert[] = [
                                    'module_id' => $mId,
                                    'tc_id' => $newTaskCraft[0]
                                ];
                            }
                        }

                        if (!empty($modulesDataToInsert)) {
                            $query = DB::insert('modules_tasks_crafts', array('module_id', 'tc_id'));
                            foreach ($modulesDataToInsert as $val) {
                                $query->values([$val['module_id'], $val['tc_id']]);
                            }
                            $query->execute($this->_db);
                        }
                    }
                }
            } catch (Exception $e){
                Database::instance()->rollback();
                throw API_Exception::factory(500,'Operation Error');
            }
        }
        $this->_responseData = $this->request->param();
    }

    public function action_list_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $taskId = $this->getUIntParamOrDie($this->request->param('id'));
        $moduleId = $this->getUIntParamOrDie($this->request->param('moduleId'));
        $craftId = $this->getUIntParamOrDie($this->request->param('craftId'));
        $project = Api_DBProjects::getProjectById($projectId);
        if(empty($project)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        if ($taskId) {
            $res = Api_DBTasks::getProjectTaskById($taskId);
        } else {
            $res = Api_DBTasks::getProjectTasks($projectId, $moduleId, $craftId);
        }
        $taskItems = $taskIds = $taskCraftsIds = [];
        if(count($res)){
            foreach ($res as $task){
                $taskItems[$task['taskId']] = [
                    'id' => $task['taskId'],
                    'name' => ltrim(rtrim($task['taskName'])),
                    'status' => $task['taskStatus'],
                ];
                $taskIds[] = $task['taskId'];
            }
            $crafts = Api_DBTasks::getTasksCrafts($taskIds);
            if(count($crafts)){
                foreach ($crafts as $craft){
                    $taskItems[$craft['task_id']]['crafts'][$craft['craft_id']]['modules'] = [];
                    $taskCraftsIds[] = $craft['id'];
                }
            }
            $modules = [];
            if (count($taskCraftsIds)) {
                $modules = Api_DBTasks::getTasksModules($taskCraftsIds);
            }
            if(count($modules)){
                foreach ($modules as $module){
                    array_push($taskItems[$module['task_id']]['crafts'][$module['craft_id']]['modules'], $module['module_id']);
                }
            }
        }
        $this->_responseData['items'] = $taskItems;
    }
}