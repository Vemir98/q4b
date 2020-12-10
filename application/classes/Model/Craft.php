<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 01.03.2017
 * Time: 19:56
 */
class Model_Craft extends ORM
{
    protected $_table_name = 'crafts';

    protected $_has_many = [
        'tasks' => [
            'model' => 'Task',
            'foreign_key' => 'craft_id',
            'far_key' => 'task_id',
            'through' => 'tasks_crafts'
        ],
        'professions' => [
            'model' => 'Profession',
            'foreign_key' => 'craft_id',
            'far_key' => 'profession_id',
            'through' => 'professions_crafts'
        ],
        'regulations' => [
            'model' => 'Certification',
            'foreign_key' => 'craft_id',
        ]
    ];

    public function source(){
        return 'base';
    }
}