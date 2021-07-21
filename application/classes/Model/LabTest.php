<?php defined('SYSPATH') OR die('No direct script access.');

class Model_LabTest extends MORM
{
    protected $_table_name = 'labtests';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'element' => [
            'model' => 'Element',
            'foreign_key' => 'element_id'
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
        'createUser' => [
            'model' => 'User',
            'foreign_key' => 'created_by'
        ],
        'updateUser' => [
            'model' => 'User',
            'foreign_key' => 'updated_by'
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
    ];

    protected $_has_many = [
        'tickets' => [
            'model' => 'LabTestTicket',
            'foreign_key' => 'labtest_id',
        ],
        'craftLabTests' => [
            'model' => 'CraftLabTest',
            'through' => 'labtest_clp',
            'foreign_key' => 'labtest_id',
            'far_key' => 'cl_id'
        ],
    ];

    public function rules(){
        return [

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