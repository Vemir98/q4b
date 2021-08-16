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
        ea.company_id,
        ea.project_id,
        ea.object_id,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        ea.place_id,
        ea.floor_id,
        p.name as place_name,
        ea.element_id,
        ea.created_by,
        u.name as creator_name,
        ea.created_at,
        ea.status
        FROM el_approvals ea 
        LEFT JOIN elements e ON ea.element_id=e.id
        LEFT JOIN pr_floors f ON ea.floor_id=f.id
        LEFT JOIN users u ON ea.created_by=u.id
        LEFT JOIN pr_places p ON ea.place_id=p.id
        LEFT JOIN pr_objects o ON ea.object_id=o.id
        WHERE ea.id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsByElAppId($elApprovalId)
    {
        $query = "SELECT 
        eac.id,
        eac.craft_id,
        cc.name as craft_name,
        eac.notice
        FROM el_approvals_crafts eac
        LEFT JOIN cmp_crafts cc ON eac.craft_id=cc.id
        WHERE eac.el_app_id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsTasksByCraftIds($craft_id)
    {
        $query = "SELECT 
        eact.id,
        eact.el_app_craft_id,
        eact.task_id,
        t.name as task_name,
        eact.appropriate
        FROM el_app_crafts_tasks eact
        LEFT JOIN pr_tasks t ON eact.task_id=t.id
        WHERE eact.el_app_craft_id={$craft_id}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalUsersListForNotify($elApprovalId)
    {
        $query = "SELECT 
        ean.user_id
        FROM el_approvals_notifications ean
        WHERE eact.ell_app_id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalsList($limit, $offset, $filters, $paginate = false)
    {
        $query = 'SELECT DISTINCT 
        ea.id,
        ea.company_id,
        ea.project_id,
        ea.object_id,
        o.name as object_name,
        e.name as element_name,
        f.custom_name as floor_name,
        p.name as place_name,
        ea.place_id,
        ea.floor_id,
        ea.element_id,
        ea.created_by,
        u.name as creator_name,
        ea.created_at,
        ea.status
          FROM el_approvals ea 
          LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
          LEFT JOIN pr_floors f ON ea.floor_id=f.id
          LEFT JOIN pr_places p ON ea.place_id=p.id
          LEFT JOIN users u ON ea.created_by=u.id
          LEFT JOIN pr_objects o ON ea.object_id=o.id
          LEFT JOIN elements e ON ea.element_id=e.id';

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
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $query .= ' AND ea.status IN ('.implode(',',$filters['statuses']).')';
        }
        if(isset($filters['speciality_ids']) && !empty($filters['speciality_ids'])){
            $query .= ' AND eac.craft_id IN ('.implode(',',$filters['speciality_ids']).')';
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
}