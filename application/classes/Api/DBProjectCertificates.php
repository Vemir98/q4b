<?php

/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 28.03.2022
 * Time: 17:01
 */
class Api_DBProjectCertificates
{
    public static function getProjectCertificatesByProjectId($projectId) : array
    {
        $query = 'SELECT
            pc.id,
            pc.name,
            pc.sample_required as sampleRequired,
            pc.craft_id as craftId,
            pc.project_id as projectId,
            pc.status,
            pc.approved_at as approvedAt,
            pc.approved_by as approvedBy,
            u.name as approverName,
            pc.updated_at as updatedAt,
            pc.updated_by as updatedBy,
            pc.created_at as createdAt,
            pc.created_by as createdBy
            FROM pr_certifications pc
            LEFT JOIN users u ON pc.approved_by=u.id
            WHERE pc.project_id = :projectId';

        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':projectId' => $projectId
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectCertificatesByCertificatesIds($certificatesIds) : array
    {
        $query = 'SELECT
            pc.id,
            pc.name,
            pc.sample_required as sampleRequired,
            pc.craft_id as craftId,
            pc.project_id as projectId,
            pc.status,
            pc.approved_at as approvedAt,
            pc.approved_by as approvedBy,
            u.name as approverName,
            pc.created_at as createdAt,
            pc.created_by as createdBy
            FROM pr_certifications pc
            LEFT JOIN users u ON pc.approved_by=u.id
            WHERE pc.id IN (:certificatesIds)';

        $certificatesIds = DB::expr(implode(',', $certificatesIds));
        $query =  DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':certificatesIds' => $certificatesIds
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectCertificatesChapters($certificatesIds) : array
    {
        $query = 'SELECT
            pcc.id,
            pcc.pr_cert_id as certificateId,
            pcc.pr_chapter_id as chapterId,
            pc.project_id as projectId,
            pc.name,
            pcc.text
            FROM pr_certifications_chapters pcc
            LEFT JOIN pr_chapters pc ON pcc.pr_chapter_id=pc.id
            WHERE pcc.pr_cert_id IN (:certificatesIds)';

        $certificatesIds = DB::expr(implode(',', $certificatesIds));
        $query = DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':certificatesIds' => $certificatesIds
        ));

        return $query->execute()->as_array();
    }

    public static function getProjectCertificatesParticipants($certificatesIds) : array
    {
        $query = 'SELECT
            pcp.id,
            pcp.pr_cert_id as certificateId,
            pcp.name,
            pcp.position
            FROM pr_cert_participants pcp
            WHERE pcp.pr_cert_id IN (:certificatesIds)';

        $certificatesIds = DB::expr(implode(',', $certificatesIds));
        $query = DB::query(Database::SELECT, $query);

        $query->parameters(array(
            ':certificatesIds' => $certificatesIds
        ));

        return $query->execute()->as_array();
    }
}

