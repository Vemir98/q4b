<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.10.2016
 * Time: 6:53
 */
class Model_CmpProfession extends ORM
{
    protected $_table_name = 'cmp_professions';
    
    protected $_belongs_to = [
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ]
    ];
    protected $_has_many = [
        'crafts' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'profession_id',
            'far_key' => 'craft_id',
            'through' => 'cmp_professions_cmp_crafts'
        ],
        'users' => [
            'model' => 'User',
            'foreign_key' => 'profession_id',
            'fer_key' => 'user_id',
            'through' => 'users_cmp_professions'
        ],
    ];

    public static function getProfCraftsIdsKeyValPairArray($company_id){
        $output = [];
        $pc = DB::query(Database::SELECT,'SELECT pc.profession_id pid, craft_id cid FROM cmp_professions_cmp_crafts pc 
                      INNER JOIN cmp_professions p ON pc.profession_id = p.id WHERE p.company_id = '.(int)$company_id)
        ->execute();

        if(!empty($pc)){
            foreach($pc as $p){
                $output[$p['pid']][] = $p['cid'];
            }
        }

        return $output;
    }

    public static function disableProfessionsWithoutRelation($company_id){
        $result = DB::query(Database::SELECT,'SELECT cp.id FROM cmp_professions cp LEFT JOIN cmp_professions_cmp_crafts cpcc ON cp.id = cpcc.profession_id 
        WHERE cp.company_id = '.(int)$company_id.' AND cpcc.profession_id IS NULL')->execute()->as_array('id');
        if(count($result)){
            $ids = implode(',',array_keys($result));
            DB::query(Database::UPDATE,'UPDATE cmp_professions SET status = "'.Enum_Status::Disabled.'" WHERE id IN 
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
            'company_id' => [
                ['not_empty'],
                ['numeric']
            ],
        ];
    }
}