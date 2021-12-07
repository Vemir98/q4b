<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBDelivery
{

    public static function getProjectsDeliveryPlacesCountsByType($filters, $type) :array
    {
        $query = 'SELECT
            dr.is_pre_delivery as isPreDelivery,
            COUNT(DISTINCT dr.id) as count
            FROM delivery_reports dr
	        INNER JOIN pr_places pp ON dr.place_id=pp.id 
            WHERE dr.project_id IN (:projectIds) AND (dr.created_at>=:from AND dr.created_at<=:to) AND pp.type=:placeType
            GROUP BY dr.is_pre_delivery';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
            ':from' => $filters['from'],
            ':to' => $filters['to'],
            ':placeType' => $type
        ));

        return $query->execute()->as_array();
    }
}