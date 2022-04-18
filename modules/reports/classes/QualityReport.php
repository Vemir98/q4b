<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 29.04.2019
 * Time: 17:24
 */

class QualityReport
{
    const STATUS_EXISTING_AND_FOR_REPAIR = Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair;
    private $_stdColors = [
        '#01fb88',
        '#62358b',
        '#bb030c',
        '#0f1ff1',
        '#4af10f',
        '#d3d108',
        '#af680b',
        '#d515d3',
        '#0caabd',
        '#701be3',
        '#4592af',
        '#b206b0',
        '#8e2e6a',
        '#d61d4e',
        '#ff0b55',
        '#f79c1d',
        '#5588a3',
        '#a9eec2',
        '#7189bf',
        '#df7599',
        '#222831',
        '#393e46',
        '#facf5a',
        '#5ca0d3',
        '#ffc15e',
        '#6f0765',
        '#a1dd70'

    ];
    /**
     * @var ReportQuery
     */
    private $_query;

    /**
     * @var array
     */
    private $_companyID;

    /**
     * @var array[int]
     */
    private $_projectsID;

    /**
     * @var array[int]
     */
    private $_craftsID;

    /**
     * @var array[int]
     */
    private $_objectsID;

    /**
     * @var bool
     */
    private $_specialityDetails = false;

    /**
     * @var int
     */
    private $_from;

    /**
     * @var int
     */
    private $_to;

    private $_monthsTimestamps = [];
    private $_months = [];

    private $_company;
    private $_projects;
    private $_objects;

    private $_companyCrafts = [];

    private $_savedReport = false;


    private $_stats;

    public function __construct(ReportQuery $query = null)
    {
        if($query != null){

            $this->_query = $query;
            $params = $query->getParams();
            $this->_companyID = (int)$params['company'];
            $this->_from = DateTime::createFromFormat('d/m/Y H:i',$params['from'].' 00:00')->getTimestamp();
            $this->_to = DateTime::createFromFormat('d/m/Y H:i',$params['to'].' 00:00')->getTimestamp();
            $this->_monthsTimestamps = $this->getMonthsTimestamps($this->_to);

            foreach ($this->_monthsTimestamps as $ts){
                $this->_months[] = '"'.Date('d/m/y',$ts).'"';
            }

            $this->_stats['_monthsTimestamps'] = $this->_monthsTimestamps;
            $this->_stats['_months'] = $this->_months;

            if(!empty($params['projects'])){
                $this->_projectsID = $params['projects'];
                if(is_array($params['projects'])){
                    array_walk($this->_projectsID, function(&$x){$x = intval($x);});
                }else{
                    $this->_projectsID = [(int)$this->_projectsID];
                }
            }
            if(!empty($params['crafts'])){
                $this->_craftsID = $params['crafts'];
                if(is_array($params['crafts'])){
                    array_walk($this->_craftsID, function(&$x){$x = intval($x);});
                }else{
                    $this->_craftsID = [(int)$this->_craftsID];
                }
            }
            if(count($this->_projectsID) == 1 AND !empty($params['objects'])){
                $this->_objectsID = $params['objects'];
                if(is_array($params['objects'])){
                    array_walk($this->_objectsID, function(&$x){$x = intval($x);});
                }else{
                    $this->_objectsID = [(int)$this->_objectsID];
                }
            }

            if(! empty($params['speciality_details']) AND (bool)$params['speciality_details']){
                $this->_specialityDetails = true;
            }
        }

    }

    public function isSaved(){
        return $this->_savedReport;
    }

    public function loadSavedReport($stats){
        $this->_stats = $stats;
        $this->_savedReport = true;
        $this->_specialityDetails = $this->_stats['data']['specialityDetails'];
        $this->_monthsTimestamps = $this->_stats['_monthsTimestamps'];
        $this->_months = $this->_stats['_months'];
        return $this;
    }

