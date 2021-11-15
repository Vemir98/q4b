<?php

class Api_DBLabtests
{
    public static function getLabtestById($labtestId)
    {
        $query = "SELECT
            id,
            project_id as projectId,
            building_id as buildingId,
            floor_id as floorId,
            place_id as placeId,
            craft_id as craftId,
            element_id as elementId,
            plan_id as planId,
            cert_number as certNumber,
            delivery_cert as deliveryCert,
            standard,
            strength_after as strengthAfter,
            created_at as createdAt,
            updated_at as updatedAt,
            created_by as createdBy,
            updated_by as updatedBy,
            create_date as createDate,
            status
            FROM labtests
            WHERE id=:labtestId";

        return DB::query(Database::SELECT, $query)
            ->bind(':labtestId', $labtestId)
            ->execute()->as_array();
    }

    public static function getTicketLabtest($labtestId)
    {
        $query = "SELECT
            id,
            labtest_id as labtestId,
            number,
            fresh_strength as freshStrength,
            roll_strength as rollStrength,
            description,
            notes,
            created_at as createdAt,
            updated_at as updatedAt,
            created_by as createdBy,
            updated_by as updatedBy,
            status
            FROM labtests_tickets
            WHERE labtest_id=:labtestId";

        return DB::query(Database::SELECT, $query)
            ->bind(':labtestId', $labtestId)
            ->execute()->as_array();
    }

    public static function getLabtestsByElementId($elementId)
    {
        $query = "SELECT
            id,
            project_id as projectId,
            building_id as buildingId,
            floor_id as floorId,
            place_id as placeId,
            craft_id as craftId,
            element_id as elementId,
            plan_id as planId,
            cert_number as certNumber,
            delivery_cert as deliveryCert,
            standard,
            strength_after as strengthAfter,
            created_at as createdAt,
            updated_at as updatedAt,
            created_by as createdBy,
            updated_by as updatedBy,
            create_date as createDate,
            status
            FROM labtests
            WHERE element_id=:elementId";

        return DB::query(Database::SELECT, $query)
            ->bind(':elementId', $elementId)
            ->execute()->as_array();
    }

    public static function getLabtestTicketsById($labtestId)
    {
        $query = "SELECT
            id,
            labtest_id as labtestId,
            number,
            fresh_strength as freshStrength,
            roll_strength as rollStrength,
            description,
            notes,
            created_at as createdAt,
            updated_at as updatedAt,
            created_by as createdBy,
            updated_by as updatedBy,
            status
            FROM labtests_tickets
            WHERE labtest_id=:labtestId
            ORDER BY created_at DESC";

        return DB::query(Database::SELECT, $query)
            ->bind(':labtestId', $labtestId)
            ->execute()->as_array();
    }

