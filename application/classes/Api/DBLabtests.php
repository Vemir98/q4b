<?php

class Api_DBLabtests
{
    public static function getLabtestById($id)
    {
        return DB::query(Database::SELECT,'SELECT * FROM labtests WHERE id='.$id)->execute()->as_array();
    }

    public static function getTicketLabtest($id)
    {
        return DB::query(Database::SELECT,'SELECT * FROM labtests_tickets WHERE labtest_id='.$id)->execute()->as_array();
    }

    public static function getLabtestsByElementId($id) {
        return DB::query(Database::SELECT,'SELECT * FROM labtests WHERE element_id='.$id)->execute()->as_array();
    }

    public static function getLabtestTicketsById($id)
    {
        return DB::query(Database::SELECT,'SELECT * FROM labtests_tickets WHERE labtest_id= '.$id.' ORDER BY created_at DESC')->execute()->as_array();
    }
    public static function getLabtestTicket($labtestId, $ticketId)
    {
        return DB::query(Database::SELECT,'SELECT * FROM labtests_tickets WHERE labtest_id='.$labtestId.' AND id='.$ticketId)->execute()->as_array();
    }

    public static function getLabtestsListPaginate($limit, $offset, $params)
    {
        $query = 'SELECT DISTINCT lt.*,
          po.name AS building_name,
          po.smaller_floor AS smaller_floor,
          po.bigger_floor AS bigger_floor,
          el.name AS element_name,
          (SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketNumber,
          fl.custom_name AS floor_custom_name,
          fl.number AS floor_number 
          FROM labtests lt 
          LEFT JOIN (SELECT id, number,labtest_id FROM labtests_tickets) AS lbt ON labtest_id=lt.id
          LEFT JOIN pr_objects po ON lt.building_id=po.id 
          LEFT JOIN elements el ON lt.element_id=el.id 
          LEFT JOIN pr_floors fl ON lt.floor_id=fl.id';

        $query .= ' WHERE lt.project_id='.$params["project_id"];

        if(isset($params['status']) && !empty($params['status'])){
            $status = $params['status'];
            $query .= ' AND lt.status IN ("' . implode('","', $status) . '")';
        }
        if(isset($params['search'])){
            $search = $params['search'];
            $query .= " AND (lt.id='$search' OR lt.cert_number='$search' OR lbt.number='$search')";
        }
        if(isset($params['element_id']) && !empty($params['element_id'])){
            $query .= ' AND element_id IN (' . implode(",", $params["element_id"]) . ')';
        }
        if(isset($params['floor_id']) && !empty($params['floor_id'])){
            $query .= ' AND floor_id IN (' . implode(",", $params["floor_id"]) . ')';
        }
        if(isset($params['place_id']) && !empty($params['place_id'])){
            $query .= ' AND place_id IN (' . implode(",", $params["place_id"]) . ')';
        }
        if(isset($params['building_id']) && !empty($params['building_id'])){
            $query .= ' AND building_id IN (' . implode(",", $params["building_id"]) . ')';
        }
        if(isset($params['craft_id']) && !empty($params['craft_id'])){
            $query .= ' AND craft_id IN (' . implode(",", $params["craft_id"]) . ')';
        }
        if(isset($params['from'])){
            $query .= ' AND lt.create_date>='.$params['from'];
        }
        if(isset($params['to'])){
            $query .= ' AND lt.create_date<='.$params['to'];
        }
        $query .= ' ORDER BY lt.create_date DESC';

        $query .= ' LIMIT '. $limit .' OFFSET ' . $offset;

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getLabtestsListWithRelations($params)
    {

        $query = 'SELECT DISTINCT lt.*,
          u.name AS created_by_name, 
          u.id AS created_user_id,
          u1.id AS updated_user_id,
          u1.name AS updated_by_name,
          po.name AS building_name,
          po.smaller_floor AS smaller_floor,
          po.bigger_floor AS bigger_floor,
          el.name AS element_name,
          cr.name AS craftName,
          fl.custom_name AS floor_custom_name,
          fl.number AS floor_number,
          (SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketNumber,
          (SELECT id FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketId,
          (SELECT fresh_strength FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS freshStrength,
          (SELECT roll_strength FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS rollStrength,
          (SELECT description FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS description,
          (SELECT notes FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS notes,
          (SELECT created_by FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketCreatedBy,
          (SELECT updated_by FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketUpdatedBy,
          fl.custom_name AS floor_custom_name,
          fl.number AS floor_number 
          FROM labtests lt 
          LEFT JOIN (SELECT id, number,labtest_id FROM labtests_tickets) AS lbt ON labtest_id=lt.id
          INNER JOIN users u ON lt.created_by = u.id 
          LEFT JOIN users u1 ON lt.updated_by = u1.id
          LEFT JOIN pr_objects po ON lt.building_id=po.id 
          LEFT JOIN elements el ON lt.element_id=el.id 
          LEFT JOIN cmp_crafts cr ON lt.craft_id=cr.id 
          LEFT JOIN pr_floors fl ON lt.floor_id=fl.id';


        $query .= ' WHERE lt.project_id='.$params["project_id"];
        if(isset($params['status']) && !empty($params['status'])){
            $status = $params['status'];
            $query .= ' AND lt.status IN ("' . implode('","', $status) . '")';
        }
        if(isset($params['search'])){
            $search = $params['search'];
            $query .= " AND (lt.id='$search' OR lt.cert_number='$search' OR lbt.number='$search')";
        }
        if(isset($params['element_id']) && !empty($params['element_id'])){
            $query .= ' AND element_id IN (' . implode(",", $params["element_id"]) . ')';
        }
        if(isset($params['floor_id']) && !empty($params['floor_id'])){
            $query .= ' AND floor_id IN (' . implode(",", $params["floor_id"]) . ')';
        }
        if(isset($params['place_id']) && !empty($params['place_id'])){
            $query .= ' AND place_id IN (' . implode(",", $params["place_id"]) . ')';
        }
        if(isset($params['building_id']) && !empty($params['building_id'])){
            $query .= ' AND building_id IN (' . implode(",", $params["building_id"]) . ')';
        }
        if(isset($params['craft_id']) && !empty($params['craft_id'])){
            $query .= ' AND craft_id IN (' . implode(",", $params["craft_id"]) . ')';
        }
        if(isset($params['from'])){
            $query .= ' AND lt.create_date>='.$params['from'];
        }
        if(isset($params['to'])){
            $query .= ' AND lt.create_date<='.$params['to'];
        }
        $query .= ' ORDER BY lt.create_date DESC';

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getLabTestCraftsWithParams() {
        return DB::query(Database::SELECT,'SELECT 
          cl.id,
          cl.name,
          cl.craft_name AS craftName,
          clp.id AS clpId,
          clp.name AS clpName,
          clp.default_value AS defaultValue,
          clp.value_type AS valueType
        FROM craft_labtest cl 
        INNER JOIN craft_labtest_params clp ON cl.id = clp.cl_id')->execute()->as_array();
    }
    public static function getLabTestCrafts($fields=null) {
        if (!empty($fields)){
            return DB::query(Database::SELECT,'
        SELECT ' . implode(",",$fields) . ' FROM craft_labtest')->execute()->as_array();
        } else {
            return DB::query(Database::SELECT,'
        SELECT * FROM craft_labtest')->execute()->as_array();
        }
    }
    public static function getLabTestCraftParams($id=null, $fields=null) {
        if (!empty($fields)) {
            $query = 'SELECT ' . implode(",",$fields) . ' FROM craft_labtest_params';
        } else {
            $query = 'SELECT * FROM craft_labtest_params';
        }
        if ($id) {
            $query .= ' WHERE cl_id = '.$id;
        }
        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getLabtestWithRelations($id)
    {
        $query = 'SELECT DISTINCT lt.*, po.name AS building_name, 
u.name AS create_user,
u1.name AS update_user,
pl.custom_number AS place_custom_number,
pl.type AS place_type,
po.smaller_floor AS smaller_floor, po.bigger_floor AS bigger_floor, el.name AS element_name, cr.name as craft_name,
(SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticket_number,
(SELECT id AS ticketId FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticket_id,
fl.custom_name AS floor_custom_name, fl.number AS floor_number FROM labtests lt 
LEFT JOIN labtests_tickets lbt ON lt.id=lbt.labtest_id
LEFT JOIN pr_objects po ON lt.building_id=po.id
LEFT JOIN elements el ON lt.element_id=el.id 
LEFT JOIN pr_floors fl ON lt.floor_id=fl.id 
LEFT JOIN cmp_crafts cr ON lt.craft_id=cr.id
LEFT JOIN pr_places pl ON lt.place_id=pl.id
 INNER JOIN users u ON lt.created_by = u.id 
 LEFT JOIN users u1 ON lt.updated_by = u1.id
          WHERE lt.id='.$id;

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getLabtestPlan($id)
    {
        return DB::query(Database::SELECT,'SELECT
          pp.id,
          pp.name,
          f.name AS fileName,
          f.path AS filePath,
          f.original_name AS fileOriginalName,
          fcn.name AS fileCustomName,
          pfa.image AS imagePath,
          pfa.mobile AS mobilePath,
          pp.place_id AS placeId,
          pp.edition,
          pp.scope,
          pp.description,
          pp.object_id AS objectId,
          pp.date,
          pp.created_at AS createdAt,
          u.email AS createdBy,
          pp.profession_id AS professionId,
          u1.email AS updatedBy,
          pp.updated_at AS updatedAt,
          pp.approved_by AS approvedBy,
          pp.approved_at AS approvedAt,
          pp.scale,
          pp.status,
          pp.approval_status AS approvalStatus
        FROM pr_plans pp 
        INNER JOIN users u ON pp.created_by = u.id
        LEFT JOIN users u1 ON pp.updated_by = u1.id
        LEFT JOIN pr_plans_files ppf ON pp.id = ppf.plan_id
        LEFT JOIN files f ON ppf.file_id = f.id
        LEFT JOIN files_custom_names fcn ON fcn.file_id = f.id
        LEFT JOIN pr_plans_file_aliases_files pfa ON f.id = pfa.file_id
        WHERE pp.id='.$id)->execute()->as_array();
    }
    public static function getLabCraftParams($id) {
        return DB::query(Database::SELECT,'SELECT
          lclp.id,
          lclp.cl_id,
          lclp.clp_id,
          lclp.labtest_id,
          lclp.value,
          cl.name AS clName,
          cl.craft_name AS clCraftName,
          clp.name AS clpName,
          clp.default_value AS clpDefaultValue,
          clp.value_type AS clpValueType        
        FROM labtest_clp lclp 
        LEFT JOIN craft_labtest cl ON cl.id = lclp.cl_id
        LEFT JOIN craft_labtest_params clp ON clp.id = lclp.clp_id

        WHERE lclp.labtest_id='.$id)->execute()->as_array();
    }

    public static function getLabtestsClp($labtestIds) {
        return DB::query(Database::SELECT, 'SELECT * FROM labtest_clp WHERE labtest_id IN ('. implode(',', $labtestIds) .')')->execute()->as_array();
    }
}