    public function generate(){
        if($this->_savedReport) return;
        $this->_company = ORM::factory('Company',$this->_companyID);
        if( ! $this->_company->loaded()){
            throw new HDVP_Exception('Incorrect Company');
        }

        $this->_projects = $this->_company->projects->where('id','IN',DB::expr('('.implode(',',$this->_projectsID).')'))->find_all();

        if(count($this->_projects) == 1){
            $this->_objects = $this->_projects[0]->objects->where('id','IN',DB::expr('('.implode(',',$this->_objectsID).')'))->find_all();
        }

        $this->_companyCrafts = $this->_company->crafts->where('status','=',Enum_Status::Enabled)->and_where('id','IN',DB::expr('('.implode(',',$this->_craftsID).')'))->find_all();

        $this->_stats['colors'] = [
            Enum_QualityControlStatus::Existing => '#28cf91',
            self::STATUS_EXISTING_AND_FOR_REPAIR => '#d515d3',
            Enum_QualityControlStatus::Normal => '#005c87',
            Enum_QualityControlStatus::Repaired => '#f99c19',
            Enum_QualityControlStatus::Invalid => '#ff0000',
        ];

        if(count($this->_projectsID) > 1){
            $this->generateForProjects();
        }else{
            $this->generateForObjects();
        }

        $this->_stats['data']['objects'] = $this->_stats['data']['projects'] = $this->_stats['data']['company'] = $this->_stats['data']['cmpCrafts']= [];

        $this->_stats['data']['company'] = [
            'id' => $this->_company->id,
            'name' => $this->_company->name,
            'logo' => $this->_company->logo
        ];

        foreach ($this->_projects as $prj){
            $this->_stats['data']['projects'][$prj->id] = [
                'id' => $prj->id,
                'name' => $prj->name,
                'generalOpinion' => ''
            ];
        }

        foreach ($this->_companyCrafts as $crft){
            $this->_stats['data']['cmpCrafts'][$crft->id] = [
                'id' => $crft->id,
                'name' => $crft->name
            ];
        }

        if(count($this->_objects)){
            foreach ($this->_objects as $obj){
                $this->_stats['data']['objects'][$obj->id] = [
                    'id' => $obj->id,
                    'name' => $obj->name,
                    'generalOpinion' => ''
                ];
            }
        }

        $this->_stats['data']['from'] = $this->_from;
        $this->_stats['data']['to'] = $this->_to;
        $this->_stats['data']['specialityDetails'] = $this->_specialityDetails;

        $this->generateYearStats();

    }

