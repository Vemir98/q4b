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
        ea.*,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        f.number as floor_number,
        p.name as place_name,
        u.name as creator_name,
        o.name as object_name
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
        eac.craft_id,
        eac.appropriate,
        cc.name as craft_name,
        eac.notice,
        u.name as updator_name,
        eac.created_at,
        eac.created_by,
        eac.updated_at,
        eac.updated_by
        FROM el_approvals_crafts eac
        LEFT JOIN cmp_crafts cc ON eac.craft_id=cc.id
        LEFT JOIN users u ON eac.updated_by=u.id
        WHERE eac.el_app_id={$elApprovalId}
        ORDER BY eac.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftByCraftId($craftId) {
        $query = "SELECT 
        eac.id,
        eac.craft_id,
        eac.appropriate,
        cc.name as craft_name,
        eac.notice,
        u.name as updator_name,
        eac.created_at,
        eac.created_by,
        eac.updated_at,
        eac.updated_by
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
        eact.el_app_craft_id,
        eact.task_id,
        t.name as task_name,
        eact.appropriate
        FROM el_app_crafts_tasks eact
        LEFT JOIN pr_tasks t ON eact.task_id=t.id
        WHERE eact.el_app_craft_id={$elAppCraftId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalUsersListForNotify($elApprovalId)
    {
        $query = "SELECT 
        ean.user_id
        FROM el_approvals_notifications ean
        WHERE ean.ell_app_id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalsList($limit, $offset, $filters, $paginate = false)
    {
        $query = 'SELECT DISTINCT 
        ea.*,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        f.number as floor_number,
        p.name as place_name,
        u.name as creator_name,
        o.name as object_name
          FROM el_approvals ea 
          LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
          LEFT JOIN el_app_signatures eas ON eac.id=eas.el_app_craft_id
          LEFT JOIN pr_floors f ON ea.floor_id=f.id
          LEFT JOIN pr_places p ON ea.place_id=p.id
          LEFT JOIN users u ON ea.created_by=u.id
          LEFT JOIN pr_objects o ON ea.object_id=o.id
          LEFT JOIN elements e ON ea.element_id=e.id
          LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id';

        $query .= ' WHERE ea.company_id='.$filters['company_id'].' AND ea.project_id='.$filters['project_id'];
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
        eas.*,
        u.name as creator_name
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

    public static function getQualityControlsListByElAppId($elApprovalId)
    {
        $query = "SELECT
        *
        FROM quality_controls qc
        WHERE qc.el_approval_id={$elApprovalId}
        ORDER BY qc.created_at DESC";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getUserElementApprovals($filters){

        $query = 'SELECT DISTINCT 
        ea.*,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        f.number as floor_number,
        p.name as place_name,
        u.name as creator_name,
        o.name as object_name
          FROM el_approvals ea 
          LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
          LEFT JOIN el_app_signatures eas ON eac.id=eas.el_app_craft_id
          LEFT JOIN pr_floors f ON ea.floor_id=f.id
          LEFT JOIN pr_places p ON ea.place_id=p.id
          LEFT JOIN users u ON ea.created_by=u.id
          LEFT JOIN pr_objects o ON ea.object_id=o.id
          LEFT JOIN elements e ON ea.element_id=e.id
          LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id';

        $query .= ' WHERE (ean.user_id='.Auth::instance()->get_user()->id.' OR ea.created_by='.Auth::instance()->get_user()->id.')';
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

    public static function getQualityControlImages($qc_id) {
        $query = "SELECT
        *
        FROM quality_controls_files qcf
        LEFT JOIN files f ON qcf.file_id=f.id
        WHERE qcf.qc_id={$qc_id}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalElementByElAppId($elApprovalId) {
        $query = "SELECT 
        ea.*,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        f.number as floor_number,
        p.name as place_name,
        u.name as creator_name,
        o.name as object_name
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
}