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

    public static function getFilteredCertificatesCount($filters) : array
    {
        $query = 'SELECT
            COUNT(DISTINCT pc.id) as reportsCount
            FROM pr_certifications pc';

        $query .= ' WHERE pc.project_id=:projectId';

        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $specialityIds = DB::expr(implode(',',$filters['specialityIds']));
            $query .= ' AND pc.craft_id IN (:specialityIds)';
        }
        if(isset($filters['sampleRequired']) && $filters['sampleRequired'] === '1'){
            $sampleRequired = $filters['sampleRequired'];
            $query .= ' AND pc.sample_required=:sampleRequired';
        }
        $query .= ' ORDER BY pc.created_at DESC';

        $query =  DB::query(Database::SELECT, $query);



        $query->parameters(array(
            ':companyId' => $filters['companyId'],
            ':projectId' => $filters['projectId'],
        ));

        if(isset($specialityIds)) $query->param(':specialityIds', $specialityIds);
        if(isset($sampleRequired)) $query->param(':sampleRequired', $sampleRequired);

        return $query->execute()->as_array();
    }

    public static function getFilteredCertificates($filters) : array
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
            LEFT JOIN users u ON pc.approved_by=u.id';


        $query .= ' WHERE pc.project_id=:projectId';


        if(isset($filters['specialityIds']) && !empty($filters['specialityIds'])){
            $specialityIds = DB::expr(implode(',',$filters['specialityIds']));
            $query .= ' AND pc.craft_id IN (:specialityIds)';
        }
        if(isset($filters['sampleRequired']) && $filters['sampleRequired'] === '1'){
            $sampleRequired = $filters['sampleRequired'];
            $query .= ' AND pc.sample_required=:sampleRequired';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $statuses = DB::expr(implode('","',$filters['statuses']));
            $query .= ' AND pc.status IN (":statuses")';
        }

        $query .= ' ORDER BY pc.created_at DESC';


        $query =  DB::query(Database::SELECT, $query);


        if(isset($specialityIds)) $query->param(':specialityIds', $specialityIds);
        if(isset($sampleRequired)) $query->param(':sampleRequired', $sampleRequired);
        if(isset($statuses)) $query->param(':statuses', $statuses);

        $query->parameters(array(
            ':companyId' => $filters['companyId'],
            ':projectId' => $filters['projectId'],
        ));
//        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($query); echo "</pre>"; exit;

        $query->execute();


        return $query->execute()->as_array();
    }

    public static function getProjectsCertificatesCountsByType($filters) : array
    {
        $query = 'SELECT
            pc.status,
            COUNT(DISTINCT pc.id) as count
            FROM pr_certifications pc';

        $query .= ' WHERE pc.project_id IN (:projectIds)';

        if(isset($filters['specialitiesIds']) && !empty($filters['specialitiesIds'])){
            $specialitiesIds = DB::expr(implode(',',$filters['specialitiesIds']));
            $query .= ' AND pc.craft_id IN (:specialitiesIds)';
        }

        if(isset($filters['sampleRequired']) && $filters['sampleRequired'] === '1'){
            $sampleRequired = $filters['sampleRequired'];
            $query .= ' AND pc.sample_required=:sampleRequired';
        }
        if(isset($filters['statuses']) && !empty($filters['statuses'])){
            $statuses = DB::expr(implode('","',$filters['statuses']));
            $query .= ' AND pc.status IN (":statuses")';
        }

        if(isset($filters['from'])){
            $from = $filters['from'];
            $query .= ' AND pc.created_at>=:from';
        }
        if(isset($filters['to'])){
            $to = $filters['to'];
            $query .= ' AND pc.created_at<=:to';
        }
        $query .= ' GROUP BY pc.status';


        $query =  DB::query(Database::SELECT, $query);

        if(isset($specialitiesIds)) $query->param(':specialitiesIds', $specialitiesIds);
        if(isset($sampleRequired)) $query->param(':sampleRequired', $sampleRequired);
        if(isset($statuses)) $query->param(':statuses', $statuses);
        if(isset($from)) $query->param(':from', $from);
        if(isset($to)) $query->param(':to', $to);

        $query->parameters(array(
            ':projectIds' => DB::expr(implode(',',$filters['projectIds'])),
        ));

        return $query->execute()->as_array();
    }

}

