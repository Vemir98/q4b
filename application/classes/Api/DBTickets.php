<?php

class Api_DBTickets {
    public static function getTicketById($id)
    {
        return DB::query(Database::SELECT,'SELECT 
        lt.*, u.name AS created_by_name, 
        u1.name AS updated_by_name,
        f.id AS fId,
        f.name AS fileName,
        f.path AS filePath,
        f.original_name AS fileOriginalName
        FROM labtests_tickets lt 
        LEFT JOIN users u ON lt.created_by = u.id 
        LEFT JOIN users u1 ON lt.updated_by = u1.id
        LEFT JOIN labtests_tickets_files ltf ON lt.id = ltf.ticket_id
        LEFT JOIN files f ON ltf.file_id = f.id
        WHERE lt.id='.$id)->execute()->as_array();
    }

}