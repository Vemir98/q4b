<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.06.2017
 * Time: 12:37
 */
class Controller_ProjectsManager extends HDVP_Controller_Template
{
    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE) {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Projects Manager'))->set_url('/projects_manager'));
        }
    }

    public function action_index()
    {
        $this->action_reset_tasks_statuses();
    }

    public function action_reset_tasks_statuses()
    {
        ini_set('max_execution_time', 300);

        if ($this->request->method() == Request::POST) {
            $projectId = Arr::get($this->post(), 'projectId');

            $tasks = ORM::factory('PrTask')
                ->where('project_id', '=', $projectId)
                ->find_all();

            foreach ($tasks as $task) {
                $task->set('status', 'disabled')
                    ->save();
            }

            $this->_setErrors('Project Task Statuses Reseted');
        } else {
            $projects = ORM::factory('Project')->find_all();

            $this->template->content = View::make('projects-manager/reset-tasks')
                ->set('projects', $projects);
        }

    }

    public function action_copy_tasks()
    {
        ini_set('max_execution_time', 300);

        if ($this->request->method() == Request::POST) {
            $fromProjectId = Arr::get($this->post(), 'fromProjectId');
            $toProjectId = Arr::get($this->post(), 'toProjectId');

            $fromProject = ORM::factory('Project', $fromProjectId);
            $fromTasks = $fromProject->tasks
                ->where('status', '=', 'enabled')// enabled Statuses only
                ->find_all();

            try {
                Database::instance()->begin();
                foreach ($fromTasks as $fromTask) {
                    $toProject = ORM::factory('Project', $toProjectId);
                    $toCompanyCrafts = $toProject->company->crafts;
                    $fromCraft = $fromTask->crafts->order_by('id', 'DESC')->find();
                    $toTask = ORM::factory('PrTask');
                    $toTask->project_id = $toProjectId;
                    $toTask->name = $fromTask->name;
                    $toTask->status = $fromTask->status;
                    $toTask->save();
                    $toCompanyCrafts = $toCompanyCrafts->where('name', 'LIKE', '%' . trim($fromCraft->name) . '%')->find();
                    $toCraftRel = ORM::factory('PrCraftRel');
                    $toCraftRel->task_id = $toTask->id;
                    $toCraftRel->craft_id = $toCompanyCrafts->id;
                    $toCraftRel->save();
                }
                Database::instance()->commit();
                $this->_setErrors('Project Task Copied');
            } catch (HDVP_Exception $e) {
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            } catch (ORM_Validation_Exception $e) {
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());

            } catch (Exception $e) {
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }
        }else{
            $projects = ORM::factory('Project')->find_all();

            $this->template->content = View::make('projects-manager/copy-tasks')
                ->set('projects', $projects);
        }
    }
}