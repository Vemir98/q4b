<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.06.2017
 * Time: 13:55
 */
class ORM extends Kohana_ORM
{
    public function list_columns()
    {
        try {
            $cache_lifetime = 360000;// 100 часов
            $cache_key = $this->_table_name . "_structure";
            Cache::instance('file')->delete($cache_key);
            $result = Cache::instance('file')->get($cache_key);

            if ($result) {
                $columns_data = $result;
            }

            if (!isset($columns_data)) {
                $columns_data = $this->_db->list_columns($this->_table_name);
                Cache::instance()->set($cache_key, $columns_data, $cache_lifetime);
            }
        }catch (Exception $e){
            $columns_data = $this->_db->list_columns($this->_table_name);
        }
        return $columns_data;
    }

    /**
     * Count the number of records in the table.
     *
     * @return integer
     */
    public function count_all()
    {
        $selects = array();

        foreach ($this->_db_pending as $key => $method)
        {
            if ($method['name'] == 'select')
            {
                // Ignore any selected columns for now
                $selects[$key] = $method;
                unset($this->_db_pending[$key]);
            }
        }

        if ( ! empty($this->_load_with))
        {
            foreach ($this->_load_with as $alias)
            {
                // Bind relationship
                $this->with($alias);
            }
        }
        $this->_build(Database::SELECT);

        $records = $this->_db_builder->from(array($this->_table_name, $this->_object_name))
            ->select(array(DB::expr('COUNT('.$this->_db->quote_column($this->_object_name.'.'.$this->_primary_key).')'), 'records_found'))
            ->execute($this->_db);

        $last_query = Database::instance()->last_query;

        // (HACK) Where in query have a GROUP BY 'records_found' returned only first row from GROUP BY
        if(strpos($last_query, 'GROUP BY')){
            $records = $records->count();
        }else{
            $records = $records->get('records_found');
        }

        // Add back in selected columns
        $this->_db_pending += $selects;

        $this->reset();

        // Return the total number of records in a table
        return (int) $records;
    }
}