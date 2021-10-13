<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 11.08.2021
 * Time: 12:37
 */
class Api_DBElApprovals
{
    public static function getElApprovalById($elApprovalId, $filters = [])
    {
        $query = 'SELECT 
            ea.id,
            ea.company_id as companyId,
            ea.project_id as projectId,
            ea.object_id as objectId,
            ea.place_id as placeId,
            ea.element_id as elementId,
            ea.floor_id as floorId,
            ea.appropriate,
            ea.notice,
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
            WHERE ea.id= :elAppId';

        if(isset($filters['status'])) {
            $query .= ' AND ea.status = :status';
        }

        return DB::query(Database::SELECT, $query)
            ->bind(':elAppId', $elApprovalId)
            ->bind(':status', $filters['status'])
            ->execute()->as_array();
    }

    public static function getElApprovalCraftsByElAppIds($elApprovalIds)
    {
        $query = "SELECT 
            eac.id,
            eac.craft_id as craftId,
            eac.el_app_id as elAppId,
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
            WHERE eac.el_app_id IN (:elAppIds)
            ORDER BY eac.created_at DESC";

        $elApprovalIds = DB::expr(implode(',', $elApprovalIds));
        return DB::query(Database::SELECT, $query)
            ->bind(':elAppIds', $elApprovalIds)
            ->execute()->as_array();
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
            WHERE eac.id=:craftId
            ORDER BY eac.created_at DESC";

        return DB::query(Database::SELECT, $query)
            ->bind('craftId', $craftId)
            ->execute()->as_array();
    }

    public static function getElApprovalCraftsTasksByCraftIds($elAppCraftIds)
    {
        $query = "SELECT 
            eact.id,
            eact.el_app_craft_id as ellAppCraftId,
            eact.task_id as taskId,
            t.name as taskName,
            eact.appropriate
            FROM el_app_crafts_tasks eact
            LEFT JOIN pr_tasks t ON eact.task_id=t.id
            WHERE eact.el_app_craft_id IN (:elAppCraftIds)";

        $elAppCraftIds = DB::expr(implode(',', $elAppCraftIds));
        return DB::query(Database::SELECT, $query)
            ->bind(':elAppCraftIds', $elAppCraftIds)
            ->execute()->as_array();
    }

    public static function getElApprovalUsersListForNotify($elApprovalId)
    {
        $query = "SELECT 
            ean.user_id as userId,
            u.device_token as deviceToken
            FROM el_approvals_notifications ean
            LEFT JOIN users u ON ean.user_id=u.id
            WHERE ean.ell_app_id=:ellAppId";

        return DB::query(Database::SELECT, $query)
            ->bind(':ellAppId', $elApprovalId)
            ->execute()->as_array();
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
            ea.notice,
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

        $query .= ' WHERE ea.company_id=:companyId AND ea.project_id=:projectId';
        if(isset($filters['objectIds']) && !empty($filters['objectIds'])){
            $objectIds = DB::expr(implode(',',$filters['objectIds']));
            $query .= ' AND ea.object_id IN (:objectIds)';
        }
        if(isset($filters['placeIds']) && !empty($filters['placeIds'])){
            $placeIds = DB::expr(implode(',',$filters['placeIds']));
            $query .= ' AND ea.place_id IN (:placeIds)';
        }
        if(isset($filters['elementIds']) && !empty($filters['elementIds'])){
            $elementIds = DB::expr(implode(',',$filters['elementIds']));
            $query .= ' AND ea.element_id IN (:elementIds)';
        }
        if(isset($filters['floorIds']) && !empty($filters['floorIds'])){
            $floorIds = DB::expr(implode(',',$filters['floorIds']));
            $query .= ' AND ea.floor_id IN (:floorIds)';
        }
        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $specialityIds = DB::expr(implode(',',$filters['specialityIds']));
            $query .= ' AND eac.craft_id IN (:specialityIds)';
        }
        if(isset($filters['managerStatuses']) && !empty($filters['managerStatuses'])){
            $managerStatuses = DB::expr(implode('","', $filters['managerStatuses']));
            $query .= ' AND ea.status IN (":managerStatuses")';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $statuses = DB::expr(implode(',',$filters['statuses']));
            $query .= ' AND ea.appropriate IN (:statuses)';
        }
        if(isset($filters['positions']) && !empty($filters['positions'])){
            $positions = DB::expr(implode('","', $filters['positions']));
            $query .= ' AND eas.position IN (":positions")';
        }
        if(isset($filters['from'])){
            $from = $filters['from'];
            $query .= ' AND ea.created_at>=:from';
        }
        if(isset($filters['to'])){
            $to = $filters['to'];
            $query .= ' AND ea.created_at<=:to';
        }
        $query .= ' ORDER BY ea.created_at DESC';

        if($paginate) {
            $query .= ' LIMIT :limit OFFSET :offset';
        }

        $query =  DB::query(Database::SELECT, $query);

