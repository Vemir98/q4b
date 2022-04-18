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

    public static function getDeliveryReportQcs($delRepId) :array
    {
        $query = 'SELECT
            id
            FROM quality_controls qc
            WHERE qc.del_rep_id=:delRepId';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':delRepId' => $delRepId
        ));

        return $query->execute()->as_array();
    }

    public static function getDeliveryReportQcsCount($delRepId) :array
    {
        $query = 'SELECT
            COUNT(qc.id) as count
            FROM quality_controls qc
            WHERE qc.del_rep_id=:delRepId';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':delRepId' => $delRepId
        ));

        return $query->execute()->as_array();
    }

    public static function getDeliveryReportQcsExpectedCount($delRepId) :array
    {
        $query = 'SELECT
            expected_qc_count as count
            FROM delivery_reports dr
            WHERE dr.id=:delRepId';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':delRepId' => $delRepId
        ));

        return $query->execute()->as_array();
    }
}