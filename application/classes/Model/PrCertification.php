<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 9:56
 */
class Model_PrCertification extends MORM
{
    protected $_table_name = 'projects_certifications';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_has_many = [
        'files' => [
            'model' => 'CertificationFile',
            'foreign_key' => 'certification_id',
            'far_key' => 'file_id',
            'through' => 'projects_certifications_files'
        ]
    ];

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'craft' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'craft_id'
        ]
    ];

    public function rules()
    {
        return [
            'project_id' => [
                ['not_empty'],
                ['numeric']
            ],
            'craft_id' => [
                ['numeric']
            ],
            'name' => [
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