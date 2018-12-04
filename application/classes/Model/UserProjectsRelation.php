<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 02.11.2018
 * Time: 13:01
 */

class Model_UserProjectsRelation extends ORM
{
    protected $_table_name = 'users_projects';

    public function getProjectIdsForUser($userId){
        $output = [];
        foreach ($this->where('user_id','=',$userId)->find_all() as $up){
            $output []= $up->project_id;
        }
        return $output;
    }
}