    protected function craftsQueryPiece($ORMQuery){
        if(count($this->_craftsID) > 1){
            $ORMQuery->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$this->_craftsID).')'));
        }else{
            $ORMQuery->and_where('qualitycontrol.craft_id','=',(int)$this->_craftsID[0]);
        }
        return $ORMQuery;
    }

    protected function placesQueryPiece($ORMQuery){
        if(count($this->_projectsID) > 1){
            $ORMQuery->where('qualitycontrol.project_id', 'IN', DB::expr('('.implode(',',$this->_projectsID).')'));
        }else{
            $ORMQuery->where('qualitycontrol.project_id','=',(int)$this->_projectsID[0]);
        }

        if(count($this->_objectsID)){
            if(count($this->_objectsID) > 1){
                $ORMQuery->where('qualitycontrol.object_id', 'IN', DB::expr('('.implode(',',$this->_objectsID).')'));
            }else{
                $ORMQuery->where('qualitycontrol.object_id','=',(int)$this->_objectsID[0]);
            }
        }
        return $ORMQuery;
    }

    protected function placesQueryPieceForProject($projectID,$ORMQuery){
        $ORMQuery->where('qualitycontrol.project_id','=',(int)$projectID);
        return $ORMQuery;
    }

    protected function placesQueryPieceForObject($objectID,$ORMQuery){
        $ORMQuery->where('qualitycontrol.object_id','=',(int)$objectID);
        return $ORMQuery;
    }

    private function generateForProjects(){

        $this->_stats['total']['statuses'] = [
            Enum_QualityControlStatus::Existing => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
            self::STATUS_EXISTING_AND_FOR_REPAIR => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
            Enum_QualityControlStatus::Normal => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
            Enum_QualityControlStatus::Repaired => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
            Enum_QualityControlStatus::Invalid => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
        ];

        $this->_stats['filtered']['statuses'] = [
            Enum_QualityControlStatus::Existing => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            self::STATUS_EXISTING_AND_FOR_REPAIR => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)),
            Enum_QualityControlStatus::Normal => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            Enum_QualityControlStatus::Repaired => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            Enum_QualityControlStatus::Invalid => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
        ];

        foreach ($this->_stats['filtered'] as $k => $vArr){
            foreach ($vArr as $k1 => $q){
                $this->_stats['filtered'][$k][$k1]->join('pr_floors','INNER');
                $this->_stats['filtered'][$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
                $this->_stats['filtered'][$k][$k1]->join('pr_places','INNER');
                $this->_stats['filtered'][$k][$k1]->on('qualitycontrol.place_id','=','pr_places.id');
                $this->_stats['filtered'][$k][$k1] = $this->_stats['filtered'][$k][$k1]->count_all();
            }
        }

        $this->_stats['total'] = $this->calculateStats($this->_stats['total'], 'root');
        $this->_stats['filtered'] = $this->calculateStats($this->_stats['filtered'], 'root');


        //for projects
        if(count($this->_projectsID) > 0){
            foreach ($this->_projectsID as $projectID){

                $this->_stats['projects'][$projectID]['color'] = $this->getColor();
                $this->_stats['projects'][$projectID]['total']['statuses'] = [
                    Enum_QualityControlStatus::Existing => $this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
                    Enum_QualityControlStatus::Normal => $this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
                    Enum_QualityControlStatus::Repaired => $this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
                    Enum_QualityControlStatus::Invalid => $this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => $this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                ];

                $this->_stats['projects'][$projectID]['filtered']['statuses'] = [
                    Enum_QualityControlStatus::Existing => $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Normal => $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Repaired => $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Invalid => $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)),
                ];

                foreach ($this->_stats['projects'][$projectID]['filtered'] as $k => $vArr){
                    foreach ($vArr as $k1 => $q){
                        $this->_stats['projects'][$projectID]['filtered'][$k][$k1]->join('pr_floors','INNER');
                        $this->_stats['projects'][$projectID]['filtered'][$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
                        $this->_stats['projects'][$projectID]['filtered'][$k][$k1]->join('pr_places','INNER');
                        $this->_stats['projects'][$projectID]['filtered'][$k][$k1]->on('qualitycontrol.place_id','=','pr_places.id');
                        $this->_stats['projects'][$projectID]['filtered'][$k][$k1] = $this->_stats['projects'][$projectID]['filtered'][$k][$k1]->count_all();
                    }
                }

                $this->_stats['projects'][$projectID]['total'] = $this->calculateStats($this->_stats['projects'][$projectID]['total'], 'projects');
                $this->_stats['projects'][$projectID]['filtered'] = $this->calculateStats($this->_stats['projects'][$projectID]['filtered'], 'projects');


                $this->_stats['projects'][$projectID]['crafts'] = DB::query(Database::SELECT,'SELECT cc.id, cc.name, count(craft_id) `count` FROM quality_controls qc JOIN cmp_crafts cc ON qc.craft_id = cc.id WHERE qc.project_id = '.$projectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$this->_companyID.' GROUP BY qc.craft_id')->execute()->as_array('id');
                $this->_stats['projects'][$projectID]['filteredCrafts'] = DB::query(Database::SELECT,'SELECT cc.id, cc.name, count(craft_id) `count` FROM quality_controls qc JOIN cmp_crafts cc ON qc.craft_id = cc.id WHERE qc.project_id = '.$projectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$this->_companyID.' GROUP BY qc.craft_id')->execute()->as_array('id');
                $this->_stats['projects'][$projectID]['defects'] = [
                    'not_compatible_with_craft' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'not_standard' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'does_not_match' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'not_compatible_with_plan' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                ];
                $defects = DB::query(Database::SELECT,'SELECT severity_level, condition_list FROM quality_controls qc WHERE project_id='.$projectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND status ="'.Enum_QualityControlStatus::Invalid.'"')->execute()->as_array();
                if(count($defects)){
                    foreach ($defects as $defect){
                        if(empty($defect['severity_level']) or empty($defect['condition_list'])) continue;
                        if(isset($this->_stats['projects'][$projectID]['defects'][$defect['severity_level']][$defect['condition_list']])){
                            $this->_stats['projects'][$projectID]['defects'][$defect['severity_level']][$defect['condition_list']]++;
                        }
                    }
                }

                $this->calculateDefects($this->_stats['projects'][$projectID]['defects']);

                //speciality details
                if($this->_specialityDetails){
                    foreach ($this->_craftsID as $craftID){
                        $this->_stats['projects'][$projectID]['craftDefects'][$craftID] = [
                            'not_compatible_with_craft' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'not_standard' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'does_not_match' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'not_compatible_with_plan' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                        ];

                        $defects = DB::query(Database::SELECT,'SELECT severity_level, condition_list FROM quality_controls qc WHERE project_id='.$projectID.' AND craft_id='.$craftID.' AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND status="'.Enum_QualityControlStatus::Invalid.'"')->execute()->as_array();
                        if(count($defects)){
                            foreach ($defects as $defect){
                                if(empty($defect['severity_level']) or empty($defect['condition_list'])) continue;
                                if(isset($this->_stats['projects'][$projectID]['craftDefects'][$craftID][$defect['severity_level']][$defect['condition_list']])){
                                    $this->_stats['projects'][$projectID]['craftDefects'][$craftID][$defect['severity_level']][$defect['condition_list']]++;
                                }
                            }
                        }


                    }
                    foreach ($this->_stats['projects'][$projectID]['craftDefects'] as $craftID => $val){
                        $this->calculateDefects($this->_stats['projects'][$projectID]['craftDefects'][$craftID]);
                    }
                }
            }
        }

    }

    private function calculateStats($data, $from){
        $totalStatuses = array_sum($data['statuses']);
        $data['percents'] = [
            Enum_QualityControlStatus::Existing => $totalStatuses ? round($data['statuses'][Enum_QualityControlStatus::Existing] * 100 / $totalStatuses) : 0,
            Enum_QualityControlStatus::Normal => $totalStatuses ? round($data['statuses'][Enum_QualityControlStatus::Normal] * 100 / $totalStatuses) : 0,
            Enum_QualityControlStatus::Repaired => $totalStatuses ? round($data['statuses'][Enum_QualityControlStatus::Repaired] * 100 / $totalStatuses) : 0,
            Enum_QualityControlStatus::Invalid => $totalStatuses ? round($data['statuses'][Enum_QualityControlStatus::Invalid] * 100 / $totalStatuses) : 0,
            QualityReport::STATUS_EXISTING_AND_FOR_REPAIR => $totalStatuses ? round($data['statuses'][QualityReport::STATUS_EXISTING_AND_FOR_REPAIR] * 100 / $totalStatuses) : 0,
        ];


        $data['statuses']['a'] = ($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal]);
        $data['statuses']['b'] = ($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal] + $data['statuses'][Enum_QualityControlStatus::Repaired]);

        $data['percents']['a'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal])) * 100 / $totalStatuses) : 0;
        $data['percents']['b'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal] + $data['statuses'][Enum_QualityControlStatus::Repaired])) * 100 / $totalStatuses) : 0;


        if($from !== 'root') {
            $data['statuses']['a'] -= $data['statuses'][QualityReport::STATUS_EXISTING_AND_FOR_REPAIR];
            $data['statuses']['b'] -= $data['statuses'][QualityReport::STATUS_EXISTING_AND_FOR_REPAIR];

            $data['percents']['a'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal]) - $data['statuses'][QualityReport::STATUS_EXISTING_AND_FOR_REPAIR]) * 100 / $totalStatuses) : 0;
            $data['percents']['b'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal] + $data['statuses'][Enum_QualityControlStatus::Repaired]) - $data['statuses'][QualityReport::STATUS_EXISTING_AND_FOR_REPAIR]) * 100 / $totalStatuses) : 0;
        }

        $data['statuses']['fixed'] = $data['statuses'][Enum_QualityControlStatus::Repaired];
        $data['percents']['fixed'] = round($data['statuses'][Enum_QualityControlStatus::Repaired] * 100 / ($data['statuses'][Enum_QualityControlStatus::Repaired] + $this->_stats['total']['statuses'][Enum_QualityControlStatus::Invalid]));

