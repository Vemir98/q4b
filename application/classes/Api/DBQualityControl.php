<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 06.09.2021
 * Time: 16:00
 */
class Api_DBQualityControl
{
    public static function getQcById($qcId, $fields)
    {
        if(!empty($fields)) {
            $query = "SELECT
            :fields
            FROM quality_controls qc
            WHERE qc.id=:qcId";

            $exceptions = ['files','tasks'];
            foreach ($fields as $key => $field) {
                if(!in_array($field, $exceptions)) {
                    $fields[$key].= ' as '.Api_DBQualityControl::toCamelCase($field);
                } else {
                    unset($fields[$key]);
                }
            }
            $query = DB::query(Database::SELECT, $query);
            $query->param(':qcId', $qcId);

            $fields = DB::expr(implode(',',$fields));
            $query->param(':fields', $fields);

        } else {
            $query = "SELECT
            qc.id,
            qc.project_id as projectId,
            qc.object_id as objectId,
            qc.floor_id as floorId,
            qc.place_id as placeId,
            qc.space_id as spaceId,
            qc.plan_id as planId,
            qc.profession_id as professionId,
            qc.craft_id as craftId,
            qc.del_rep_id as delRepId,
            qc.el_approval_id as elApprovalId,
            qc.place_type as placeType,
            qc.project_stage as projectStage,
            qc.severity_level as severityLevel,
            qc.status,
            qc.condition_list as conditionList,
            qc.description,
            qc.due_date as dueDate,
            qc.created_at as createdAt,
            qc.updated_at as updatedAt,
            qc.approved_at as approvedAt,
            qc.created_by as createdBy,
            qc.updated_by as updatedBy,
            qc.approved_by as approvedBy,
            qc.approval_status as approvalStatus
            FROM quality_controls qc
            WHERE qc.id=:qcId";

            $query = DB::query(Database::SELECT, $query);
            $query->param(':qcId', $qcId);
        }

        return $query->execute()->as_array();
    }

    public static function getQcImages($qcId)
    {
        $query = "SELECT
            f.name,
            f.path
            FROM quality_controls_files qcf
            LEFT JOIN files f ON qcf.file_id=f.id
            WHERE qcf.qc_id=:qcId";

        return DB::query(Database::SELECT, $query)
            ->bind(':qcId', $qcId)
            ->execute()->as_array();
    }

    public static function getQcTasks($qcId)
    {
        $query = "SELECT
            task_id as taskId,
            qcontrol_id as qcId
            FROM qcontrol_pr_tasks 
            WHERE qcontrol_id=:qcId";

        return DB::query(Database::SELECT, $query)
            ->bind(':qcId', $qcId)
            ->execute()->as_array();
    }

    private static function toCamelCase($string) {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $string))));
    }
}