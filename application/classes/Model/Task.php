<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 01.03.2017
 * Time: 19:55
 */
class Model_Task extends ORM
{
    protected $_table_name = 'tasks';

    protected $_has_many = [
        'crafts' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'task_id',
            'far_key' => 'craft_id',
            'through' => 'tasks_crafts'
        ]
    ];

    public static function getTaskCraftsIdsKeyValPairArray(){
        $output = [];
        $tc = DB::query(Database::SELECT,'SELECT tc.task_id pid, craft_id cid FROM tasks_crafts tc 
                      INNER JOIN tasks t ON tc.task_id = t.id')
            ->execute();

        if(!empty($tc)){
            foreach($tc as $t){
                $output[$t['pid']][] = $t['cid'];
            }
        }

        return $output;
    }
}