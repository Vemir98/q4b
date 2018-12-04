<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:31
 */
class Job_Settings_ProcessNewData
{
    const filePath = APPPATH.'local-storage/settings.txt';
    protected $data;
    protected $companies;

    public function perform(){
        if(file_exists(APPPATH.'local-storage/settings.txt')) {
            $rawData = file_get_contents(self::filePath);
        }else{
            $rawData = false;
        }
        if($rawData !== false){
            $this->data = json_decode($rawData,true);
        }
        try{
            Database::instance()->begin();
            if(!empty($this->data)){
            $this->companies = ORM::factory('Company')->find_all();
            $this->processCrafts();
            $this->processProfessions();
            $this->processTasks();

            unlink(self::filePath);
            }
            $item = ORM::factory('Settings',['key' => 'settingsMode']);
            $item->val = Enum_Status::Enabled;
            $item->save();
            Database::instance()->commit();
        }catch (Exception $e){
            Database::instance()->rollback();
            throw $e;
        }

        //file_put_contents(self::filePath,json_encode($this->data));
    }

    protected function processCrafts(){
        if(!empty($this->data['crafts']) and !empty($this->data['crafts']['added'])){
            $craftIds = implode(',',$this->data['crafts']['added']);
            foreach ($this->companies as $company) {
                DB::query(Database::INSERT,'INSERT INTO cmp_crafts (company_id, name, catalog_number, status, related_id) 
          SELECT '.$company->id.',`name`, catalog_number, status, id FROM crafts WHERE id IN ('.$craftIds.') AND status = "'.Enum_Status::Enabled.'"')->execute();
            }
        }
    }

    protected function processProfessions()
    {
        if (!empty($this->data['professions']) and !empty($this->data['professions']['added'])) {
            $professionIds = implode(',', $this->data['professions']['added']);
            foreach ($this->companies as $company) {
                $profAndCrafts = DB::query(Database::SELECT, 'SELECT p.id p_id,p.name p_name, c.id c_id, c.name c_name FROM professions p 
          INNER JOIN professions_crafts pc ON p.id = pc.profession_id 
          INNER JOIN crafts c ON pc.craft_id = c.id
          WHERE p.id IN ('.$professionIds.') AND p.status = "' . Enum_Status::Enabled . '" 
          AND c.status="' . Enum_Status::Enabled . '"')->execute()->as_array();

                DB::query(Database::INSERT, 'INSERT INTO cmp_professions (company_id, name, catalog_number, status, related_id) 
          SELECT ' . $company->id . ',`name`, catalog_number, status,id FROM professions WHERE id IN ('.$professionIds.') AND status = "' . Enum_Status::Enabled . '"
        ')->execute();

                $profs = DB::query(Database::SELECT, 'SELECT * FROM cmp_professions c WHERE c.company_id = ' . $company->id)->execute()->as_array('name');

                $crafts = DB::query(Database::SELECT, 'SELECT * FROM cmp_crafts c WHERE c.company_id = ' . $company->id)->execute()->as_array('name');

                if (!empty($profAndCrafts)) {
                    $rel = [];
                    foreach ($profAndCrafts as $arr) {
                        if (isset($profs[$arr['p_name']]) AND isset($crafts[$arr['c_name']])) {
                            $rel [] = [$profs[$arr['p_name']]['id'], $crafts[$arr['c_name']]['id']];
                        }
                    }

                    $relInsertQuery = 'INSERT INTO cmp_professions_cmp_crafts (profession_id, craft_id) VALUES ';

                    for ($i = count($rel) - 1; $i >= 0; $i--) {
                        $relInsertQuery .= '(' . implode(',', $rel[$i]) . ')';
                        if ($i != 0) {
                            $relInsertQuery .= ', ';
                        }
                    }
                    DB::query(Database::INSERT, $relInsertQuery)->execute();
                }
            }
        }
    }

    protected function processTasks(){
        if(!empty($this->data['tasks']) and !empty($this->data['tasks']['added'])){
            $tasksIds = implode(',',$this->data['tasks']['added']);
            foreach ($this->companies as $company) {
                $tasksData = DB::query(Database::SELECT,'SELECT
              tasks.name,
              cmp_crafts.id AS craft_id,
              tasks.id AS task_id
            FROM tasks_crafts
              INNER JOIN tasks
                ON tasks_crafts.task_id = tasks.id
              INNER JOIN crafts
                ON tasks_crafts.craft_id = crafts.id
                AND crafts.status = "'.Enum_Status::Enabled.'"
              INNER JOIN cmp_crafts
                ON crafts.id = cmp_crafts.related_id
                AND cmp_crafts.status = "'.Enum_Status::Enabled.'" 
                WHERE tasks.id IN ('.$tasksIds.')
                AND cmp_crafts.company_id = '.$company->id)
                    ->execute()->as_array();
                foreach ($company->projects->find_all() as $project){
                    if(count($tasksData)){
                        $tasks = [];
                        foreach ($tasksData as $td){
                            if( ! isset($tasks[$td['name']])){
                                $tasks[$td['name']] = ORM::factory('PrTask');
                                $tasks[$td['name']]->name = $td['name'];
                                $tasks[$td['name']]->project_id = $project->id;
                                $tasks[$td['name']]->status = Enum_Status::Enabled;
                                $tasks[$td['name']]->save();
                            }
                        }

                        foreach ($tasksData as $td){
                            if(isset($tasks[$td['name']]) AND $tasks[$td['name']] instanceof ORM){
                                $tasks[$td['name']]->add('crafts',$td['craft_id']);
                            }
                        }
                    }
                }

            }
        }
    }
}