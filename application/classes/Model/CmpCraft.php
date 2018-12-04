<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.10.2016
 * Time: 6:53
 */
class Model_CmpCraft extends ORM
{
    protected $_table_name = 'cmp_crafts';

    protected $_belongs_to = [
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ]
    ];
    protected $_has_many = [
        'professions' => [
            'model' => 'CmpProfession',
            'foreign_key' => 'craft_id',
            'far_key' => 'profession_id',
            'through' => 'cmp_professions_cmp_crafts'
        ],
        'certifications' => [
            'model' => 'PrCertification',
            'foreign_key' => 'craft_id'
        ],
        'plans' => [
            'model' => 'PrPlan',
            'through' => 'pr_plans_cmp_crafts',
            'foreign_key' => 'craft_id',
            'far_key' => 'plan_id'
        ],
        'quality_controls' => [
            'model' => 'QualityControl',
            'foreign_key' => 'craft_id'
        ],
    ];

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

    public function source(){
        return 'custom';
    }
}