<?php defined('SYSPATH') OR die('No direct script access.');


class Controller_Tasks extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'tasks_list' => [
            'GET' => 'read'
        ],
    ];

    public $company, $project;

    public function before()
    {
        parent::before();

    }

    public function action_tasks_list()
    {
        $translations = [
            "lab_control" => __('Lab control'),
            "select_project" => __('Select project'),
            "company" => __('Company'),
            "status" => __('Status'),
            "active" => __('active'),
            "archive" => __('archive'),
            "suspended" => __('suspended'),
            "start_date" => __('Start Date'),
            "end_date" => __('End Date'),
        ];
        VueJs::instance()->addComponent('tasks/tasks-list');
        VueJs::instance()->addComponent('labtests/task-item');
        VueJs::instance()->includeMultiselect();
        $this->template->content = View::make('tasks/tasks-list', ['translations' => $translations]);
    }
}