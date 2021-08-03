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
//    protected $_actions_perms = [
//        'index' => [
//            'GET' => 'read',
//        ],
//        'generate' => [
//            'POST' => 'read',
//        ],
//    ];

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
        VueJs::instance()->addComponent('reports/approve-element-reports/generate-reports');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();

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
            "approve_element" => __('Approve element')
        ];

        $this->template->content = View::make('reports/approve-element/generate-reports', ['translations' => $translations]);
    }

    public function action_generate(){

        VueJs::instance()->addComponent('reports/approve-element-reports/reports-list');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();

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
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'speciality_name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'test__active',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'speciality_name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'test__active2',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123423', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123524', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123225', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123726', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123827', 'description' => 'test__taskDescription5'],
                                ],
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
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'speciality_name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'test__active',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'speciality_name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'test__active2',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123423', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123524', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123225', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123726', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123827', 'description' => 'test__taskDescription5'],
                                ],
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
                        'specialities' => [
                            [
                                'id' => 'test__111',
                                'speciality_name' => 'test__someSpecialityName1',
                                'approval_date' => 'test__07/05/2021',
                                'status' => 'test__active',
                                'position' => 'test__position',
                                'signer_name' => 'test__SingerName1',
                                'signature' => 'test__some/image/url/1',
                                'updated_by' => 'test__ExampleName1',
                                'description' => 'test__Some short descrpition1',
                                'tasks' => [
                                    ['id' => 'test__123123', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123124', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123125', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123126', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123127', 'description' => 'test__taskDescription5'],
                                ]
                            ],
                            [
                                'id' => 'test__112',
                                'speciality_name' => 'test__someSpecialityName2',
                                'approval_date' => 'test__01/01/2021',
                                'status' => 'test__active2',
                                'position' => 'test__position2',
                                'signer_name' => 'test__SingerName2',
                                'signature' => 'test__some/image/url/2',
                                'updated_by' => 'test__ExampleName2',
                                'description' => 'test__Some short descrpition2',
                                'tasks' => [
                                    ['id' => 'test__123423', 'description' => 'test__taskDescription1'],
                                    ['id' => 'test__123524', 'description' => 'test__taskDescription2'],
                                    ['id' => 'test__123225', 'description' => 'test__taskDescription3'],
                                    ['id' => 'test__123726', 'description' => 'test__taskDescription4'],
                                    ['id' => 'test__123827', 'description' => 'test__taskDescription5'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        $translations = [
            "project_name" => __('Project name'),
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
            "export" => __('Export'),
            "element" => __('Element_item'),
            "craft" => __('Craft'),
            "floor" => __('Floor'),
            "status" => __('status'),
            "position" => __('Position'),
            "signature" => __('Signature'),
            "more" => __('More'),
            "qc_report" => __('QC Report')
        ];

        $this->template->content = View::make('reports/approve-element/reports-list', [
            'data' => $responseData, 'translations' => $translations
            ]
        );

    }

}