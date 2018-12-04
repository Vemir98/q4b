<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 9:56
 */
class Model_CmpStandard extends ORM
{
    protected $_table_name = 'companies_standards';

    protected $_has_many = [
        'files' => [
            'model' => 'StandardFile',
            'foreign_key' => 'standard_id',
            'far_key' => 'file_id',
            'through' => 'company_standards_files'
        ]
    ];

    protected $_belongs_to = [
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ]
    ];

    public function rules()
    {
        return [
            'company_id' => [
                ['not_empty'],
                ['numeric']
            ],
            'name' => [
                ['not_empty'],
                ['max_length',[':value',50]]
            ],
            'organisation' => [
                ['not_empty'],
                ['max_length',[':value',250]]
            ],
            'number' => [
                ['not_empty'],
                ['max_length',[':value',250]]
            ],
            'submission_place' => [
                ['not_empty'],
                ['max_length',[':value',250]]
            ],
            'responsible_person' => [
                ['not_empty'],
                ['numeric']
            ]
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