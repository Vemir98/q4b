<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
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
        ea.place_id,
        ea.element_id,
        ea.created_by,
        ea.status
        FROM el_approvals ea 
        WHERE ea.id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsByElAppId($elApprovalId)
    {
        $query = "SELECT 
        eac.id,
        eac.craft_id,
        eac.notice
        FROM el_approvals_crafts eac
        WHERE eac.el_app_id={$elApprovalId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getElApprovalCraftsTasksByCraftIds($craft_id)
    {
        $query = "SELECT 
        eact.id,
        eact.el_app_craft_id,
        eact.task_id,
        eact.appropriate
        FROM el_app_crafts_tasks eact
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
        ea.place_id,
        ea.element_id,
        ea.created_by,
        ea.status
          FROM el_approvals ea 
          LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id';

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