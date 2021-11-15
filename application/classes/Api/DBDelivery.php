<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBDelivery
{
    public static function getProjectsDeliveryPlacesCounts($filters) :array
    {
        $query = 'SELECT
            dr.is_pre_delivery as isPreDelivery,
            COUNT(DISTINCT dr.id) as count
            FROM delivery_reports dr
	        INNER JOIN pr_places pp ON dr.place_id=pp.id 
            WHERE dr.project_id IN (:projectIds) AND (dr.created_at>=:from AND dr.created_at<=:to)
            GROUP BY dr.is_pre_delivery';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
            ':from' => $filters['from'],
            ':to' => $filters['to']
        ));

        return $query->execute()->as_array();
    }
}