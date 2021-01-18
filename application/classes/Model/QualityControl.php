<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 24.04.2017
 * Time: 9:08
 */
class Model_QualityControl extends MORM
{
    protected $_table_name = 'quality_controls';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'object' => [
            'model' => 'PrObject',
            'foreign_key' => 'object_id'
        ],
        'place' => [
            'model' => 'PrPlace',
            'foreign_key' => 'place_id'
        ],
        'floor' => [
            'model' => 'PrFloor',
            'foreign_key' => 'floor_id'
        ],
        'profession' => [
            'model' => 'CmpProfession',
            'foreign_key' => 'profession_id'
        ],
        'createUser' => [
            'model' => 'User',
            'foreign_key' => 'created_by'
        ],
        'updateUser' => [
            'model' => 'User',
            'foreign_key' => 'updated_by'
        ],
        'approveUser' => [
            'model' => 'User',
            'foreign_key' => 'approved_by'
        ],
        'craft' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'craft_id',
        ],
        'plan' => [
            'model' => 'PrPlan',
            'foreign_key' => 'plan_id',
        ],
        'space' => [
            'model' => 'PrSpace',
            'foreign_key' => 'space_id'
        ],
        'delivery_report' => [
            'model' => 'DeliveryReport',
            'foreign_key' => 'del_rep_id'
        ],
    ];

    protected $_has_many = [
        'tasks' => [
            'model' => 'PrTask',
            'foreign_key' => 'qcontrol_id',
            'far_key' => 'task_id',
            'through' => 'qcontrol_pr_tasks'
        ],
        'images' => [
            'model' => 'Image',
            'through' => 'quality_controls_files',
            'foreign_key' => 'qc_id',
            'far_key' => 'file_id'
        ],
        'comments' => [
            'model' => 'QcComment',
            'foreign_key' => 'qcontrol_id',
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
            'project_stage' => [
                ['not_empty'],
            ],
            'craft_id' => [
                ['not_empty'],
            ],
            'project_id' => [
                ['not_empty'],
            ],
            'object_id' => [
                ['not_empty'],
            ],
            'floor_id' => [
                ['not_empty'],
            ],
            'place_id' => [
                ['not_empty'],
            ],
            'space_id' => [
                ['not_empty'],
            ],
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

    public function userHasExtraPrivileges(Model_User $user){
        if($user->getRelevantRole('outspread') == Enum_UserOutspread::General OR in_array($user->getRelevantRole('name'),['corporate_admin','corporate_infomanager','company_admin','company_manager'])){
            return true;
        }else{
            return false;
        }
    }

    public function getComments(){
        return $this->comments->order_by('created_at','ASC')->find_all();
    }

    public function getDialog($str, $pattern)
    {
        if (strlen($str) > 0) {

            $substr = explode($pattern,$str);
            unset($substr[0]);

            return implode("\n",$substr);
        }
        return "";
    }

    public function getDesc($str, $pattern)
    {
        if (strlen($str) > 0) {
            $pos = strpos($str, $pattern);
            return $pos ? rtrim(substr($str,0, $pos), " \n\r\t\v\0") : rtrim($str, " \n\r\t\v\0");
        }
        return $str;
    }

    public function create(Validation $validation = NULL)
    {
        if ($this->_loaded)
            throw new Kohana_Exception('Cannot create :model model because it is already loaded.', array(':model' => $this->_object_name));

        // Require model validation before saving
        if ( ! $this->_valid OR $validation)
        {
            $this->check($validation);
        }

        $data = array();
        foreach ($this->_changed as $column)
        {
            // Generate list of column => values
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_created_column))
        {
            // Fill the created column
            $column = $this->_created_column['column'];
            $format = $this->_created_column['format'];

            $data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
            $data[$this->_updated_column['column']] = $this->_object[$this->_updated_column['column']] = $data[$column];
        }

        if (is_array($this->_created_by_column))
        {
            // Fill the created by column
            $column = $this->_created_by_column['column'];
            $value = $this->_created_by_column['value'];

            $data[$column] = $this->_object[$column] = $value;
        }

        $result = DB::insert($this->_table_name)
            ->columns(array_keys($data))
            ->values(array_values($data))
            ->execute($this->_db);

        if ( ! array_key_exists($this->_primary_key, $data))
        {
            // Load the insert id as the primary key if it was left out
            $this->_object[$this->_primary_key] = $this->_primary_key_value = $result[0];
        }
        else
        {
            $this->_primary_key_value = $this->_object[$this->_primary_key];
        }

        // Object is now loaded and saved
        $this->_loaded = $this->_saved = TRUE;

        // All changes have been saved
        $this->_changed = array();
        $this->_original_values = $this->_object;
        $this->clearLTCache();
        return $this;
    }
}