        if(isset($objectIds)) $query->param(':objectIds', $objectIds);
        if(isset($placeIds)) $query->param(':placeIds', $placeIds);
        if(isset($elementIds)) $query->param(':elementIds', $elementIds);
        if(isset($floorIds)) $query->param(':floorIds', $floorIds);
        if(isset($specialityIds)) $query->param(':specialityIds', $specialityIds);
        if(isset($managerStatuses)) $query->param(':managerStatuses', $managerStatuses);
        if(isset($statuses)) $query->param(':statuses', $statuses);
        if(isset($positions)) $query->param(':positions', $positions);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);

        if($paginate) {
            $query->parameters(array(
                ':limit' => $limit,
                ':offset' => $offset,
            ));
        }

        $query->parameters(array(
            ':companyId' => $filters['companyId'],
            ':projectId' => $filters['projectId'],
        ));

        return $query->execute()->as_array();
    }

    public static function getElApprovalsListCount($filters)
    {
        $query = 'SELECT 
            COUNT(DISTINCT ea.id) as reportsCount
            FROM el_approvals ea 
            LEFT JOIN el_approvals_crafts eac ON ea.id=eac.el_app_id
            LEFT JOIN el_app_signatures eas ON eac.id=eas.el_app_craft_id
            LEFT JOIN pr_floors f ON ea.floor_id=f.id
            LEFT JOIN pr_places p ON ea.place_id=p.id
            LEFT JOIN users u ON ea.created_by=u.id
            LEFT JOIN pr_objects o ON ea.object_id=o.id
            LEFT JOIN elements e ON ea.element_id=e.id
            LEFT JOIN el_approvals_notifications ean ON ean.ell_app_id=ea.id';

        $query .= ' WHERE ea.company_id=:companyId AND ea.project_id=:projectId';
        if(isset($filters['objectIds']) && !empty($filters['objectIds'])){
            $objectIds = DB::expr(implode(',',$filters['objectIds']));
            $query .= ' AND ea.object_id IN (:objectIds)';
        }
        if(isset($filters['placeIds']) && !empty($filters['placeIds'])){
            $placeIds = DB::expr(implode(',',$filters['placeIds']));
            $query .= ' AND ea.place_id IN (:placeIds)';
        }
        if(isset($filters['elementIds']) && !empty($filters['elementIds'])){
            $elementIds = DB::expr(implode(',',$filters['elementIds']));
            $query .= ' AND ea.element_id IN (:elementIds)';
        }
        if(isset($filters['floorIds']) && !empty($filters['floorIds'])){
            $floorIds = DB::expr(implode(',',$filters['floorIds']));
            $query .= ' AND ea.floor_id IN (:floorIds)';
        }
        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $specialityIds = DB::expr(implode(',',$filters['specialityIds']));
            $query .= ' AND eac.craft_id IN (:specialityIds)';
        }
        if(isset($filters['managerStatuses']) && !empty($filters['managerStatuses'])){
            $managerStatuses = DB::expr(implode('","', $filters['managerStatuses']));
            $query .= ' AND ea.status IN (":managerStatuses")';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $statuses = DB::expr(implode(',',$filters['statuses']));
            $query .= ' AND ea.appropriate IN (:statuses)';
        }
        if(isset($filters['positions']) && !empty($filters['positions'])){
            $positions = DB::expr(implode('","', $filters['positions']));
            $query .= ' AND eas.position IN (":positions")';
        }
        if(isset($filters['from'])){
            $from = $filters['from'];
            $query .= ' AND ea.created_at>='.$filters['from'];
        }
        if(isset($filters['to'])){
            $to = $filters['to'];
            $query .= ' AND ea.created_at<='.$filters['to'];
        }
        $query .= ' ORDER BY ea.created_at DESC';

        $query =  DB::query(Database::SELECT, $query);

        if(isset($objectIds)) $query->param(':objectIds', $objectIds);
        if(isset($placeIds)) $query->param(':placeIds', $placeIds);
        if(isset($elementIds)) $query->param(':elementIds', $elementIds);
        if(isset($floorIds)) $query->param(':floorIds', $floorIds);
        if(isset($specialityIds)) $query->param(':specialityIds', $specialityIds);
        if(isset($managerStatuses)) $query->param(':managerStatuses', $managerStatuses);
        if(isset($statuses)) $query->param(':statuses', $statuses);
        if(isset($positions)) $query->param(':positions', $positions);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);

        $query->parameters(array(
            ':companyId' => $filters['companyId'],
            ':projectId' => $filters['projectId'],
        ));

        return $query->execute()->as_array();
    }

    public static function getElApprovalCraftsSignaturesByCraftIds($elAppCraftIds)
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
            WHERE eas.el_app_craft_id IN (:elAppCraftIds)
            ORDER BY eas.created_at DESC";

        $elAppCraftIds = DB::expr(implode(',', $elAppCraftIds));

        return DB::query(Database::SELECT, $query)
            ->bind(':elAppCraftIds', $elAppCraftIds)
            ->execute()->as_array();
    }

    public static function getElApprovalCraftsSignaturesPositionsListByProjectId($projectId)
    {
        $query = "SELECT DISTINCT
            eas.id,
            eas.position
            FROM el_app_signatures eas
            LEFT JOIN el_approvals ea ON eas.el_app_id=ea.id
            WHERE ea.project_id=:projectId
            GROUP BY eas.position
            ORDER BY eas.created_at DESC";

        return DB::query(Database::SELECT, $query)
            ->bind(':projectId',$projectId)
            ->execute()->as_array();
    }

    public static function getElApprovalQCListByIds($elApprovalIds)
    {
        $query = "SELECT 
            qc.id,
            qc.craft_id as craftId,
            qc.el_approval_id as elApprovalId
            FROM quality_controls qc
            WHERE qc.el_approval_id IN (:elAppIds)";

        $elApprovalIds = DB::expr(implode(',', $elApprovalIds));

        return DB::query(Database::SELECT, $query)
            ->bind(':elAppIds', $elApprovalIds)
            ->execute()->as_array();
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
            ea.notice,
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

        $query .= ' WHERE (ean.user_id=:userId OR ea.created_by=:userId)';
        $query .= ' AND ea.company_id=:companyId AND ea.project_id=:projectId AND ea.status != :elAppStatus ';
        if(isset($filters['objectIds']) && !empty($filters['objectIds'])){
            $objectIds = DB::expr(implode(',',$filters['objectIds']));
            $query .= ' AND ea.object_id IN (:objectIds)';
        }
        if(isset($filters['placeIds']) && !empty($filters['placeIds'])){
            $placeIds = DB::expr(implode(',',$filters['placeIds']));
            $query .= ' AND ea.place_id IN (:placeIds)';
        }
        if(isset($filters['elementIds']) && !empty($filters['elementIds'])){
            $elementIds = DB::expr(implode(',',$filters['elementIds']));
            $query .= ' AND ea.element_id IN (:elementIds)';
        }
        if(isset($filters['floorIds']) && !empty($filters['floorIds'])){
            $floorIds = DB::expr(implode(',',$filters['floorIds']));
            $query .= ' AND ea.floor_id IN (:floorIds)';
        }
        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $specialityIds = DB::expr(implode(',',$filters['specialityIds']));
            $query .= ' AND eac.craft_id IN (:specialityIds)';
        }
        if(isset($filters['managerStatuses']) && !empty($filters['managerStatuses'])){
            $managerStatuses = DB::expr(implode('","', $filters['managerStatuses']));
            $query .= ' AND ea.status IN (":managerStatuses")';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $statuses = DB::expr(implode(',',$filters['statuses']));
            $query .= ' AND ea.appropriate IN (:statuses)';
        }
        if(isset($filters['positions']) && !empty($filters['positions'])){
            $positions = DB::expr(implode('","', $filters['positions']));
            $query .= ' AND eas.position IN (":positions")';
        }
        $query .= ' ORDER BY ea.created_at DESC';

        $query = DB::query(Database::SELECT, $query);

        if(isset($objectIds)) $query->param(':objectIds', $objectIds);
        if(isset($placeIds)) $query->param(':placeIds', $placeIds);
        if(isset($elementIds)) $query->param(':elementIds', $elementIds);
        if(isset($floorIds)) $query->param(':floorIds', $floorIds);
        if(isset($specialityIds)) $query->param(':specialityIds', $specialityIds);
        if(isset($managerStatuses)) $query->param(':managerStatuses', $managerStatuses);
        if(isset($statuses)) $query->param(':statuses', $statuses);
        if(isset($positions)) $query->param(':positions', $positions);

        $query->parameters(array(
            ':companyId' => $filters['companyId'],
            ':projectId' => $filters['projectId'],
            ':userId' => $userId,
            ':elAppStatus' => 'approved'
        ));

        return $query->execute()->as_array();
    }

    public static function getElApprovalElementByElAppId($elApprovalId)
    {
        $query = "SELECT 
            e.id,
            e.company_id as companyId,
            e.project_id as projectId,
            e.name
            FROM el_approvals ea 
            LEFT JOIN elements e ON ea.element_id=e.id
            WHERE ea.id=:elApprovalId";

        return DB::query(Database::SELECT, $query)
            ->bind(':elApprovalId', $elApprovalId)
            ->execute()->as_array();
    }

    public static function getElApprovalManagerSignatureByElAppId($elApprovalId)
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
            WHERE eas.el_app_id=:elAppIds AND ISNULL(eas.el_app_craft_id)
            ORDER BY eas.created_at DESC";

        return DB::query(Database::SELECT, $query)
            ->bind(':elAppIds', $elApprovalId)
            ->execute()->as_array();
    }

}