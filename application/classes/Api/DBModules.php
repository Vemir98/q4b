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

    public static function getModulesGuides($guideType) : array
    {
        $query = 'SELECT
            g.id,
            g.type,
            g.title,
            g.description,
            g.ordering,
            f.id as fileId,
            f.original_name as fileName,
            f.path as filePath,
            mg.module_id as moduleId
            FROM guides g
            LEFT JOIN modules_guides mg on g.id = mg.guide_id
            LEFT JOIN files f on f.id = g.file_id
            WHERE g.type=:guideType';

        $query =  DB::query(Database::SELECT, $query);

        return DB::query(Database::SELECT, $query)
            ->bind(':guideType', $guideType)
            ->execute()->as_array();
    }
}