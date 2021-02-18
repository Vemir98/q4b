<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.03.2017
 * Time: 0:46
 */
class Model_PlanFileAlias extends ORM
{
    protected $_table_name = 'pr_plans_file_aliases_files';

    protected $_belongs_to = [
        'planFile' => [
            'model' => 'PlanFile',
            'foreign_key' => 'file_id',
            'far_key' => 'id'
        ]
    ];


}