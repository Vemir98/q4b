<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.10.2016
 * Time: 19:02
 * Модифицированный класс ORM
 */
class MORM extends ORM
{
    /**
     * Кем создана запись
     * @var null | ['column' => '', 'value' => '']
     */
    protected $_created_by_column = null;

    /**
     * Кем обновлена запись
     * @var null | ['column' => '', 'value' => '']
     */
    protected $_updated_by_column = null;

    /**
     * Поле для кеширования чего либо на время жизни класса
     * @var array
     */
    protected $_life_time_cache = [];

    protected function setLTCache($name,$val){
        $this->_life_time_cache[$name] = $val;
        return $this;
    }

    protected function getLTCache($name){
        return isset($this->_life_time_cache[$name]) ? $this->_life_time_cache[$name] : null;
    }

    protected function clearLTCache($name = null){
        if(is_null($name)){
            $this->_life_time_cache = null;
        }else if(isset($this->_life_time_cache[$name])){
            unset($this->_life_time_cache[$name]);
        }
        return $this;
    }

    /**
     * Insert a new object to the database
     * @param  Validation $validation Validation object
     * @throws Kohana_Exception
     * @return ORM
     */
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

    /**
     * Updates a single record or multiple records
     *
     * @chainable
     * @param  Validation $validation Validation object
     * @throws Kohana_Exception
     * @return ORM
     */
    public function update(Validation $validation = NULL)
    {
        if ( ! $this->_loaded)
            throw new Kohana_Exception('Cannot update :model model because it is not loaded.', array(':model' => $this->_object_name));

        // Run validation if the model isn't valid or we have additional validation rules.
        if ( ! $this->_valid OR $validation)
        {
            $this->check($validation);
        }

        if (empty($this->_changed))
        {
            // Nothing to update
            return $this;
        }

        $data = array();
        foreach ($this->_changed as $column)
        {
            // Compile changed data
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_updated_column))
        {
            // Fill the updated column
            $column = $this->_updated_column['column'];
            $format = $this->_updated_column['format'];

            $data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
        }

        if (is_array($this->_updated_by_column))
        {
            // Fill the updated by column
            $column = $this->_updated_by_column['column'];
            $value = $this->_updated_by_column['value'];

            $data[$column] = $this->_object[$column] = $value;
        }

        // Use primary key value
        $id = $this->pk();

        // Update a single record
        DB::update($this->_table_name)
            ->set($data)
            ->where($this->_primary_key, '=', $id)
            ->execute($this->_db);

        if (isset($data[$this->_primary_key]))
        {
            // Primary key was changed, reflect it
            $this->_primary_key_value = $data[$this->_primary_key];
        }

        // Object has been saved
        $this->_saved = TRUE;

        // All changes have been saved
        $this->_changed = array();
        $this->_original_values = $this->_object;
        $this->clearLTCache();
        return $this;
    }

    public function created_by_column(){
        return $this->_created_by_column;
    }

    public function updated_by_column(){
        return $this->_updated_by_column;
    }

    public function _setCreatedBy($val){
        $this->_created_by_column['value'] = (int)$val;
    }

    public function _setUpdatedBy($val){
        $this->_updated_by_column['value'] = (int)$val;
    }
}

