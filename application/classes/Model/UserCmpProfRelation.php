<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.10.2016
 * Time: 14:43
 */
class Model_UserCmpProfRelation extends ORM
{
    protected $_table_name = 'users_cmp_professions';
    protected $_created_column = ['column' => 'created_at', 'format' => true];
}