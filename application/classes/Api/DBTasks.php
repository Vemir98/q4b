<?php

class Api_DBTasks
{
    public static function getTasksByProjectId($id, $moduleId=null, $craftId=null)
    {
        if (!$moduleId) {
            $query = "SELECT
                pt.id as taskId,
                pt.name as taskName,
                pt.status as taskStatus
                FROM pr_tasks pt
                WHERE pt.project_id=:id
                ORDER BY pt.id DESC";

            $query = DB::query(Database::SELECT, $query);
            $query->param(':id', $id);

        } else {
            $query = "SELECT DISTINCT 
                pt.id as taskId, 
                pt.name as taskName, 
                pt.status as taskStatus
                FROM pr_tasks pt 
                LEFT JOIN pr_tasks_crafts tc ON pt.id=tc.task_id
                LEFT JOIN modules_tasks_crafts mtc ON tc.id=mtc.tc_id 
                WHERE pt.project_id=:id";

            if ($craftId) {
                $query .= " AND tc.craft_id=:craftId";
            }
            if ($moduleId) {
                $query .= " AND mtc.module_id=:moduleId";
            }
            $query .= " ORDER BY pt.id DESC";

            $query = DB::query(Database::SELECT, $query);

            $query->param(':id', $id);
            if ($craftId) {
                $query->param(':craftId', $craftId);
            }
            if ($moduleId) {
                $query->param(':moduleId', $moduleId);
            }
        }


        return $query->execute()->as_array();
    }

    public static function getTasksWithCraftByIds($taskIds)
    {
        $query = 'SELECT 
            pt.id, 
            pt.name, 
            pt.status,
            tc.craft_id as craftId,
            cr.name as craftName,
            mtc.module_id as moduleId
            FROM pr_tasks pt 
            INNER JOIN pr_tasks_crafts tc ON pt.id=tc.task_id
            LEFT JOIN cmp_crafts cr ON tc.craft_id=cr.id
            LEFT JOIN modules_tasks_crafts mtc ON tc.id=mtc.tc_id 
            WHERE pt.id IN (:taskIds)';

        $taskIds =  DB::expr(implode(',',$taskIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':taskIds', $taskIds);

        return $query->execute()->as_array();
    }

    public static function getTaskById($taskId)
    {
        $query = "SELECT
            pt.id AS taskId,
            t.name AS taskName,
            pt.status AS taskStatus
            FROM pr_tasks pt
            WHERE pt.id=:taskId";

        return DB::query(Database::SELECT, $query)
            ->bind(':taskId', $taskId)
            ->execute()->as_array();
    }

    public static function getTaskByName($projectId, $name)
    {
        $query = "SELECT
            id,
            name,
            status
            FROM pr_tasks
            WHERE TRIM(`name`)=:taskName AND `project_id`=:projectId";

        $query = DB::query(Database::SELECT, $query);
        $query->param(':taskName', $name);
        $query->param(':projectId', $projectId);

        return $query->execute()->as_array();
    }

    public static function getTaskCrafts($taskIds, $fields=null)
    {
        //[nuynna]
        $query = 'SELECT DISTINCT 
            ptc.craft_id as craftId,
            ptc.task_id as taskId,
            ptc.status,
            cr.name 
            FROM pr_tasks_crafts ptc 
            INNER JOIN pr_tasks pt ON ptc.task_id=pt.id 
            LEFT JOIN cmp_crafts cr ON ptc.craft_id=cr.id
            WHERE pt.status =:status AND ptc.task_id IN (:taskIds)';

        $taskIds =  DB::expr(implode(',', $taskIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':status', "enabled");
        $query->param(':taskIds', $taskIds);

        return $query->execute()->as_array();
    }

    public static function getTaskCraftsModules($taskIds, $fields=null)
    {
        //[nuynna]
        $query = 'SELECT
            ptc.craft_id as craftId,
            ptc.task_id as taskId,
            ptc.status,
            cr.name 
            FROM pr_tasks_crafts ptc 
            INNER JOIN pr_tasks pt ON ptc.task_id=pt.id 
            LEFT JOIN cmp_crafts cr ON ptc.craft_id=cr.id
            WHERE pt.status =:status AND ptc.task_id IN (:taskIds)';

        $taskIds =  DB::expr(implode(',', $taskIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':status', "enabled");
        $query->param(':taskIds', $taskIds);

        return $query->execute()->as_array();
    }

    public static function getTasksByIds($taskIds)
    {
        $query = 'SELECT
            id,
            project_id as projectId,
            name,
            status
            FROM pr_tasks
            WHERE id IN (:taskIds)';

        $taskIds =  DB::expr(implode(',', $taskIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':taskIds', $taskIds);

        return $query->execute()->as_array();
    }

    public static function getTasksCrafts($taskIds)
    {
        $query = 'SELECT
            id,
            task_id as taskId,
            craft_id as craftId,
            status
            FROM pr_tasks_crafts ptc
            WHERE ptc.task_id IN (:taskIds)';

        $taskIds =  DB::expr(implode(',', $taskIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':taskIds', $taskIds);

        return $query->execute()->as_array();
    }

    public static function getTasksModules($taskCraftsIds)
    {
        $query = 'SELECT DISTINCT
            mtc.module_id as moduleId,
            mtc.tc_id as tcId,
            ptc.task_id as taskId,
            ptc.craft_id as craftId
            FROM modules_tasks_crafts mtc
            INNER JOIN pr_tasks_crafts ptc ON ptc.id=mtc.tc_id
            WHERE mtc.tc_id IN (:taskCraftsIds)';

        $taskCraftsIds =  DB::expr(implode(',', $taskCraftsIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':taskCraftsIds', $taskCraftsIds);

        return $query->execute()->as_array();
    }

    public static function getCompanyCraftsByIds($cmpId, $craftIds)
    {
        $query = 'SELECT
            id,
            company_id as companyId,
            trim(name) as name,
            catalog_number as catalogNumber,
            status
            FROM cmp_crafts
            WHERE company_id=:cmpId AND id IN (:craftIds)';

        $craftIds =  DB::expr(implode(',', $craftIds));

        $query = DB::query(Database::SELECT, $query);
        $query->param(':cmpId', $cmpId);
        $query->param(':craftIds', $craftIds);

        return $query->execute()->as_array();
    }
}