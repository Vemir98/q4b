<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.09.2017
 * Time: 12:48
 */
class Model_Consultant
{

    public static function getAllUsers(){
        $output = null;
        //$result = DB::query(Database::SELECT,'SELECT DISTINCT id FROM users u INNER JOIN users_projects up on u.id=up.user_id')->execute(null,true);
        $result = DB::query(Database::SELECT,'SELECT DISTINCT u.id FROM users u INNER JOIN roles_users ru ON u.id=ru.user_id INNER JOIN roles r ON ru.role_id = r.id WHERE r.outspread = "'.Enum_UserOutspread::Project.'"')->execute(null,true);
        if($result->count()){
            $uIds = [];
            foreach ($result as $item){
                $uIds[] = $item->id;
            }
            $uIds = '('.implode(',',$uIds).')';
            $output = ORM::factory('User')->where('id','IN',DB::expr($uIds))->find_all();

        }
        return $output;

    }

    public static function getAllUsersForProject($projectID){
        $output = null;
        //$result = DB::query(Database::SELECT,'SELECT DISTINCT id FROM users u INNER JOIN users_projects up on u.id=up.user_id')->execute(null,true);
        $result = DB::query(Database::SELECT,'SELECT DISTINCT u.id FROM users u INNER JOIN roles_users ru ON u.id=ru.user_id INNER JOIN roles r ON ru.role_id = r.id INNER JOIN users_projects up ON u.id = up.user_id WHERE r.outspread = "'.Enum_UserOutspread::Project.'" AND up.project_id = '.$projectID)->execute(null,true);
        if($result->count()){
            $uIds = [];
            foreach ($result as $item){
                $uIds[] = $item->id;
            }
            $uIds = '('.implode(',',$uIds).')';
            $output = ORM::factory('User')->where('id','IN',DB::expr($uIds))->find_all();

        }
        return $output;

    }
}