    public static function getLabtestTicket($labtestId, $ticketId)
    {
        $query = "SELECT
            id,
            labtest_id as labtestId,
            number,
            fresh_strength as freshStrength,
            roll_strength as rollStrength,
            description,
            notes,
            created_at as createdAt,
            updated_at as updatedAt,
            created_by as createdBy,
            updated_by as updatedBy,
            status
            FROM labtests_tickets
            WHERE labtest_id=:labtestId AND id=:ticketId";

        $query = DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':labtestId' => $labtestId,
            ':ticketId' => $ticketId,
        ));

        return $query->execute()->as_array();
    }

    public static function getLabtestsListPaginate($limit, $offset, $params)
    {
        $query = 'SELECT DISTINCT 
            lt.id,
            lt.project_id as projectId,
            lt.building_id as buildingId,
            lt.floor_id as floorId,
            lt.place_id as placeId,
            lt.craft_id as craftId,
            lt.element_id as elementId,
            lt.plan_id as planId,
            lt.cert_number as certNumber,
            lt.delivery_cert as deliveryCert,
            lt.standard,
            lt.strength_after as strengthAfter,
            lt.created_at as createdAt,
            lt.updated_at as updatedAt,
            lt.created_by as createdBy,
            lt.updated_by as updatedBy,
            lt.create_date as createDate,
            lt.status,
            po.name AS buildingName,
            po.smaller_floor AS smallerFloor,
            po.bigger_floor AS biggerFloor,
            el.name AS elementName,
            (SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketNumber,
            fl.custom_name AS floorCustomName,
            fl.number AS floorNumber 
            FROM labtests lt 
            LEFT JOIN (SELECT id, number,labtest_id FROM labtests_tickets) AS lbt ON labtest_id=lt.id
            LEFT JOIN pr_objects po ON lt.building_id=po.id 
            LEFT JOIN elements el ON lt.element_id=el.id 
            LEFT JOIN pr_floors fl ON lt.floor_id=fl.id';

        $query .= ' WHERE lt.project_id=:projectId';

        if(isset($params['status']) && !empty($params['status'])){
            $status = DB::expr(implode('","', $params['status']));
            $query .= ' AND lt.status IN (":status")';
        }
        if(isset($params['search'])){
            $search = trim($params['search']);
            $query .= ' AND (lt.id=:search OR lt.cert_number=:search OR lbt.number=:search)';
        }
        if(isset($params['elementId']) && !empty($params['elementId'])){
            $elementId = DB::expr(implode(",", $params["elementId"]));
            $query .= ' AND element_id IN (:elementId)';
        }
        if(isset($params['floorId']) && !empty($params['floorId'])){
            $floorId = DB::expr(implode(",", $params["floorId"]));
            $query .= ' AND floor_id IN (:floorId)';
        }
        if(isset($params['placeId']) && !empty($params['placeId'])){
            $placeId = DB::expr(implode(",", $params["placeId"]));
            $query .= ' AND place_id IN (:placeId)';
        }
        if(isset($params['buildingId']) && !empty($params['buildingId'])){
            $buildingId = DB::expr(implode(",", $params["buildingId"]));
            $query .= ' AND building_id IN (:buildingId)';
        }
        if(isset($params['craftId']) && !empty($params['craftId'])){
            $craftId = DB::expr(implode(",", $params["craftId"]));
            $query .= ' AND craft_id IN (:craftId)';
        }
        if(isset($params['from'])){
            $from = $params['from'];
            $query .= ' AND lt.create_date>=:from';
        }
        if(isset($params['to'])){
            $to = $params['to'];
            $query .= ' AND lt.create_date<=:to';
        }
        $query .= ' ORDER BY lt.create_date DESC';
        $query .= ' LIMIT :limit OFFSET :offset';

        $query = DB::query(Database::SELECT, $query);

        if(isset($status)) $query->param(':status', $status);
        if(isset($search)) $query->param(':search', $search);
        if(isset($elementId)) $query->param(':elementId', $elementId);
        if(isset($floorId)) $query->param(':floorId', $floorId);
        if(isset($placeId)) $query->param(':placeId', $placeId);
        if(isset($buildingId)) $query->param(':buildingId', $buildingId);
        if(isset($craftId)) $query->param(':craftId', $craftId);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);


        $query->parameters(array(
            ':projectId' => $params['projectId'],
            ':limit' => $limit,
            ':offset' => $offset,
        ));

        return $query->execute()->as_array();
    }

    public static function getLabtestsListCountWithRelations($params)
    {
        $query = 'SELECT
            COUNT(DISTINCT lt.id) as labtestsCount
            FROM labtests lt 
            LEFT JOIN (SELECT id, number,labtest_id FROM labtests_tickets) AS lbt ON labtest_id=lt.id
            INNER JOIN users u ON lt.created_by = u.id 
            LEFT JOIN users u1 ON lt.updated_by = u1.id
            LEFT JOIN pr_objects po ON lt.building_id=po.id 
            LEFT JOIN elements el ON lt.element_id=el.id 
            LEFT JOIN cmp_crafts cr ON lt.craft_id=cr.id 
            LEFT JOIN pr_floors fl ON lt.floor_id=fl.id';

        $query .= ' WHERE lt.project_id=:projectId';

        if(isset($params['status']) && !empty($params['status'])){
            $status = DB::expr(implode('","', $params['status']));
            $query .= ' AND lt.status IN (":status")';
        }
        if(isset($params['search'])){
            $search = trim($params['search']);
            $query .= ' AND (lt.id=:search OR lt.cert_number=:search OR lbt.number=:search)';
        }
        if(isset($params['elementId']) && !empty($params['elementId'])){
            $elementId = DB::expr(implode(",", $params["elementId"]));
            $query .= ' AND element_id IN (:elementId)';
        }
        if(isset($params['floorId']) && !empty($params['floorId'])){
            $floorId = DB::expr(implode(",", $params["floorId"]));
            $query .= ' AND floor_id IN (:floorId)';
        }
        if(isset($params['placeId']) && !empty($params['placeId'])){
            $placeId = DB::expr(implode(",", $params["placeId"]));
            $query .= ' AND place_id IN (:placeId)';
        }
        if(isset($params['buildingId']) && !empty($params['buildingId'])){
            $buildingId = DB::expr(implode(",", $params["buildingId"]));
            $query .= ' AND building_id IN (:buildingId)';
        }
        if(isset($params['craftId']) && !empty($params['craftId'])){
            $craftId = DB::expr(implode(",", $params["craftId"]));
            $query .= ' AND craft_id IN (:craftId)';
        }
        if(isset($params['from'])){
            $from = $params['from'];
            $query .= ' AND lt.create_date>=:from';
        }
        if(isset($params['to'])){
            $to = $params['to'];
            $query .= ' AND lt.create_date<=:to';
        }

        $query .= ' ORDER BY lt.create_date DESC';

        $query = DB::query(Database::SELECT, $query);


        if(isset($status)) $query->param(':status', $status);
        if(isset($search)) $query->param(':search', $search);
        if(isset($elementId)) $query->param(':elementId', $elementId);
        if(isset($floorId)) $query->param(':floorId', $floorId);
        if(isset($placeId)) $query->param(':placeId', $placeId);
        if(isset($buildingId)) $query->param(':buildingId', $buildingId);
        if(isset($craftId)) $query->param(':craftId', $craftId);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);

        $query->param(':projectId', $params['projectId']);

        return $query->execute()->as_array();
    }

    public static function getLabtestsListWithRelations($params)
    {

        $query = 'SELECT DISTINCT
            lt.id,
            lt.project_id as projectId,
            lt.building_id as buildingId,
            lt.floor_id as floorId,
            lt.place_id as placeId,
            lt.craft_id as craftId,
            lt.element_id as elementId,
            lt.plan_id as planId,
            lt.cert_number as certNumber,
            lt.delivery_cert as deliveryCert,
            lt.standard,
            lt.strength_after as strengthAfter,
            lt.created_at as createdAt,
            lt.updated_at as updatedAt,
            lt.created_by as createdBy,
            lt.updated_by as updatedBy,
            lt.create_date as createDate,
            lt.status,
            u.name AS createdByName,
            u.id AS createdUserId,
            u1.id AS updatedUserId,
            u1.name AS updatedByName,
            po.name AS buildingName,
            po.smaller_floor AS smallerFloor,
            po.bigger_floor AS biggerFloor,
            el.name AS elementName,
            cr.name AS craftName,
            fl.custom_name AS floorCustomName,
            fl.number AS floorNumber,
            (SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketNumber,
            (SELECT id FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketId,
            (SELECT fresh_strength FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS freshStrength,
            (SELECT roll_strength FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS rollStrength,
            (SELECT description FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS description,
            (SELECT notes FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS notes,
            (SELECT created_by FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketCreatedBy,
            (SELECT updated_by FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketUpdatedBy,
            fl.custom_name AS floorCustomName,
            fl.number AS floorNumber
            FROM labtests lt
            LEFT JOIN (SELECT id, number,labtest_id FROM labtests_tickets) AS lbt ON labtest_id=lt.id
            INNER JOIN users u ON lt.created_by = u.id
            LEFT JOIN users u1 ON lt.updated_by = u1.id
            LEFT JOIN pr_objects po ON lt.building_id=po.id
            LEFT JOIN elements el ON lt.element_id=el.id
            LEFT JOIN cmp_crafts cr ON lt.craft_id=cr.id
            LEFT JOIN pr_floors fl ON lt.floor_id=fl.id';

        $query .= ' WHERE lt.project_id=:projectId';

        if(isset($params['status']) && !empty($params['status'])){
            $status = DB::expr(implode('","', $params['status']));
            $query .= ' AND lt.status IN (":status")';
        }
        if(isset($params['search'])){
            $search = trim($params['search']);
            $query .= ' AND (lt.id=:search OR lt.cert_number=:search OR lbt.number=:search)';
        }
        if(isset($params['elementId']) && !empty($params['elementId'])){
            $elementId = DB::expr(implode(",", $params["elementId"]));
            $query .= ' AND element_id IN (:elementId)';
        }
        if(isset($params['floorId']) && !empty($params['floorId'])){
            $floorId = DB::expr(implode(",", $params["floorId"]));
            $query .= ' AND floor_id IN (:floorId)';
        }
        if(isset($params['placeId']) && !empty($params['placeId'])){
            $placeId = DB::expr(implode(",", $params["placeId"]));
            $query .= ' AND place_id IN (:placeId)';
        }
        if(isset($params['buildingId']) && !empty($params['buildingId'])){
            $buildingId = DB::expr(implode(",", $params["buildingId"]));
            $query .= ' AND building_id IN (:buildingId)';
        }
        if(isset($params['craftId']) && !empty($params['craftId'])){
            $craftId = DB::expr(implode(",", $params["craftId"]));
            $query .= ' AND craft_id IN (:craftId)';
        }
        if(isset($params['from'])){
            $from = $params['from'];
            $query .= ' AND lt.create_date>=:from';
        }
        if(isset($params['to'])){
            $to = $params['to'];
            $query .= ' AND lt.create_date<=:to';
        }


        $query .= ' ORDER BY lt.create_date DESC';

        $query = DB::query(Database::SELECT, $query);

        if(isset($status)) $query->param(':status', $status);
        if(isset($search)) $query->param(':search', $search);
        if(isset($elementId)) $query->param(':elementId', $elementId);
        if(isset($floorId)) $query->param(':floorId', $floorId);
        if(isset($placeId)) $query->param(':placeId', $placeId);
        if(isset($buildingId)) $query->param(':buildingId', $buildingId);
        if(isset($craftId)) $query->param(':craftId', $craftId);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);

        $query->param(':projectId', $params['projectId']);

        return $query->execute()->as_array();
    }

    public static function getLabTestCraftsWithParams()
    {
        $query = "SELECT 
            cl.id,
            cl.name,
            cl.craft_name AS craftName,
            clp.id AS clpId,
            clp.name AS clpName,
            clp.default_value AS defaultValue,
            clp.value_type AS valueType
            FROM craft_labtest cl 
            INNER JOIN craft_labtest_params clp ON cl.id = clp.cl_id";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getLabTestCrafts($fields=null)
    {
        $query = '';
        foreach ($fields as $key => $field) {
            $fields[$key].= ' as '.Api_DBLabtests::toCamelCase($field);
        }

        if (!empty($fields)){
            $query .= 'SELECT ' . implode(",",$fields) .' FROM craft_labtest';
        } else {
            $query .= 'SELECT 
                id,
                name,
                craft_name as craftName
                FROM craft_labtest';
        }
        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getLabTestCraftParams($id=null, $fields=null)
    {
        $query = '';

        foreach ($fields as $key => $field) {
            $fields[$key].= ' as '.Api_DBLabtests::toCamelCase($field);
        }

        if (!empty($fields)) {
            $query .= 'SELECT '. implode(",",$fields) .' FROM craft_labtest_params';
        } else {
            $query .= 'SELECT
                id,
                cl_id as clId,
                name,
                default_value as defaultValue,
                value_type as valueType
                FROM craft_labtest_params';
        }

        if ($id) {
            $query .= ' WHERE cl_id = :id';
            $query = DB::query(Database::SELECT, $query);
            $query->param(':id', $id);
        } else {
            $query = DB::query(Database::SELECT, $query);
        }

        return $query->execute()->as_array();
    }

    public static function getLabtestWithRelations($id)
    {
        $query = "SELECT DISTINCT
            lt.id,
            lt.project_id as projectId,
            lt.building_id as buildingId,
            lt.floor_id as floorId,
            lt.place_id as placeId,
            lt.craft_id as craftId,
            lt.element_id as elementId,
            lt.plan_id as planId,
            lt.cert_number as certNumber,
            lt.delivery_cert as deliveryCert,
            lt.standard,
            lt.strength_after as strengthAfter,
            lt.created_at as createdAt,
            lt.updated_at as updatedAt,
            lt.created_by as createdBy,
            lt.updated_by as updatedBy,
            lt.create_date as createDate,
            lt.status, 
            po.name AS buildingName,
            u.name AS createUser,
            u1.name AS updateUser,
            pl.custom_number AS placeCustomNumber,
            pl.type AS placeType,
            po.smaller_floor AS smallerFloor,
            po.bigger_floor AS biggerFloor,
            el.name AS elementName,
            cr.name as craftName,
            (SELECT number FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketNumber,
            (SELECT id AS ticketId FROM labtests_tickets WHERE labtests_tickets.labtest_id = lt.id ORDER BY created_at DESC LIMIT 1) AS ticketId,
            fl.custom_name AS floorCustomName,
            fl.number AS floorNumber
            FROM labtests lt 
            LEFT JOIN labtests_tickets lbt ON lt.id=lbt.labtest_id
            LEFT JOIN pr_objects po ON lt.building_id=po.id
            LEFT JOIN elements el ON lt.element_id=el.id 
            LEFT JOIN pr_floors fl ON lt.floor_id=fl.id 
            LEFT JOIN cmp_crafts cr ON lt.craft_id=cr.id
            LEFT JOIN pr_places pl ON lt.place_id=pl.id
            INNER JOIN users u ON lt.created_by = u.id 
            LEFT JOIN users u1 ON lt.updated_by = u1.id
            WHERE lt.id=:id";

        return DB::query(Database::SELECT, $query)
            ->bind(':id', $id)
            ->execute()->as_array();
    }

    public static function getLabtestPlan($id)
    {
        $query = "SELECT
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
            WHERE pp.id=:id";

        return DB::query(Database::SELECT, $query)
            ->bind(':id', $id)
            ->execute()->as_array();
    }

    public static function getLabCraftParams($id)
    {
        $query = "SELECT
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
            WHERE lclp.labtest_id=:id";

        return DB::query(Database::SELECT, $query)
            ->bind(':id', $id)
            ->execute()->as_array();
    }

    public static function getLabtestsClp($labtestIds)
    {
        $query = 'SELECT 
            id,
            labtest_id as labtestId,
            cl_id as clId,
            clp_id as clpId,
            value
            FROM labtest_clp
            WHERE labtest_id IN (:labtestIds)';

        $labtestIds = DB::expr(implode(',',$labtestIds));
        $query =  DB::query(Database::SELECT, $query);
        $query->param(':labtestIds', $labtestIds);

        return $query->execute()->as_array();
    }

    public static function getProjectsLabTestsCountsGroupsByStatus($filters) :array
    {
        $query = 'SELECT 
            lt.status,
            COUNT(DISTINCT lt.id) as count
            FROM labtests lt
            WHERE lt.project_id IN (:projectIds) AND (lt.created_at>=:from AND lt.created_at<=:to)
            GROUP BY lt.status';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
            ':from' => $filters['from'],
            ':to' => $filters['to']
        ));

        return $query->execute()->as_array();
    }

    private static function toCamelCase($string) {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $string))));
    }
}