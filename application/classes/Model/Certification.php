<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 9:56
 */
class Model_Certification extends MORM
{
    protected $_table_name = 'certifications';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_has_one = [
        'file' => [
            'model' => 'CertificationFile',
            'foreign_key' => 'certification_id',
        ]
    ];

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'company' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'craft' => [
            'model' => 'Craft',
            'foreign_key' => 'craft_id'
        ],
        'companyCraft' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'cmp_craft_id'
        ]
    ];

    public function rules()
    {
        return [
            'project_id' => [
                ['numeric']
            ],
            'company_id' => [
                ['numeric']
            ],
            'craft_id' => [
                ['numeric']
            ],
            'desc' => [
                ['not_empty'],
                ['max_length',[':value',250]]
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

    /**
     * Перегрузка метода инициализации
     * Добавляем информацию о тех кто работал с текущей записью
     */
    protected function _initialize(){
        if(Auth::instance()->get_user()){
            $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
            $this->_updated_by_column = ['column' => 'updated_by', 'value' => Auth::instance()->get_user()->id];
        }else{
            $this->_created_by_column = ['column' => 'created_by'];
            $this->_updated_by_column = ['column' => 'updated_by'];
        }
        parent::_initialize();
    }
}