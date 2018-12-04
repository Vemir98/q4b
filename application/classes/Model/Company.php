<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.09.2016
 * Time: 12:28
 */
class Model_Company extends MORM
{
    protected $_table_name = 'companies';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];
    
    protected $_has_many = [
        'crafts' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'company_id'
        ],
        'professions' => [
            'model' => 'CmpProfession',
            'foreign_key' => 'company_id'
        ],
        'standards' => [
            'model' => 'CmpStandard',
            'foreign_key' => 'company_id'
        ],
        'users' => [
            'model' => 'User',
            'foreign_key' => 'company_id'
        ],
        'projects' => [
            'model' => 'Project',
            'foreign_key' => 'company_id'
        ],
        'links' => [
            'model' => 'Link',
            'foreign_key' => 'company_id',
            'far_key' => 'link_id',
            'through' => 'companies_links'
        ]
    ];
    
    protected $_belongs_to = [
        'client' => [
            'model' => 'Client',
            'foreign_key' => 'client_id'
        ],
        'country' => [
            'model' => 'Country',
            'foreign_key' => 'country_id'
        ]
    ];


    /**
     * Переопределение метода инициализации
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

    public function rules(){
        return [
            'name' => [
                ['not_empty'],
                ['max_length',[':value','50']],
            ],
            'address' => [
                ['not_empty'],
                ['max_length',[':value','250']],
            ],
            'description' => [
                ['max_length',[':value','250']],
            ],
            'status' => [
                ['not_empty'],
                [
                    function(Validation $valid){
                        if(empty($this->created_by)){
                            if($this->status != Enum_CompanyStatus::Active){
                                $valid->error('status', 'invalid_company_status');
                            }
                        }else{
                            if(!in_array($this->status,Enum_CompanyStatus::toArray())){
                                $valid->error('status', 'invalid_company_status');
                            }
                        }
                    },
                    [':validation']
                ],
            ],
            'company_id' => [
                ['max_length',[':value','32']],
            ],
        ];
    }

    public function create(Validation $validation = NULL){
        parent::create($validation);
        //Достаём все активные связанные записи профессий и специальностей
        $profAndCrafts = DB::query(Database::SELECT,'SELECT p.id p_id,p.name p_name, c.id c_id, c.name c_name FROM professions p 
          INNER JOIN professions_crafts pc ON p.id = pc.profession_id 
          INNER JOIN crafts c ON pc.craft_id = c.id
          WHERE p.status = "'.Enum_Status::Enabled.'" 
          AND c.status="'.Enum_Status::Enabled.'"')->execute()->as_array();

        DB::query(Database::INSERT,'INSERT INTO cmp_professions (company_id, name, catalog_number, status, related_id) 
          SELECT '.$this->pk().',`name`, catalog_number, status,id FROM professions WHERE status = "'.Enum_Status::Enabled.'"
        ')->execute();

        DB::query(Database::INSERT,'INSERT INTO cmp_crafts (company_id, name, catalog_number, status, related_id) 
          SELECT '.$this->pk().',`name`, catalog_number, status, id FROM crafts WHERE status = "'.Enum_Status::Enabled.'"
        ')->execute();
        
        $profs = DB::query(Database::SELECT,'SELECT * FROM cmp_professions c WHERE c.company_id = '.$this->pk())->execute()->as_array('name');

        $crafts = DB::query(Database::SELECT,'SELECT * FROM cmp_crafts c WHERE c.company_id = '.$this->pk())->execute()->as_array('name');

        if(!empty($profAndCrafts)){
            $rel = [];
            foreach($profAndCrafts as $arr){
                if(isset($profs[$arr['p_name']]) AND isset($crafts[$arr['c_name']])){
                    $rel []= $profs[$arr['p_name']]['id'].', '.$crafts[$arr['c_name']]['id'];
                }
            }
            if(!empty($rel))
            $rel = array_values(array_unique($rel));
            $relInsertQuery = 'INSERT INTO cmp_professions_cmp_crafts (profession_id, craft_id) VALUES ';
            
            for($i = count($rel)-1; $i >= 0 ; $i--){
                $relInsertQuery .= '('.$rel[$i].')';
                if($i != 0){
                    $relInsertQuery .= ', ';
                }
            }
            DB::query(Database::INSERT,$relInsertQuery)->execute();
        }
        
        return $this;
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
     * Возвращает массив [pagination => Pagination, items => [Company,Company,...]]
     * @return array
     */
    public function findAllWithPagination(){
        $object = clone($this);
        $count = $object->count_all();
        $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));
        $pagination = Pagination::factory(array(
                'total_items'    => $count,
                'items_per_page' => 8,
                'view'              => 'pagination/company',
            )
        )
            ->route_params($params);

        return ['pagination' => $pagination, 'items' => $this->limit($pagination->items_per_page)->offset($pagination->offset)->find_all(),'total_items' => $count];
    }

    public function craftsWithProfessionsFlag(){
        return DB::query(Database::SELECT,
            'SELECT cc.id,cc.name,cc.catalog_number,cc.status, IF(cpcc.profession_id IS NULL, 0, 1) belongs_to_profession FROM cmp_crafts cc LEFT JOIN cmp_professions_cmp_crafts cpcc ON cc.id = cpcc.craft_id WHERE cc.company_id = '.$this->id.' GROUP BY cc.id ORDER BY cc.name ASC
')->execute()->as_array();
    }
}