//        $data['statuses']['a'] = ($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal]);
//        $data['statuses']['b'] = ($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal] + $data['statuses'][Enum_QualityControlStatus::Repaired]);
//        $data['statuses']['fixed'] = $data['statuses'][Enum_QualityControlStatus::Repaired];
//
//        $data['percents']['a'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal])) * 100 / $totalStatuses) : 0;
//        $data['percents']['b'] = $totalStatuses ? round((($data['statuses'][Enum_QualityControlStatus::Existing] + $data['statuses'][Enum_QualityControlStatus::Normal] + $data['statuses'][Enum_QualityControlStatus::Repaired])) * 100 / $totalStatuses) : 0;
//        $data['percents']['fixed'] = round($data['statuses'][Enum_QualityControlStatus::Repaired] * 100 / ($data['statuses'][Enum_QualityControlStatus::Repaired] + $this->_stats['total']['statuses'][Enum_QualityControlStatus::Invalid]));
        return $data;
    }

    private function getColor()
    {
        if(count($this->_stdColors)){
            $color = $this->_stdColors[0];
            unset($this->_stdColors[0]);
            if(count($this->_stdColors))
                $this->_stdColors = array_values($this->_stdColors);
            return $color;
        }


        $color = '#';
        $colorHexLighter = array("9","A","B","C","D","E","F" );
        shuffle($colorHexLighter);
        for($x=0; $x < 6; $x++) {
            $color .= $colorHexLighter[array_rand($colorHexLighter, 1)];
        }
        return substr($color, 0, 7);
    }

    private function generateForObjects(){

        $this->_stats['total']['statuses'] = [
            Enum_QualityControlStatus::Existing => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
            self::STATUS_EXISTING_AND_FOR_REPAIR => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
            Enum_QualityControlStatus::Normal => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
            Enum_QualityControlStatus::Repaired => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
            Enum_QualityControlStatus::Invalid => $this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
        ];

        $this->_stats['filtered']['statuses'] = [
            Enum_QualityControlStatus::Existing => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            self::STATUS_EXISTING_AND_FOR_REPAIR => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)),
            Enum_QualityControlStatus::Normal => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            Enum_QualityControlStatus::Repaired => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
            Enum_QualityControlStatus::Invalid => $this->craftsQueryPiece($this->placesQueryPiece(ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
        ];

        foreach ($this->_stats['filtered'] as $k => $vArr){
            foreach ($vArr as $k1 => $q){
                $this->_stats['filtered'][$k][$k1]->join('pr_floors','INNER');
                $this->_stats['filtered'][$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
                $this->_stats['filtered'][$k][$k1]->join('pr_places','INNER');
                $this->_stats['filtered'][$k][$k1]->on('qualitycontrol.place_id','=','pr_places.id');
                $this->_stats['filtered'][$k][$k1] = $this->_stats['filtered'][$k][$k1]->count_all();
            }
        }

        $this->_stats['total'] = $this->calculateStats($this->_stats['total'], 'root');
        $this->_stats['filtered'] = $this->calculateStats($this->_stats['filtered'], 'root');


        //for objects
        if(count($this->_objectsID) > 0){
            foreach ($this->_objectsID as $objectID){

                $this->_stats['objects'][$objectID]['color'] = $this->getColor();
                $this->_stats['objects'][$objectID]['total']['statuses'] = [
                    Enum_QualityControlStatus::Existing => $this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
                    Enum_QualityControlStatus::Normal => $this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
                    Enum_QualityControlStatus::Repaired => $this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
                    Enum_QualityControlStatus::Invalid => $this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => $this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                ];

                $this->_stats['objects'][$objectID]['filtered']['statuses'] = [
                    Enum_QualityControlStatus::Existing => $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Normal => $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Repaired => $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    Enum_QualityControlStatus::Invalid => $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($this->_from.' AND '.$this->_to))->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)),
                ];

                foreach ($this->_stats['objects'][$objectID]['filtered'] as $k => $vArr){
                    foreach ($vArr as $k1 => $q){
                        $this->_stats['objects'][$objectID]['filtered'][$k][$k1]->join('pr_floors','INNER');
                        $this->_stats['objects'][$objectID]['filtered'][$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
                        $this->_stats['objects'][$objectID]['filtered'][$k][$k1]->join('pr_places','INNER');
                        $this->_stats['objects'][$objectID]['filtered'][$k][$k1]->on('qualitycontrol.place_id','=','pr_places.id');
                        $this->_stats['objects'][$objectID]['filtered'][$k][$k1] = $this->_stats['objects'][$objectID]['filtered'][$k][$k1]->count_all();
                    }
                }

                $this->_stats['objects'][$objectID]['total'] = $this->calculateStats($this->_stats['objects'][$objectID]['total'], 'objects');
                $this->_stats['objects'][$objectID]['filtered'] = $this->calculateStats($this->_stats['objects'][$objectID]['filtered'], 'objects');


                $this->_stats['objects'][$objectID]['crafts'] = DB::query(Database::SELECT,'SELECT cc.id, cc.name, count(craft_id) `count` FROM quality_controls qc JOIN cmp_crafts cc ON qc.craft_id = cc.id WHERE qc.object_id = '.$objectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$this->_companyID.' GROUP BY qc.craft_id')->execute()->as_array('id');
                $this->_stats['objects'][$objectID]['filteredCrafts'] = DB::query(Database::SELECT,'SELECT cc.id, cc.name, count(craft_id) `count` FROM quality_controls qc JOIN cmp_crafts cc ON qc.craft_id = cc.id WHERE qc.object_id = '.$objectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$this->_companyID.' GROUP BY qc.craft_id')->execute()->as_array('id');
                $this->_stats['objects'][$objectID]['defects'] = [
                    'not_compatible_with_craft' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'not_standard' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'does_not_match' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                    'not_compatible_with_plan' => [
                        'for_immediate_treatment' => 0,
                        'do_not_go_th_stage' => 0,
                        'do_not_prc_without_suv_approve' => 0,
                        'compliance_of_materials' => 0,
                    ],
                ];
                $defects = DB::query(Database::SELECT,'SELECT severity_level, condition_list FROM quality_controls qc WHERE object_id='.$objectID.' AND qc.craft_id IN ('.implode(',',$this->_craftsID).') AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND status ="'.Enum_QualityControlStatus::Invalid.'"')->execute()->as_array();
                if(count($defects)){
                    foreach ($defects as $defect){
                        if(empty($defect['severity_level']) or empty($defect['condition_list'])) continue;
                        if(isset($this->_stats['objects'][$objectID]['defects'][$defect['severity_level']][$defect['condition_list']])){
                            $this->_stats['objects'][$objectID]['defects'][$defect['severity_level']][$defect['condition_list']]++;
                        }
                    }
                }

                $this->calculateDefects($this->_stats['objects'][$objectID]['defects']);

                //speciality details
                if($this->_specialityDetails){
                    foreach ($this->_craftsID as $craftID){
                        $this->_stats['objects'][$objectID]['craftDefects'][$craftID] = [
                            'not_compatible_with_craft' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'not_standard' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'does_not_match' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                            'not_compatible_with_plan' => [
                                'for_immediate_treatment' => 0,
                                'do_not_go_th_stage' => 0,
                                'do_not_prc_without_suv_approve' => 0,
                                'compliance_of_materials' => 0,
                            ],
                        ];

                        $defects = DB::query(Database::SELECT,'SELECT severity_level, condition_list FROM quality_controls qc WHERE object_id='.$objectID.' AND craft_id='.$craftID.' AND (qc.updated_at BETWEEN '.$this->_from.' AND '.$this->_to.') AND status ="'.Enum_QualityControlStatus::Invalid.'"')->execute()->as_array();
                        if(count($defects)){
                            foreach ($defects as $defect){
                                if(empty($defect['severity_level']) or empty($defect['condition_list'])) continue;
                                if(isset($this->_stats['objects'][$objectID]['craftDefects'][$craftID][$defect['severity_level']][$defect['condition_list']])){
                                    $this->_stats['objects'][$objectID]['craftDefects'][$craftID][$defect['severity_level']][$defect['condition_list']]++;
                                }
                            }
                        }
                    }

                    foreach ($this->_stats['objects'][$objectID]['craftDefects'] as $craftID => $val){
                        $this->calculateDefects($this->_stats['objects'][$objectID]['craftDefects'][$craftID]);
                    }

                }
            }
        }

    }

    public function calculateDefects(&$data){
        $total = [
            'for_immediate_treatment' => 0,
            'do_not_go_th_stage' => 0,
            'do_not_prc_without_suv_approve' => 0,
            'compliance_of_materials' => 0,
        ];
        foreach ($data as $key => $val){
            foreach (Enum_QualityControlConditionList::toArray() as $key1){
                $total[$key1] += $data[$key][$key1];
            }
        }

//        foreach ($data as $key => $val){
//            foreach (Enum_QualityControlConditionList::toArray() as $key1){
//                $data[$key][$key1] = ($total[$key1] > 0 AND $data[$key][$key1] > 0) ? (round($data[$key][$key1] * 100 / $total[$key1]).'%') : 0;
//            }
//        }

        $data['total'] = $total;
    }

    public function specHasResult($craftData){
        $output = 0;
        foreach ($craftData as $cd){
            $output+= $cd;
        }
        return (bool)$output;
    }

    public function getStats(){
        return $this->_stats;
    }

    public function getCompany(){
        return $this->_stats['data']['company'];
    }

    public function getCompanyCrafts(){
        return $this->_stats['data']['cmpCrafts'];
    }

    public function getProjects(){
        return $this->_stats['data']['projects'];
    }

    public function getProjectORObjectName($type,$id){
        if(strtolower($type) == 'projects'){
            foreach ($this->getProjects() as $p){
                if($p['id'] == $id) return $p['name'];
            }
        }else{
            foreach ($this->getObjects() as $o){
                if($o['id'] == $id) return $o['name'];
            }
        }

        return 'NO NAME';

    }

    public function getProjectsORObjects($type){
        if(strtolower($type) == 'projects'){
            return $this->getProjects();
        }else{
            return $this->getObjects();
        }

    }

    public function getObjects(){
        return $this->_stats['data']['objects'];
    }

    public function hasSpecialityDetails(){
        return $this->_stats['data']['specialityDetails'];
    }

    public function getDateFrom($normalized = false){
        if($normalized){
            return date('d-m-Y',$this->_stats['data']['from']);
        }else{
            return $this->_stats['data']['from'];
        }
    }

    public function getDateTo($normalized = false){
        if($normalized){
            return date('d-m-Y',$this->_stats['data']['to']);
        }else{
            return $this->_stats['data']['to'];
        }
    }

    public function renderPieChartTotal($id){
        return View::make('reports/quality/piechart-js',['report' => $this, 'type' => 'total', 'id' => $id])->render();
    }

    public function renderPieChartFiltered($id){
        return View::make('reports/quality/piechart-js',['report' => $this, 'type' => 'filtered', 'id' => $id])->render();
    }

    public function renderBarChart($id,$type){
        return View::make('reports/quality/barchart-js',['report' => $this, 'type' => $type, 'id' => $id])->render();
    }
    public function renderLineChart($id,$type){
        return View::make('reports/quality/linechart-js',['report' => $this, 'type' => $type, 'id' => $id, 'months' => implode(',',$this->_months)])->render();
    }

    public function renderEntityLineChart($id,$type,$entityId){
        return View::make('reports/quality/entity-linechart-js',['report' => $this, 'type' => $type, 'id' => $id, 'months' => implode(',',$this->_months), 'entityId' => $entityId])->render();
    }


    public function getCraftsID(){
        return $this->_craftsID;
    }

    public function getMonthsTimestamps($timestamp, $direction = '-'){
        $date = Date('Y-m',$timestamp);
        $i = -1;
        $output = [];
        while($i++ < 10){
            if($direction == '+'){
                $output[] = strtotime(Date('Y-m-d H:i:s',strtotime($date. ' +'.$i.' Month')));
            }else{
                $output[] = strtotime(Date('Y-m-d H:i:s',strtotime($date. ' -'.$i.' Month')));
            }
        }

        return $direction == '-' ? array_reverse($output) : $output;
    }

    public function generateYearStats(){
        if(count($this->_projectsID) > 1){
            //for projects
            foreach ($this->_projectsID as $projectID){
                foreach ($this->_monthsTimestamps as $key => $ts){
                    $prevMonthTs = strtotime(Date('Y-m',$ts).' - 1 Month');
                    $totalQcs = $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))->count_all();
                    $this->_stats['projects'][$projectID]['yearFkk']['a+b'][$key] = $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))
                        ->and_where_open()
                        ->where('status','=',Enum_QualityControlStatus::Existing)
                        ->or_where('status','=',Enum_QualityControlStatus::Normal)
                        ->and_where_close()
                        ->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))
                        //->and_where('qualitycontrol.updated_at','<=',DB::expr($ts)))
                        ->count_all();
                    $this->_stats['projects'][$projectID]['yearFkk']['a+b'][$key] = $this->_stats['projects'][$projectID]['yearFkk']['a+b'][$key] > 0 ? round($this->_stats['projects'][$projectID]['yearFkk']['a+b'][$key] * 100 / $totalQcs) : 0;
                    $this->_stats['projects'][$projectID]['yearFkk']['a+b+fixed'][$key] = $this->craftsQueryPiece($this->placesQueryPieceForProject($projectID, ORM::factory('QualityControl'))
                        ->and_where_open()
                        ->where('status','=',Enum_QualityControlStatus::Existing)
                        ->or_where('status','=',Enum_QualityControlStatus::Normal)
                        ->or_where('status','=',Enum_QualityControlStatus::Repaired)
                        ->and_where_close()
                        ->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))
