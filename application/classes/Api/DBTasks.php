<?php

class Api_DBTasks
{
    public static function getProjectTasks($id, $moduleId=null, $craftId=null)
    {
        if (!$moduleId) {
            return DB::query(Database::SELECT,'SELECT pt.id AS taskId, pt.name AS taskName, pt.status AS taskStatus FROM pr_tasks pt WHERE pt.project_id = '.$id)->execute()->as_array();
        } else {
            $query = 'SELECT DISTINCT pt.id AS taskId, pt.name AS taskName, pt.status AS taskStatus FROM pr_tasks pt 
            LEFT JOIN pr_tasks_crafts tc ON pt.id=tc.task_id
            LEFT JOIN modules_tasks_crafts mtc ON tc.id=mtc.tc_id WHERE pt.project_id='.$id;
            if ($craftId) {
                $query .= ' AND tc.craft_id='.$craftId;
            }
            if ($moduleId) {
                $query .= ' AND mtc.module_id='.$moduleId;
            }
        }
        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getProjectTaskById($id)
    {
        return DB::query(Database::SELECT,'SELECT pt.id AS taskId, pt.name AS taskName, pt.status AS taskStatus FROM pr_tasks pt WHERE pt.id='.$id)->execute()->as_array();
    }

    public static function getTaskCrafts($taskIds, $fields=null) {
        if (!empty($fields)){
            return DB::query(Database::SELECT,'SELECT ' . implode(",",$fields) . ' FROM pr_tasks_crafts ptc WHERE ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
        } else {
            return DB::query(Database::SELECT,'SELECT craft_id id, task_id, status FROM pr_tasks_crafts ptc 
            INNER JOIN pr_tasks pt ON ptc.task_id=pt.id 
            LEFT JOIN cmp_crafts cr ON ptc.craft_id=cr.id
            WHERE pt.status = "enabled" AND ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
        }
    }
}