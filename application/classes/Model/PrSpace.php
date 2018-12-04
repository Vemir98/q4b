<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.12.2016
 * Time: 3:29
 */
class Model_PrSpace extends ORM
{
    protected $_table_name = 'pr_spaces';

    protected $_belongs_to = [
        'place' => [
            'model' => 'PrPlace',
            'foreign_key' => 'place_id'
        ],
        'type' => [
            'model' => 'PrSpaceType',
            'foreign_key' => 'type_id'
        ],
    ];

    public function cloneToPlace(Model_PrPlace $place){
        $space = ORM::factory('PrSpace');
        $space->values($this->as_array(),['desc','type_id']);
        $space->place_id = $place->id;
        $space->save();
        return $space;
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

    public function rules(){
        return [
            'place_id' => [
                ['not_empty'],
            ],
            'type_id' => [
                ['not_empty'],
            ],
        ];
    }
}