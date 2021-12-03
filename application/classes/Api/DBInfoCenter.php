<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBInfoCenter
{
    public static function getProjectMessagesIds($projectId) : array
    {
        $query = 'SELECT
            ppm.pm_id as messageId
            FROM projects_projects_messages ppm
            WHERE ppm.project_id = :projectId
            ORDER BY ppm.pm_id DESC';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectId' => $projectId
        ));

        return $query->execute()->as_array();
    }

    public static function getMessagesIdByHistoryId($historyId) : array
    {
        $query = 'SELECT
            pmc.pm_id as messageId
            FROM projects_messages_contents pmc
            WHERE pmc.id = :historyId
            ORDER BY pmc.pm_id DESC';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':historyId' => $historyId
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectMessages($messageIds) : array
    {
        $query = 'SELECT
            pm.id,
            pm.created_at as createdAt,
            pm.created_by as createdBy,
            u.name as creatorName
            FROM projects_messages pm
            LEFT JOIN users u on pm.created_by = u.id
            WHERE pm.id IN (:messageIds)
            ORDER BY pm.id DESC';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':messageIds' => DB::expr(implode(',',$messageIds)),
        ));

        return $query->execute()->as_array();
    }

    public static function getMessageProjects($messageId) : array
    {
        $query = 'SELECT
            ppm.project_id as projectId
            FROM projects_messages pm
            INNER JOIN projects_projects_messages ppm on pm.id = ppm.pm_id
            WHERE pm.id = :messageId
            ORDER BY ppm.project_id DESC';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':messageId' => $messageId,
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectMessagesHistory($messageIds) : array
    {
        $query = 'SELECT
            pmc.id,
            pmc.pm_id as pmId,
            pmc.parent_id as parentId,
            pmc.text,
            pmc.created_at as createdAt,
            pmc.created_by as createdBy,
            u.name as creatorName
            FROM projects_messages_contents pmc
            LEFT JOIN users u on pmc.created_by = u.id
            WHERE pmc.pm_id IN (:messageIds)
            ORDER BY pmc.id DESC';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':messageIds' => DB::expr(implode(',',$messageIds)),
        ));

        return $query->execute()->as_array();
    }
}