//                        ->and_where('qualitycontrol.updated_at','<=',DB::expr($ts)))
                        ->count_all();
                    $this->_stats['projects'][$projectID]['yearFkk']['a+b+fixed'][$key] = $this->_stats['projects'][$projectID]['yearFkk']['a+b+fixed'][$key] > 0 ? round($this->_stats['projects'][$projectID]['yearFkk']['a+b+fixed'][$key] * 100 / $totalQcs) : 0;
                }

            }
        }else{
            //for objects
            if(count($this->_objectsID) > 0) {
                foreach ($this->_objectsID as $objectID) {
                    foreach ($this->_monthsTimestamps as $key => $ts){
                        $prevMonthTs = strtotime(Date('Y-m',$ts).' - 1 Month');
                        $totalQcs = $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))->count_all();
                        $this->_stats['objects'][$objectID]['yearFkk']['a+b'][$key] = $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))
                            ->and_where_open()
                            ->where('status','=',Enum_QualityControlStatus::Existing)
                            ->or_where('status','=',Enum_QualityControlStatus::Normal)
                            ->and_where_close()
                            ->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))
//                            ->and_where('qualitycontrol.updated_at','<=',DB::expr($ts)))
                            ->count_all();
                        $this->_stats['objects'][$objectID]['yearFkk']['a+b'][$key] = $this->_stats['objects'][$objectID]['yearFkk']['a+b'][$key] > 0 ? round($this->_stats['objects'][$objectID]['yearFkk']['a+b'][$key] * 100 / $totalQcs) : 0;
                        $this->_stats['objects'][$objectID]['yearFkk']['a+b+fixed'][$key] = $this->craftsQueryPiece($this->placesQueryPieceForObject($objectID, ORM::factory('QualityControl'))
                            ->and_where_open()
                            ->where('status','=',Enum_QualityControlStatus::Existing)
                            ->or_where('status','=',Enum_QualityControlStatus::Normal)
                            ->or_where('status','=',Enum_QualityControlStatus::Repaired)
                            ->and_where_close()
                            ->and_where('qualitycontrol.updated_at','BETWEEN',DB::expr($prevMonthTs.' AND '.$ts)))
//                            ->and_where('qualitycontrol.updated_at','<=',DB::expr($ts)))
                            ->count_all();
                        $this->_stats['objects'][$objectID]['yearFkk']['a+b+fixed'][$key] = $this->_stats['objects'][$objectID]['yearFkk']['a+b+fixed'][$key] > 0 ? round($this->_stats['objects'][$objectID]['yearFkk']['a+b+fixed'][$key] * 100 / $totalQcs) : 0;
                    }
                }
            }
        }
    }
    public function canDisplayYearStats(){

        if(isset($this->_stats['objects'])){
            foreach ($this->_stats['objects'] as $key => $obj){
                if(isset($this->_stats['objects'][$key]['yearFkk'])){
                    return true;
                }
                return false;
            }
        }
        if(isset($this->_stats['projects'])){
            foreach ($this->_stats['projects'] as $key => $proj){
                if(isset($this->_stats['projects'][$key]['yearFkk'])){
                    return true;
                }
                return false;
            }
        }
        return false;
    }
}