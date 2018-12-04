<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.06.2017
 * Time: 9:58
 */
class Usr
{
    const CREATE_PERM = 'create';
    const READ_PERM = 'read';
    const UPDATE_PERM = 'update';
    const DELETE_PERM = 'delete';
    const TASKS_PERM = 'tasks';

    const DASHBOARD_RES = 'Controller_Dashboard';
    const COMPANIES_RES = 'Controller_Companies';
    const PROJECTS_RES = 'Controller_Projects';
    const REPORTS_RES = 'Controller_Reports';
    /**
     * Проверяет права пользователя относительно текущего контроллера
     * @return bool
     * @throws Exception
     */
    public static function can(){
        $arg = func_get_args();
        $output = false;
        $user = Auth::instance()->get_user();
        switch (func_num_args()){
            case 1: $output = $user->can($arg[0], 'Controller_'.Request::current()->controller());break;
            case 2: $output = $user->can($arg[0],$arg[1]);break;
            case 3: $output = $user->can($arg[0],$arg[1]) && $user->priorityLevelIn($arg[2]);break;
            default: throw new Exception('Incorrect Arguments');
        }
        return $output;
    }

    public static function role(){
        return Auth::instance()->get_user()->getRelevantRole('name');
    }

    public static function agreed_terms(){
        return (bool)Auth::instance()->get_user()->terms_agreed;
    }
}