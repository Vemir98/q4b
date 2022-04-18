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

    public static function getPlaces($filters) {
        $query = 'SELECT
            pp.id,
            pp.project_id as projectId,
            pp.object_id as objectId,
            pp.floor_id as floorId,
            pp.name,
            pp.icon,
            pp.type,
            pp.number,
            pp.ordering,
            pp.custom_number as customNumber
            FROM pr_places pp
            INNER JOIN pr_floors pf ON pp.floor_id=pf.id
            WHERE pp.project_id =:projectId AND pp.object_id =:objectId AND pf.number IN (:floorNumbers) AND pp.type =:placeType';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectId' => $filters['projectId'],
            ':objectId' => $filters['objectId'],
            ':floorNumbers' => DB::expr(implode(',',$filters['floorNumbers'])),
            ':placeType' => $filters['placeType']
        ));

        return $query->execute()->as_array();
    }
}