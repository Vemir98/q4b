<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.03.2017
 * Time: 14:26
 */
class Model_PrTask extends ORM
{
    protected $_table_name = 'pr_tasks';

    protected $_has_many = [
        'crafts' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'task_id',
            'far_key' => 'craft_id',
            'through' => 'pr_tasks_crafts'
        ],
        'crafts_relation' => [
            'model' => 'PrCraftRel',
            'foreign_key' => 'task_id',
        ]
    ];

    public function getQualityControlStatus(){
        $started = 0;
        $checked = 0;
        $count = $this->crafts_relation->count_all();
        foreach ($this->crafts_relation->find_all() as $cr){
            switch($cr->status){
                case Enum_ProjectTaskQqStatus::Started: $started++; break;
                case Enum_ProjectTaskQqStatus::Checked: $checked++; break;
            }
        }

        if( ($started + $checked) != $count AND ($started + $checked) > 0){
            return Enum_ProjectTaskQqStatus::Started;
        }else if($started > 0){
            return Enum_ProjectTaskQqStatus::Started;
        }else if($checked == $count){
            return Enum_ProjectTaskQqStatus::Completed;
        }else{
            return Enum_ProjectTaskQqStatus::NotStarted;
        }
    }

    public function rules(){
        return [
            'project_id' => [
                ['not_empty'],
            ],
            'name' => [
                ['not_empty'],
            ],
            'status' => [
                ['not_empty'],
                [
                    function(Validation $valid){
                        if(!in_array($this->status,Enum_Status::toArray())){
                            $valid->error('status', 'invalid_status');
                        }
                    },
                    [':validation']
                ],
            ],
        ];
    }

    public function filters()
    {
        return [
            true => [
                ['htmlentities', [':value'],ENT_QUOTES],
                ['strip_tags']
            ]
        ];
    }
}