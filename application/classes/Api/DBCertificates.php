<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBCertificates
{
    public static function getProjectsCertificatesCountsByType($filters) : array
    {
        $query = 'SELECT
            c.status,
            COUNT(DISTINCT c.id) as count
            FROM certifications c
            WHERE c.project_id IN (:projectIds) AND (c.created_at>=:from AND c.created_at<=:to)
            GROUP BY c.status';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
            ':from' => $filters['from'],
            ':to' => $filters['to']
        ));

        return $query->execute()->as_array();
    }
}