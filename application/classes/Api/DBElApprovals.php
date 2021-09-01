<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 11.08.2021
 * Time: 12:37
 */
class Api_DBElApprovals
{
    public static function getElApprovalById($elApprovalId)
    {
        $query = "SELECT 
            ea.id,
            ea.company_id as companyId,
            ea.project_id as projectId,
            ea.object_id as objectId,
            ea.place_id as placeId,
            ea.element_id as elementId,
            ea.floor_id as floorId,
            ea.appropriate,
            ea.status,
            ea.created_at as createdAt,
            ea.created_by as createdBy,
            ea.updated_at as updatedAt, 
            ea.updated_by as updatedBy,
            o.name as objectName,
            e.name as elementName,
            f.custom_name as floorName,
            f.number as floorNumber,
            p.name as placeName,
            u.name as creatorName,
            o.name as objectName
            FROM el_approvals ea 
            LEFT JOIN elements e ON ea.element_id=e.id
            LEFT JOIN pr_floors f ON ea.floor_id=f.id
            LEFT JOIN users u ON ea.created_by=u.id
            LEFT JOIN pr_places p ON ea.place_id=p.id
            LEFT JOIN pr_objects o ON ea.object_id=o.id
            LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id
            WHERE ea.id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsByElAppId($elApprovalId)
    {
        $query = "SELECT 
            eac.id,
            eac.craft_id as craftId,
            eac.appropriate,
            cc.name as craftName,
            eac.notice,
            u.name as updatorName,
            eac.created_at as createdAt,
            eac.created_by as createdBy,
            eac.updated_at as updatedAt,
            eac.updated_by as updatedBy
            FROM el_approvals_crafts eac
            LEFT JOIN cmp_crafts cc ON eac.craft_id=cc.id
            LEFT JOIN users u ON eac.updated_by=u.id
            WHERE eac.el_app_id={$elApprovalId}
            ORDER BY eac.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftByCraftId($craftId)
    {
        $query = "SELECT 
            eac.id,
            eac.craft_id as craftId,
            eac.appropriate,
            cc.name as craftName,
            eac.notice,
            u.name as updatorName,
            eac.created_at as createdAt,
            eac.created_by as createdBy,
            eac.updated_at as updatedAt,
            eac.updated_by as updatedBy
            FROM el_approvals_crafts eac
            LEFT JOIN cmp_crafts cc ON eac.craft_id=cc.id
            LEFT JOIN users u ON eac.updated_by=u.id
            WHERE eac.id={$craftId}
            ORDER BY eac.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsTasksByCraftIds($elAppCraftId)
    {
        $query = "SELECT 
            eact.id,
            eact.el_app_craft_id as ellAppCraftId,
            eact.task_id as taskId,
            t.name as taskName,
            eact.appropriate
            FROM el_app_crafts_tasks eact
            LEFT JOIN pr_tasks t ON eact.task_id=t.id
            WHERE eact.el_app_craft_id={$elAppCraftId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalUsersListForNotify($elApprovalId)
    {
        $query = "SELECT 
            ean.user_id as userId
            FROM el_approvals_notifications ean
            WHERE ean.ell_app_id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalsList($limit, $offset, $filters, $paginate = false)
    {
        $query = 'SELECT DISTINCT 
            ea.id,
            ea.company_id as companyId,
            ea.project_id as projectId,
            ea.object_id as objectId,
            ea.place_id as placeId,
            ea.element_id as elementId,
            ea.floor_id as floorId,
            ea.appropriate,
            ea.status,
            ea.created_at as createdAt,
            ea.created_by as createdBy,
            ea.updated_at as updatedAt, 
            ea.updated_by as updatedBy,
            o.name as objectName,
            e.name as elementName,
            f.custom_name as floorName,
            f.number as floorNumber,
            p.name as placeName,
            u.name as creatorName,
            o.name as objectName
            FROM el_approvals ea 
            LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
            LEFT JOIN el_app_signatures eas ON eac.id=eas.el_app_craft_id
            LEFT JOIN pr_floors f ON ea.floor_id=f.id
            LEFT JOIN pr_places p ON ea.place_id=p.id
            LEFT JOIN users u ON ea.created_by=u.id
            LEFT JOIN pr_objects o ON ea.object_id=o.id
            LEFT JOIN elements e ON ea.element_id=e.id
            LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id';

        $query .= ' WHERE ea.company_id='.$filters['companyId'].' AND ea.project_id='.$filters['projectId'];
        if(isset($filters['objectIds']) && !empty($filters['objectIds'])){
            $query .= ' AND ea.object_id IN ('.implode(',',$filters['objectIds']).')';
        }
        if(isset($filters['placeIds']) && !empty($filters['placeIds'])){
            $query .= ' AND ea.place_id IN ('.implode(',',$filters['placeIds']).')';
        }
        if(isset($filters['elementIds']) && !empty($filters['elementIds'])){
            $query .= ' AND ea.element_id IN ('.implode(',',$filters['elementIds']).')';
        }
        if(isset($filters['floorIds']) && !empty($filters['floorIds'])){
            $query .= ' AND ea.floor_id IN ('.implode(',',$filters['floorIds']).')';
        }
        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $query .= ' AND eac.craft_id IN ('.implode(',',$filters['specialityIds']).')';
        }
        if(isset($filters['managerStatuses']) && !empty($filters['managerStatuses'])){
            $query .= ' AND ea.status IN ("' . implode('","', $filters['managerStatuses']) . '")';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $query .= ' AND ea.appropriate IN ('.implode(',',$filters['statuses']).')';
        }
        if(isset($filters['positions']) && !empty($filters['positions'])){
            $query .= ' AND eas.position IN ("' . implode('","', $filters['positions']) . '")';
        }
        if(isset($filters['from'])){
            $query .= ' AND ea.created_at>='.$filters['from'];
        }
        if(isset($filters['to'])){
            $query .= ' AND ea.created_at<='.$filters['to'];
        }
        $query .= ' ORDER BY ea.created_at DESC';

        if($paginate) {
            $query .= ' LIMIT '. $limit .' OFFSET ' . $offset;
        }

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsSignaturesByCraftIds($elAppCraftId)
    {
        $query = "SELECT 
            eas.id,
            eas.el_app_id as elAppId,
            eas.el_app_craft_id as elAppCraftId,
            eas.name,
            eas.position,
            eas.image,
            eas.created_at as createdAt,
            eas.created_by as createdBy,
            u.name as creatorName
            FROM el_app_signatures eas
            LEFT JOIN el_approvals_crafts eac ON eas.el_app_craft_id=eac.id
            LEFT JOIN users u ON eas.created_by=u.id
            WHERE eas.el_app_craft_id={$elAppCraftId}
            ORDER BY eas.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsSignaturesPositionsListByProjectId($projectId)
    {
        $query = "SELECT DISTINCT
            eas.id,
            eas.position
            FROM el_app_signatures eas
            LEFT JOIN el_approvals ea ON eas.el_app_id=ea.id
            WHERE ea.project_id={$projectId}
            GROUP BY eas.position
            ORDER BY eas.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalQCListById($elApprovalId)
    {
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
            qc.approval_status as approvalStatus,
            qc.unique_token as uniqueToken
            FROM quality_controls qc
            WHERE qc.el_approval_id={$elApprovalId}
            ORDER BY qc.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalsByUserId($filters, $userId)
    {
        $query = 'SELECT DISTINCT 
            ea.id,
            ea.company_id as companyId,
            ea.project_id as projectId,
            ea.object_id as objectId,
            ea.place_id as placeId,
            ea.element_id as elementId,
            ea.floor_id as floorId,
            ea.appropriate,
            ea.status,
            ea.created_at as createdAt,
            ea.created_by as createdBy,
            ea.updated_at as updatedAt, 
            ea.updated_by as updatedBy,
            o.name as objectName,
            e.name as elementName,
            f.custom_name as floorName,
            f.number as floorNumber,
            p.name as placeName,
            u.name as creatorName,
            o.name as objectName
            FROM el_approvals ea 
            LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
            LEFT JOIN el_app_signatures eas ON eac.id=eas.el_app_craft_id
            LEFT JOIN pr_floors f ON ea.floor_id=f.id
            LEFT JOIN pr_places p ON ea.place_id=p.id
            LEFT JOIN users u ON ea.created_by=u.id
            LEFT JOIN pr_objects o ON ea.object_id=o.id
            LEFT JOIN elements e ON ea.element_id=e.id
            LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id';

        $query .= ' WHERE (ean.user_id='.$userId.' OR ea.created_by='.$userId.')';
        $query .= ' AND ea.company_id='.$filters['company_id'].' AND ea.project_id='.$filters['project_id'];
        if(isset($filters['object_ids']) && !empty($filters['object_ids'])){
            $query .= ' AND ea.object_id IN ('.implode(',',$filters['object_ids']).')';
        }
        if(isset($filters['place_ids']) && !empty($filters['place_ids'])){
            $query .= ' AND ea.place_id IN ('.implode(',',$filters['place_ids']).')';
        }
        if(isset($filters['element_ids']) && !empty($filters['element_ids'])){
            $query .= ' AND ea.element_id IN ('.implode(',',$filters['element_ids']).')';
        }
        if(isset($filters['floor_ids']) && !empty($filters['floor_ids'])){
            $query .= ' AND ea.floor_id IN ('.implode(',',$filters['floor_ids']).')';
        }
        if(isset($filters['speciality_ids']) && !empty($filters['speciality_ids'])){
            $query .= ' AND eac.craft_id IN ('.implode(',',$filters['speciality_ids']).')';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $query .= ' AND ea.status IN ("' . implode('","', $filters['statuses']) . '")';
        }
        if(isset($filters['positions']) && !empty($filters['positions'])){
            $query .= ' AND eas.position IN ("' . implode('","', $filters['positions']) . '")';
        }
        $query .= ' ORDER BY ea.created_at DESC';

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getQualityControlImages($qc_id)
    {
        $query = "SELECT
            qcf.qc_id as qcId,
            f.id,
            f.name,
            f.path,
            f.created_at as createdAt,
            f.created_by as createdBy
            FROM quality_controls_files qcf
            LEFT JOIN files f ON qcf.file_id=f.id
            WHERE qcf.qc_id={$qc_id}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalElementByElAppId($elApprovalId)
    {
        $query = "SELECT 
            e.id,
            e.company_id as companyId,
            e.project_id as projectId
            FROM el_approvals ea 
            LEFT JOIN elements e ON ea.element_id=e.id
            WHERE ea.id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
}