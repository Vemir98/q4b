<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 06.09.2021
 * Time: 16:00
 */
class Api_DBQualityControl
{
    public static function getQcById($qcId, $fields, $all)
    {
        if(!empty($fields)) {
            $query = "SELECT
            :fields
            FROM quality_controls qc
            WHERE qc.id=:qcId";

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
            ea.element_id as elementId,
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
            (select u.name from users u where u.id=qc.created_by) as createdBy,
            (select u.name from users u where u.id=qc.updated_by) as updatedBy,
            (select u.name from users u where u.id=qc.approved_by) as approvedBy,
            qc.approval_status as approvalStatus
            FROM quality_controls qc
            LEFT JOIN el_approvals ea ON qc.el_approval_id=ea.id
            WHERE qc.id=:qcId";

        }
//        if(!$all) {
//            $query .= ' AND qc.status NOT IN (":statuses")';
//            $query .= ' AND qc.approval_status != :approvalStatus';
//        }

        $query = DB::query(Database::SELECT, $query);
        $query->param(':qcId', $qcId);

        if(!empty($fields)) {

            $exceptions = ['files','tasks'];
            foreach ($fields as $key => $field) {
                $field = Api_DBQualityControl::toCamelCase($field);
                if(!in_array($field, $exceptions)) {
                    $fields[$key].= ' as '.$field;
                    if(in_array($field, ['createdBy', 'updatedBy', 'approvedBy'])) {
                        $fields[$key] = '(select u.name from users u where u.id=qc.'.Arr::decamelize([$field])[0].') as '.$field;
                    }
                } else {
                    unset($fields[$key]);
                }
            }

            $fields = DB::expr(implode(',',$fields));
            $query->param(':fields', $fields);
        }

//        $statuses = DB::expr(implode('","',[
//            Enum_QualityControlStatus::Existing,
//            Enum_QualityControlStatus::Normal
//        ]));
//        $approvalStatus = Enum_QualityControlApproveStatus::Approved;
//        $query->param(':statuses', $statuses);
//        $query->param(':approvalStatus', $approvalStatus);

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