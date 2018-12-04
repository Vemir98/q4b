<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.10.2016
 * Time: 6:53
 */
class Model_Profession extends ORM
{
    protected $_table_name = 'professions';
    
    protected $_has_many = [
        'crafts' => [
            'model' => 'Craft',
            'foreign_key' => 'profession_id',
            'far_key' => 'craft_id',
            'through' => 'professions_crafts'
        ],
    ];

    public static function getProfCraftsIdsKeyValPairArray(){
        $output = [];
        $pc = DB::query(Database::SELECT,'SELECT pc.profession_id pid, craft_id cid FROM professions_crafts pc 
                      INNER JOIN professions p ON pc.profession_id = p.id')
        ->execute();

        if(!empty($pc)){
            foreach($pc as $p){
                $output[$p['pid']][] = $p['cid'];
            }
        }

        return $output;
    }

    public static function disableProfessionsWithoutRelation(){
        $result = DB::query(Database::SELECT,'SELECT cp.id FROM professions cp LEFT JOIN professions_crafts cpcc ON cp.id = cpcc.profession_id 
        WHERE  cpcc.profession_id IS NULL')->execute()->as_array('id');
        if(count($result)){
            $ids = implode(',',array_keys($result));
            DB::query(Database::UPDATE,'UPDATE professions SET status = "'.Enum_Status::Disabled.'" WHERE id IN 
        ('.$ids.')')->execute();
        }
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
            'name' => [
                ['not_empty'],
            ],
        ];
    }
}