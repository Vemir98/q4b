<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBHelper
{
    public static function getCompaniesList($clientId = null){
        return DB::query(Database::SELECT,'SELECT
              companies.id AS id,
              companies.name,
              companies.address,
              companies.description,
              companies.status,
              CONCAT("'.URL::base('https').'",companies.logo) AS logo,
              companies.company_id AS companyId,
              companies.created_at AS createdAt,
              IF(ISNULL(companies.updated_at),companies.created_at,companies.updated_at) AS updatedAt,
              clients.type AS clientType,
              users.email AS createdBy,
              u1.email AS updatedBy
            FROM companies
              INNER JOIN clients
                ON companies.client_id = clients.id
              INNER JOIN users
                ON companies.created_by = users.id
               LEFT JOIN users u1
                ON companies.updated_by = u1.id'.($clientId ? " WHERE companies.client_id=$clientId" : ''))
            ->execute()->as_array();
    }

    public static function getCompanyProfessions($cmpId){
        return DB::query(Database::SELECT,'
SELECT id professionId, company_id cmpId, trim(name) as name, catalog_number catalogNumber, status FROM cmp_professions WHERE company_id='.$cmpId.' ORDER BY name ASC')->execute()->as_array();
    }

    public static function getCompanyCrafts($cmpId){
        return DB::query(Database::SELECT,'
SELECT id craftId, company_id cmpId, trim(name) as name, catalog_number catalogNumber, status FROM cmp_crafts WHERE company_id='.$cmpId.' ORDER BY name ASC')->execute()->as_array();
    }

    public static function getCompanyProfCraftRelation($cmpId)
    {
        return DB::query(Database::SELECT,'
SELECT
  cmp_professions_cmp_crafts.profession_id professionId,
  cmp_professions_cmp_crafts.craft_id craftId
FROM cmp_professions_cmp_crafts
  INNER JOIN cmp_professions
    ON cmp_professions_cmp_crafts.profession_id = cmp_professions.id
  WHERE cmp_professions.company_id = '.$cmpId)->execute()->as_array();
    }

    public static function getProjectsList($clientId = null)
    {
        return DB::query(Database::SELECT,'SELECT projects.id,
  projects.company_id cmpId,
  projects.client_id clientId,
  CONCAT("'.URL::base('https').'",files.path,"/",files.name) AS imgPath,
  projects.name,
  projects.address,
  projects.project_id projectId,
  projects.owner,
  projects.description,
  projects.status,
  projects.start_date startDate,
  projects.end_date endDate,
  projects.created_by createdBy,
  projects.updated_by updatedBy,
  projects.created_at createdAt,
  projects.updated_at updatedAt FROM projects 
  LEFT JOIN files ON projects.image_id = files.id 
  '.($clientId ? 'WHERE client_id='.$clientId : '').'
  ORDER BY projects.name ASC
  ')->execute()->as_array();
    }

    public static function getProjectObjectsList($projectId, $clientId)
    {
        return DB::query(Database::SELECT,'
  SELECT
  po.id,
  po.name,
  po.project_id AS projId,
  pot.name AS type,
  po.smaller_floor AS smallerFloor,
  po.bigger_floor AS biggerFloor,
  po.places_count AS placesCount,
  po.start_date AS startDate,
  po.end_date AS endDate,
  po.created_at AS createdAt,
  po.updated_at AS updatedAt,
  po.state AS state
FROM pr_objects po
 INNER JOIN projects p ON p.id = po.project_id
 INNER JOIN pr_object_types pot ON po.type_id = pot.id
 WHERE p.id = '.$projectId.
($clientId ? ' AND p.client_id='.$clientId : '').' ORDER BY po.name ASC')->execute()->as_array();
    }

    public static function objectIssetForClient($objectId,$clientId){
        $output = false;
        $resp = DB::query(Database::SELECT,'
  SELECT
  COUNT(*) cnt
FROM pr_objects po
  INNER JOIN projects p
    ON po.project_id = p.id
  WHERE po.id = '.$objectId.' 
'.($clientId ? 'AND p.client_id='.$clientId : ''))->execute()->as_array();
        if(isset($resp[0]['cnt'])){
            $output = (bool)(int)$resp[0]['cnt'];
        }
        return $output;
    }

    public static function getObjectFloors($objectId)
    {
        return DB::query(Database::SELECT,'SELECT * FROM pr_floors pf WHERE pf.object_id = '.$objectId)->execute()->as_array();
    }

    public static function getFloorsPlaces(array $floorIds)
    {
        return DB::query(Database::SELECT,'SELECT * FROM pr_places pp WHERE pp.floor_id IN ('.implode(',',$floorIds).')')->execute()->as_array();
    }

    public static function getPlacesSpaces(array $placeIds)
    {
        return DB::query(Database::SELECT,'SELECT ps.id, ps.place_id, ps.desc, pst.name type, pst.id typeId FROM pr_spaces ps INNER JOIN pr_space_types pst ON ps.type_id=pst.id WHERE ps.place_id IN ('.implode(',',$placeIds).')')->execute()->as_array();
    }

    public static function getFloorsPlanIds( array $floorIds){
        return DB::query(Database::SELECT,'SELECT * FROM pr_floors_pr_plans pfpp WHERE pfpp.floor_id IN ('.implode(',',$floorIds).')')->execute()->as_array();
    }

    public static function getPlacesPlanIds( array $placeIds){
        return DB::query(Database::SELECT,'SELECT id, place_id FROM pr_plans pp WHERE pp.place_id IN ('.implode(',',$placeIds).')')->execute()->as_array();
    }

    public static function getProjectTasks($id)
    {
        return DB::query(Database::SELECT,'SELECT id, `name`, status FROM pr_tasks pt WHERE pt.project_id = '.$id)->execute()->as_array();
    }

    public static function getFloorsCrafts($taskIds)
    {
        return DB::query(Database::SELECT,'SELECT craft_id id, task_id, status FROM pr_tasks_crafts ptc WHERE ptc.task_id IN ('.implode(',',$taskIds).')')->execute()->as_array();
    }

    public static function getProjectPlans($id)
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
  cp.status as professionStatus,
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
LEFT JOIN cmp_professions cp ON pp.profession_id = cp.id
LEFT JOIN files f ON ppf.file_id = f.id
LEFT JOIN files_custom_names fcn ON fcn.file_id = f.id
LEFT JOIN pr_plans_file_aliases_files pfa ON f.id = pfa.file_id

WHERE pp.id IN (SELECT max(pp1.id) id FROM pr_plans pp1 WHERE pp1.project_id='.$id.' GROUP BY pp1.scope ORDER BY pp1.id DESC) ')->execute()->as_array();
    }

    public static function getPlanCrafts($planId)
    {
        return DB::query(Database::SELECT,'SELECT craft_id id, plan_id FROM pr_plans_cmp_crafts ppcc WHERE ppcc.plan_id = '.$planId)->execute()->as_array();
    }

    public static function getPlansCrafts($planIds)
    {
        return DB::query(Database::SELECT,'SELECT craft_id id, plan_id FROM pr_plans_cmp_crafts ppcc WHERE ppcc.plan_id IN ('.implode(',',$planIds).')')->execute()->as_array();
    }

    public static function getPlansFloors($planIds)
    {
        return DB::query(Database::SELECT,'SELECT floor_id id, plan_id FROM pr_floors_pr_plans pfpp WHERE pfpp.plan_id IN ('.implode(',',$planIds).')')->execute()->as_array();
    }

    public static function getPlanFloors($planId)
    {
        return DB::query(Database::SELECT,'SELECT floor_id id, plan_id FROM pr_floors_pr_plans pfpp WHERE pfpp.plan_id = '.$planId)->execute()->as_array();
    }

    public static function getProjectUsedTasks($id)
    {
        return DB::query(Database::SELECT,'SELECT
  pr_tasks.id,
  quality_controls.created_at createdAt,
  users.email createdBy
FROM qcontrol_pr_tasks
  INNER JOIN quality_controls
    ON qcontrol_pr_tasks.qcontrol_id = quality_controls.id
  INNER JOIN pr_tasks
    ON qcontrol_pr_tasks.task_id = pr_tasks.id
  INNER JOIN users
    ON quality_controls.created_by = users.id
    WHERE pr_tasks.project_id = '.(int)$id.'
GROUP BY pr_tasks.id
ORDER BY quality_controls.created_at DESC')->execute()->as_array();
    }
    public static function getUserClientIds($userId)
    {
        $output = DB::query(Database::SELECT,'SELECT DISTINCT
  projects.client_id
FROM users_projects
  INNER JOIN projects
    ON users_projects.project_id = projects.id
WHERE users_projects.user_id = '.(int)$userId)->execute()->as_array('client_id');
        $output = array_keys($output);
        return $output;
    }
}