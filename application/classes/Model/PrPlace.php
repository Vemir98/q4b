<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.12.2016
 * Time: 3:28
 */
class Model_PrPlace extends ORM
{
    protected $_table_name = 'pr_places';
    
    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'object' => [
            'model' => 'PrObject',
            'foreign_key' => 'object_id'
        ],
        'floor' => [
            'model' => 'PrFloor',
            'foreign_key' => 'floor_id'
        ]
    ];

    protected $_has_many = [
        'spaces' => [
            'model' => 'PrSpace',
            'foreign_key' => 'place_id'
        ],
        'plans' => [
            'model' => 'PrPlan',
            'foreign_key' => 'place_id'
        ],
        'quality_control' => [
            'model' => 'QualityControl',
            'foreign_key' => 'place_id'
        ]
    ];


    public function cloneIntoFloor(Model_PrFloor $floor){
        $place = ORM::factory('PrPlace');
        $place->values($this->as_array(),['name','icon','type','number','ordering','custom_number']);
        $place->project_id = $floor->project_id;
        $place->object_id = $floor->object_id;
        $place->floor_id = $floor->id;
        $place->save();
        foreach ($this->spaces->find_all() as $space){
            $space->cloneToPlace($place);
        }
        return $place;
    }


    public function getTableName(){
        return $this->_table_name;
    }

//    public function getRelevantPlan(){
//        $plan =  $this->plan;
//        if( ! $plan->loaded()){
//            $plan = $this->floor->plans->where('place_id','=',0)->order_by('created_at','DESC')->find();
//            if( ! $plan->loaded()){
//                $plan = null;
//            }
//        }
//        return $plan;
//    }

    public function filters()
    {
        return [
            true => [
                ['htmlentities', [':value'],ENT_QUOTES],
                ['strip_tags']
            ]
        ];
    }

    public function rules(){
        return [
            'name' => [
                ['not_empty'],
                ['max_length',[':value','50']],
            ],
            'icon' => [
                ['not_empty'],
                ['max_length',[':value','50']],
            ],
            'type' => [
                ['not_empty'],
                ['max_length',[':value','10']],
                [
                    function(Validation $valid){
                        if(!in_array($this->type,Enum_ProjectPlaceType::toArray())){
                            $valid->error('type', 'invalid_place_type');
                        }
                    },
                    [':validation']
                ],
            ],
        ];
    }
}