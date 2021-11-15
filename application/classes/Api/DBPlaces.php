<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBPlaces
{
    public static function getProjectsPlacesCounts($filters) : array
    {
        $query = 'SELECT
            COUNT(DISTINCT pp.id) as count
            FROM pr_places pp
            WHERE pp.project_id IN (:projectIds)';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds']))
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectsPlacesCountsByType($filters) : array
    {
        $query = 'SELECT
            pp.type,
            COUNT(DISTINCT pp.id) as count
            FROM pr_places pp
            WHERE pp.project_id IN (:projectIds)
            GROUP BY pp.type';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds']))
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectsPlacesCountsWithQcByType($filters) :array
    {
        $query = 'SELECT
            pp.type,
            COUNT(DISTINCT pp.id) as count
            FROM pr_places pp
	        INNER JOIN quality_controls qc ON pp.id=qc.place_id 
            WHERE pp.project_id IN (:projectIds) AND (qc.created_at>=:from AND qc.created_at<=:to)
            GROUP BY pp.type';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
            ':from' => $filters['from'],
            ':to' => $filters['to']
        ));

        return $query->execute()->as_array();
    }
}