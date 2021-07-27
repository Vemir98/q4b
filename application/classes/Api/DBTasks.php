<?php

class Api_DBTasks
{
    public static function getProjectTasks($id, $moduleId=null, $craftId=null)
    {
        if (!$moduleId) {
            return DB::query(Database::SELECT,'SELECT pt.id AS taskId, pt.name AS taskName, pt.status AS taskStatus FROM pr_tasks pt WHERE pt.project_id = '.$id.' ORDER BY pt.id DESC')->execute()->as_array();
        } else {
            $query = 'SELECT DISTINCT 
            pt.id AS taskId, 
            pt.name AS taskName, 
            pt.status AS taskStatus
            FROM pr_tasks pt 
            LEFT JOIN pr_tasks_crafts tc ON pt.id=tc.task_id
            LEFT JOIN modules_tasks_crafts mtc ON tc.id=mtc.tc_id 
            WHERE pt.project_id='.$id;
            if ($craftId) {
                $query .= ' AND tc.craft_id='.$craftId;
            }
            if ($moduleId) {
                $query .= ' AND mtc.module_id='.$moduleId;
            }
            $query .= ' ORDER BY pt.id DESC';
        }
        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getProjectTasksByIds($taskIds)
    {
        $query = 'SELECT 
        pt.id, 
        pt.name, 
        pt.status,
        tc.craft_id,
        cr.name AS craftName,
        mtc.module_id 
        FROM pr_tasks pt 
        INNER JOIN pr_tasks_crafts tc ON pt.id=tc.task_id
        LEFT JOIN cmp_crafts cr ON tc.craft_id=cr.id
        LEFT JOIN modules_tasks_crafts mtc ON tc.id=mtc.tc_id 
        WHERE pt.id IN ('.implode(',',$taskIds).')';

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getProjectTaskById($id)
    {
        return DB::query(Database::SELECT,'SELECT pt.id AS taskId, pt.name AS taskName, pt.status AS taskStatus FROM pr_tasks pt WHERE pt.id='.$id)->execute()->as_array();
    }

    public static function getTaskCrafts($taskIds, $fields=null) {
        return DB::query(Database::SELECT,'SELECT DISTINCT ptc.craft_id, ptc.task_id, ptc.status, cr.name 
        FROM pr_tasks_crafts ptc 
        INNER JOIN pr_tasks pt ON ptc.task_id=pt.id 
        LEFT JOIN cmp_crafts cr ON ptc.craft_id=cr.id
        WHERE pt.status = "enabled" AND ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
    }
    public static function getTaskCraftsModules($taskIds, $fields=null) {
        return DB::query(Database::SELECT,'SELECT ptc.craft_id, ptc.task_id, ptc.status, cr.name 
        FROM pr_tasks_crafts ptc 
        INNER JOIN pr_tasks pt ON ptc.task_id=pt.id 
        LEFT JOIN cmp_crafts cr ON ptc.craft_id=cr.id
        WHERE pt.status = "enabled" AND ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
    }
    public  static  function  getTasksByIds($taskIds) {
        return DB::query(Database::SELECT,'SELECT * FROM pr_tasks WHERE id IN ('.implode(',',$taskIds).')')->execute()->as_array();
    }
    public static function getTasksCrafts($taskIds)
    {

        return DB::query(Database::SELECT,'SELECT ptc.* FROM pr_tasks_crafts ptc WHERE ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
    }
    public static function getTasksModules($taskCraftsIds)
    {
        return DB::query(Database::SELECT,'SELECT DISTINCT mtc.module_id, mtc.tc_id, ptc.task_id, ptc.craft_id FROM modules_tasks_crafts mtc INNER JOIN pr_tasks_crafts ptc ON ptc.id=mtc.tc_id  WHERE mtc.tc_id IN ('.implode(',',$taskCraftsIds).')')->execute()->as_array();
    }
    public static function getCompanyCraftsByIds($cmpId, $craftIds){
        return DB::query(Database::SELECT,'
        SELECT id, company_id, trim(name) as name, catalog_number, status FROM cmp_crafts WHERE company_id='.$cmpId.' AND id IN ('.implode(',',$craftIds).')')->execute()->as_array();
    }
}