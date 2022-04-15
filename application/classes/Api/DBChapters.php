<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 28.03.2022
 * Time: 16:37
 */
class Api_DBChapters
{
    public static function getProjectChapters($projectId) : array
    {
        $query = 'SELECT
            pc.id,
            pc.project_id as projectId,
            pc.name
            FROM pr_chapters pc
            WHERE pc.project_id = :projectId';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectId' => $projectId
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectChaptersImages($chaptersIds) : array
    {
        $query = 'SELECT
            f.id,
            pcci.cert_chapter_id as certChapterId,
            f.name,
            f.original_name as originalName,
            f.ext,
            f.mime,
            f.path,
            f.remote,
            f.created_at as createdAt,
            f.created_by as createdBy
            FROM pr_certifications_chapters_images pcci
            LEFT JOIN files f ON pcci.file_id=f.id
            WHERE pcci.cert_chapter_id IN (:chaptersIds)';

        $chaptersIds = DB::expr(implode(',', $chaptersIds));
        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':chaptersIds' => $chaptersIds
        ));

        return $query->execute()->as_array();
    }
}

