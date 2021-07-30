<?php

class Api_DBModules
{
    public static function getModules()
    {
        return DB::query(Database::SELECT,'SELECT * FROM modules')->execute()->as_array();
    }

    public static function getModulesForTasks()
    {
        $moduleIds = implode(',',array(3));
        return DB::query(Database::SELECT,"SELECT * FROM modules WHERE id NOT IN($moduleIds)")->execute()->as_array();
    }
}