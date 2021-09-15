<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBCompanies
{
    public static function getCompanyCrafts($cmpId, $fields=[]){
        return DB::query(Database::SELECT,'
        SELECT ' . implode(",",$fields) . ' FROM cmp_crafts WHERE company_id='.$cmpId.' AND status="'.Enum_Status::Enabled.'" ORDER BY name ASC')->execute()->as_array();
    }
    public static function getCmpProjects($id) {
        return DB::query(Database::SELECT,'
        SELECT * FROM projects WHERE company_id='.$id)->execute()->as_array();
    }
    public static function getCompanyCraftByName($cmpId, $craftName){
        return DB::query(Database::SELECT,"
        SELECT id, status FROM cmp_crafts WHERE TRIM(`name`)='{$craftName}' AND `company_id`={$cmpId}")->execute()->as_array();
    }

    public static function getUserCompaniesByProjects($id) {
        $query = 'SELECT
        c.id AS id,
        c.name,
        c.address,
        c.description,
        c.status,
        CONCAT("'.URL::base('https').'",c.logo) AS logo,
        c.company_id AS companyId,
        c.created_at AS createdAt,
        IF(ISNULL(c.updated_at),c.created_at,c.updated_at) AS updatedAt
        FROM companies c
        LEFT JOIN projects p ON c.id=p.company_id
        LEFT JOIN users_projects up ON up.project_id=p.id
        WHERE up.user_id=:id GROUP BY  c.id';

        return DB::query(Database::SELECT, $query)
            ->bind(':id', $id)
            ->execute()->as_array();
    }

    public static function getProjectCompanyByProjectId($projectId) {
        $query = "SELECT
            company_id
            FROM
            projects u WHERE id={$projectId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
}