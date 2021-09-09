<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBUsers
{
    public static function getUsersList()
    {
        $query = "SELECT
            u.id,
            u.name,
            u.email,
            u.phone,
            u.username,
            u.logins,
            u.last_login as lastLogin,
            u.created_by as cretedBy,
            u.status,
            u.terms_agreed as termsAgreed,
            u.lang,
            u.client_id as clientId,
            u.company_id as companyId
            FROM users u";

        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function getRolesByUserId($userId)
    {
        $query = "SELECT 
            r.id,
            r.name,
            r.description,
            r.priority,
            r.outspread
            FROM roles_users ru
            LEFT JOIN roles r ON r.id=ru.role_id
            WHERE ru.user_id=:userId";

        return DB::query(Database::SELECT, $query)
            ->bind(':userId', $userId)
            ->execute()->as_array();
    }

    public static function getUsersByCompanyId($companyId)
    {
        $query = "SELECT
            u.id,
            u.name,
            u.email,
            u.phone,
            u.username,
            u.logins,
            u.last_login as lastLogin,
            u.created_by as cretedBy,
            u.status,
            u.terms_agreed as termsAgreed,
            u.lang,
            u.client_id as clientId,
            u.company_id as companyId
            FROM users u 
            WHERE company_id=:companyId";

        return DB::query(Database::SELECT, $query)
            ->bind(':companyId', $companyId)
            ->execute()->as_array();
    }

    public static function getUsersByProjectId($projectId)
    {
        $query = "SELECT
            u.id,
            u.name,
            u.email,
            u.phone,
            u.username,
            u.logins,
            u.last_login as lastLogin,
            u.created_by as cretedBy,
            u.status,
            u.terms_agreed as termsAgreed,
            u.lang,
            u.client_id as clientId,
            u.company_id as companyId
            FROM users u
            LEFT JOIN users_projects up ON up.user_id=u.id
            WHERE up.project_id=:projectId";

        return DB::query(Database::SELECT, $query)
            ->bind(':projectId', $projectId)
            ->execute()->as_array();
    }

    public static function getUsersByRoleId($roleId)
    {
        $query = "SELECT
            u.id,
            u.name,
            u.email,
            u.phone,
            u.username,
            u.logins,
            u.last_login as lastLogin,
            u.created_by as cretedBy,
            u.status,
            u.terms_agreed as termsAgreed,
            u.lang,
            u.client_id as clientId,
            u.company_id as companyId
            FROM users u
            LEFT JOIN roles_users ru ON ru.user_id=u.id
            LEFT JOIN roles r ON ru.role_id=r.id
            WHERE r.id=:roleId";

        return DB::query(Database::SELECT, $query)
            ->bind(':roleId', $roleId)
            ->execute()->as_array();
    }

    public static function getUsersByRoleOutspread($outspread)
    {
        $query = 'SELECT
            u.id,
            u.name,
            u.email,
            u.phone,
            u.username,
            u.logins,
            u.last_login as lastLogin,
            u.created_by as cretedBy,
            u.status,
            u.terms_agreed as termsAgreed,
            u.lang,
            u.client_id as clientId,
            u.company_id as companyId
            FROM users u
            LEFT JOIN roles_users ru ON ru.user_id=u.id
            LEFT JOIN roles r ON ru.role_id=r.id
            WHERE r.outspread=:outspread';

        return DB::query(Database::SELECT, $query)
            ->bind(':outspread', $outspread)
            ->execute()->as_array();
    }

    private static function toCamelCase($string) {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $string))));
    }
}