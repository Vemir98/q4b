<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBProjects
{
    public static function getProjectById($id)
    {
        return DB::query(Database::SELECT,'SELECT * FROM projects WHERE id='.$id)->execute()->as_array();
    }
    public static function getAllProjectsList($params)
    {
        $query = 'SELECT projects.id, projects.name FROM projects ';
        if($params['client_id']){
            $query .= ' WHERE client_id in ('.implode(",", $params['client_id']).')';
        }
        if(!empty($params['projectIds'])){
            $query .= ($params['client_id'])?' AND ': ' WHERE ' .'projects.id in ('.implode(",", $params['projectIds']).')';
        }

        $query .= ' ORDER BY projects.name asc';

        return  DB::query(Database::SELECT,$query)->execute()->as_array();
    }
    public static function getProjectsListTotal($params)
    {
        $query = 'SELECT projects.id,
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
              LEFT JOIN files ON projects.image_id = files.id';

        if($params['client_id']){
            $query .= ' WHERE client_id in ('.implode(",", $params['client_id']).')';
        }

        if(!empty($params['projectIds'])){
            $query .= ($params['client_id'])?' AND ': ' WHERE ' .'projects.id in ('.implode(",", $params['projectIds']).')';
        }

        return  DB::query(Database::SELECT,$query)->execute()->as_array();
    }

    public static function getProjectsListPaginate($limit, $offset, $params)
    {

        $query = 'SELECT projects.id,
                projects.company_id cmpId,
                projects.client_id clientId,
                CONCAT("'.URL::base('https').'",files.path,"/",files.name) AS imgPath,
                projects.name,
                projects.image_id,
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
                projects.updated_at updatedAt,
                companies.id as comp_id,
                companies.client_id as comp_client_id,
                companies.name as comp_name,
                companies.address as comp_address,
                companies.description as comp_description,
                companies.status as comp_status,
                companies.logo as comp_logo,
                companies.company_id as comp_company_id,
                companies.created_by as comp_created_by,
                companies.country_id as comp_country_id,
                companies.created_at as comp_created_at,
                companies.updated_by as comp_updated_by,
                companies.updated_at as comp_updated_at 
                FROM projects LEFT JOIN files ON projects.image_id = files.id 
                LEFT JOIN companies ON projects.company_id = companies.id';

        if($params['client_id']){
            $query .= ' WHERE projects.client_id in ('.implode(",", $params['client_id']).')';
        }

        if(!empty($params['projectIds'])){
            $query .= ($params['client_id'])?' AND ': ' WHERE ' .'projects.id in ('.implode(",", $params['projectIds']).')';
        }

        $query .= ' LIMIT '. $limit .' OFFSET ' . $offset;

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
    public static function getProjectElements($id, $search)
    {
        return DB::query(Database::SELECT,"SELECT distinct pe.id, trim(name) as name, `company_id`, pe.project_id,
        (SELECT count(*) FROM labtests WHERE pe.id=labtests.element_id) AS labtestsCount
        FROM elements pe 
        LEFT JOIN labtests ON pe.id=labtests.element_id 
        WHERE pe.project_id = '{$id}' AND `name` LIKE '%{$search}%'
        ORDER BY pe.id DESC")->execute()->as_array();
    }
    public static function getUserProjects($id) {
        return DB::query(Database::SELECT,'SELECT *
        FROM users_projects WHERE user_id= '.(int)$id)->execute()->as_array();
    }
    public static function getProjectImages($projectId) {
        $query = "SELECT 
        *
        FROM projects_images pi
        LEFT JOIN files f ON pi.file_id=f.id
        WHERE pi.project_id={$projectId}";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }
}