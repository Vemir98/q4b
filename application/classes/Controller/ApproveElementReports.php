<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.03.2019
 * Time: 13:26
 */
use JonnyW\PhantomJs\Client;
class Controller_ApproveElementReports extends HDVP_Controller_Template
{
    public function before()
    {
        parent::before();
//        if ($this->auto_render === TRUE)
//        {
//            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
//            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Delivery report'))->set_url('/reports/delivery'));
//        }
    }

    public function action_index()
    {
        VueJs::instance()->addComponent('reports/approve-element-reports/approve-elements-tab');
        VueJs::instance()->addComponent('reports/approve-element-reports/generate-reports');
        VueJs::instance()->addComponent('reports/approve-element-reports/reports-list');
        VueJs::instance()->addComponent('reports/approve-element-reports/report-item');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();
        VueJs::instance()->includeSignaturePad();

        $responseData = [
                'company_name' => 'test__CompanyName',
                'project_name' => 'test__ProjectName',
                'owner' => 'test__Owner',
                'start_date' => 'test__12/03/2020',
                'end_date' => 'test__18/08/2021',
                'project_id' => 'test__52',
                'project_status' => 'test__active',
                'address' => 'test__address',
                'structures_quantity' => 'test__10',
                'report_range' => 'test__01/02/2021-15/05/2021',
                'reports' => [
                    [
                        'id' => 'test__10254',
                        'check_date' => 'test__05/05/2021',
                        'element_name' => 'test__ElementName',
                        'floor' => 'test__2',
                        'status' => 'test__active',
                        'show_specialities' => false,
                        'show_options' => false,
                        'creator' => 'Vemir',
                        'structure' => 'test__Structure1',
                        'place' => 'test__place1',
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'Not appropriate',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__12313', 'description' => 'test__taskDescription1', 'status' => 'enabled'],
                                    ['id' => 'test__12324', 'description' => 'test__taskDescription2', 'status' => 'disabled'],
                                    ['id' => 'test__12312', 'description' => 'test__taskDescription3', 'status' => 'enabled'],
                                    ['id' => 'test__12326', 'description' => 'test__taskDescription4', 'status' => 'enabled'],
                                    ['id' => 'test__12327', 'description' => 'test__taskDescription5', 'status' => 'enabled'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'Appropriate',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1', 'status' => 'enabled'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2', 'status' => 'disabled'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3', 'status' => 'disabled'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4', 'status' => 'enabled'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5', 'status' => 'disabled'],
                                ]
                            ],
                        ],
                    ],
                    [
                        'id' => 'test__102521',
                        'check_date' => 'test__05/05/2021',
                        'element_name' => 'test__ElementName',
                        'floor' => 'test__2',
                        'status' => 'test__active',
                        'show_specialities' => false,
                        'show_options' => false,
                        'creator' => 'Vemir2',
                        'structure' => 'test__Structure1',
                        'place' => 'test__place1',
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'Appropriate',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__12323', 'description' => 'test__taskDescription1', 'status' => 'disabled'],
                                    ['id' => 'test__13124', 'description' => 'test__taskDescription2', 'status' => 'enabled'],
                                    ['id' => 'test__12315', 'description' => 'test__taskDescription3', 'status' => 'enabled'],
                                    ['id' => 'test__12326', 'description' => 'test__taskDescription4', 'status' => 'enabled'],
                                    ['id' => 'test__12317', 'description' => 'test__taskDescription5', 'status' => 'disabled'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'Appropriate',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1', 'status' => 'enabled'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2', 'status' => 'disabled'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3', 'status' => 'disabled'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4', 'status' => 'disabled'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5', 'status' => 'disabled'],
                                ]
                            ],
                        ],
                    ],
                    [
                        'id' => 'test__10254',
                        'check_date' => 'test__05/05/2021',
                        'element_name' => 'test__ElementName',
                        'floor' => 'test__2',
                        'status' => 'test__active',
                        'show_specialities' => false,
                        'show_options' => false,
                        'creator' => 'Vemir',
                        'structure' => 'test__Structure1',
                        'place' => 'test__place1',
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'Not appropriate',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1', 'status' => 'enabled'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2', 'status' => 'enabled'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3', 'status' => 'enabled'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4', 'status' => 'enabled'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5', 'status' => 'disabled'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'Not appropriate',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1', 'status' => 'enable'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2', 'status' => 'disabled'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3', 'status' => 'enabled'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4', 'status' => 'enabled'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5', 'status' => 'disabled'],
                                ]
                            ],
                        ],
                    ],
                ],
            ];

        $translations = [
            "project_name" => __('Project name'),
            "search" => __('Search'),
            "select_status" => __('Select_status'),
            "show" => __('Show'),
            "print" => __('Print'),
            "export" => __('Export'),
            "select_structure" => __('Select_structure'),
            "select_company" => __('Select Company'),
            "select_project" => __('Select project'),
            "select_floor_level" => __('Select floor/level'),
            "select_place" => __('Select_place'),
            "select_element" => __('Select element'),
            "select_specialty" => __('Select_specialty'),
            "date" => __('Date'),
            "create_date" => __('Create Date'),
            "structure" => __('Structure'),
            "floor_level" => __('Floor/Level'),
            "element" => __('Element_item'),
            "status" => __('Status'),
            "more" => __('More'),
            "waiting" => __('waiting'),
            "approve" => __('Approved'),
            "non_approve" => __('Not approved'),
            "edit" => __('Edit'),
            "delete" => __('Delete'),
            "select_all" => __('select all'),
            "unselect_all" => __('unselect all'),
            "cancel" => __('Cancel'),
            "are_you_sure" => __('Are you sure, you want'),
            "to_delete" => __('delete'),
            "are_you_sure_to_delete" => __('Are you sure, you want delete this'),
            "company" => __('Company'),
            "project" => __('Project'),
            "places" => __('Places'),
            "crafts" => __('Crafts'),
            "generate" => __('Generate'),
            "company_name" => __('company name'),
            "owner" => __('Owner'),
            "start_date" => __('Start Date'),
            "end_date" => __('End Date'),
            "project_id" => __('Project ID'),
            "project_status" => __('project status'),
            "approve_element" => __('Approve element'),
            "address" => __('Address'),
            "structures_quantity" => __('Quantity of properties'),
            "report_range" => __('Report Range'),
            "craft" => __('Craft'),
            "floor" => __('Floor'),
            "position" => __('Position'),
            "signature" => __('Signature'),
            "qc_report" => __('QC Report'),
            "created_by" => __('Created by'),
            "place" => __('Place'),
            "updated_by" => __('Updated by'),
            "notes" => __('Notes'),
            "tasks" => __('Tasks'),
            "task" => __('Task'),
            "approved_by" => __('Approved by'),
            "manager_status" => __('Manager status'),
            "update" => __('Update'),
            "set_structures" => __('set_structures'),
            "set_floor_level" => __('set_floor_level'),
            "set_places" => __('set_places'),
            "set_elements" => __('set_elements'),
            "set_specialities" => __('set_specialities'),
            "set_statuses" => __('set_statuses'),
            "set_positions" => __('set_positions'),
            "view_qc" => __('view_qc'),
            "speciality_list" => __('speciality_list'),
            "approval_date" => __('approval_date'),
            "signer_name" => __('signer_name'),
            "check_date" => __('check_date'),
            "check_number" => __('check_number')
        ];

        foreach (Api_DBModules::getModulesForTasks() as $module) {
            $translations['module_'.$module['id']] = $module['name'];
        }


        $this->template->content = View::make('reports/approve-element/index', [
            'translations' => $translations,
            'data' => $responseData
        ]);
    }
}