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
        $this->_responseData = $this->request->param();
    }

    /**
     * update tasks
     */
    public function action_tasks_put(){
        $this->_responseData = $this->request->param();
    }

    /**
     * returns list of crafts witch bonded to tasks
     * if you pass in get param "fields" property list divided by "," will returned only that property list
     */
    public function action_tasks_crafts_get(){
        $this->_responseData = $this->request->param();
    }

    /**
     * updates task, crafts and modules relation with themself
     */
    public function action_tasks_crafts_put(){
        $this->_responseData = $this->request->param();
    }

    /**
     * copy tasks with relations too another project
     */
    public function action_copy_post(){
        $this->_responseData = $this->request->param();
    }
}