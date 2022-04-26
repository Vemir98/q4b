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
//            'POST' => 'read'
//        ]
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
        VueJs::instance()->addComponent('./confirm-modal');
        VueJs::instance()->addComponent('./q4b-warning-popup');
        VueJs::instance()->addComponent('reports/approve-element-reports/approve-elements-tab');
        VueJs::instance()->addComponent('reports/approve-element-reports/generate-reports');
        VueJs::instance()->addComponent('reports/approve-element-reports/reports-list');
        VueJs::instance()->addComponent('reports/approve-element-reports/report-item');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();
        VueJs::instance()->includeSignaturePad();

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
            "approved" => __('Approved'),
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
            "check_number" => __('check_number'),
            "additional_signature" => __('additional_signature'),
            "userPosition" => __($this->_user->getRelevantRole('name')),
            'appropriate' => __('appropriate'),
            'not_appropriate' => __('not_appropriate'),
            'delete_all' => __('delete_all'),
            'view' => __('view'),
            'please_sign' => __('please_sign'),
            'clear_sign' => __('clear_sign'),
            'sign' => __('sign'),
            'add_signature' => __('add_signature'),
            'positions' => __('positions'),
            'manager_signature' => __('manager_signature'),
            'notes_description' => __('notes_description'),
            'change_status?' => __('change_status?'),
            'primary_supervision' => __('primary_supervision'),
            'partial_process' => __('partial_process'),
            'no_search_results' => __('no_search_results'),
            'close' => __('Close'),
            'certificates_search_placeholder' => __('certificates_search_placeholder'),
            'total' => __('Total'),
            'search_by_element_number' => __('search_by_element_number')
        ];


        $this->template->content = View::make('reports/approve-element/index', [
            'translations' => $translations,
            'userProfession' => $this->_user->professions->find()->name,
            'userRole' =>  $this->_user->getRelevantRole('name'),
            'projectId' => Request::current()->param('projectId')
        ]);
    }

    public function action_create() {
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));

        VueJs::instance()->addComponent('reports/approve-element-reports/create/create');
        VueJs::instance()->addComponent('reports/approve-element-reports/create/report-speciality');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();
        VueJs::instance()->includeSignaturePad();

        $translations = [
            'notes_description' => __('notes_description'),
            "crafts" => __('Crafts'),
            "status" => __('Status'),
            "userPosition" => __($this->_user->getRelevantRole('name')),
            'appropriate' => __('appropriate'),
            'not_appropriate' => __('not_appropriate'),
            'primary_supervision' => __('primary_supervision'),


            'create_lab_control' => __('Create Lab Control'),
            'structure' => __('Structure'),
            'select_structure' => __('Select_structure'),
            'floor' => __('Floor/Level'),
            'select_floor' => __('Select_floor'),
            'place' => __('Place'),
            'select_place' => __('Select_place'),
            'element' => __('Element_item'),
            'select_element' => __('Select element'),
            'craft' => __('Craft'),
            'select_craft' => __('Select_specialty'),
            'sample_number' => __('Sample number (external)'),
            'description' => __('Description'),
            'essence_of_work' => __('Essence of work/standard'),
            'strength_after' => __('Strength after/result'),
            'lab_certificate' => __('Lab certificate'),
            'create_date' => __('Create_Date'),
            'delivery_certificates' => __('Delivery certificates'),
            'add' => __('Add'),
            'attached_files' => __('Attached files'),
            'list_of_files' => __('list_of_files'),
            'uploaded' => __('uploaded'),
            'close' => __('Close'),
            'save' => __('Save'),
            'create' => __('Create'),
            'enter_essence_of_work' => __('Enter essence of work/standard'),
            'enter_certificate_number' => __('Enter certificate number'),
            'enter_delivery_certificate' => __('Enter delivery certificate'),
            'certificate' => __('Certificate'),
            'notes' => __('Notes'),
            'fresh_concrete_strength' => __('Fresh concrete strength'),
            'roll_strength' => __('Roll strength'),
            'amount_of_volume' => __('Amount of volume'),
            'type' => __('Type'),
            'sediment' => __('Sediment'),
            'enter_the_amount_of_volume' => __('Enter the amount of volume'),
            'enter_the_type' => __('Enter the type'),
            'enter_the_sediment' => __('Enter the sediment'),
            'enter_the' => __('Enter the'),
            'plans' => __('Plans'),
            'name_type' => __('Name/Type'),
            'profession' => __('Profession'),
            'place_name_number' => __('Element number'),
            'edition' => __('Edition'),
            'date' => __('Date'),
            'image' => __('Image'),
            'sheet_number' => __('Sheet Number')
        ];

        $this->template->content = View::make('reports/approve-element/create', [
            'translations' => $translations,
            'userProfession' => $this->_user->professions->find()->name,
            'userRole' =>  $this->_user->getRelevantRole('name'),
            'projectId' => $projectId
        ]);
    }
}