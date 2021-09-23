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
    protected $_actions_perms = [
        'index' => [
            'GET' => 'read',
            'POST' => 'read'
        ]
    ];
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
            'notes_description' => __('notes_description')
        ];

//        foreach (Api_DBModules::getModulesForTasks() as $module) {
//            $translations['module_'.$module['id']] = $module['name'];
//        }


        $this->template->content = View::make('reports/approve-element/index', [
            'translations' => $translations,
            'userProfession' => $this->_user->professions->find()->name,
            'userRole' =>  $this->_user->getRelevantRole('name')
        ]);
    }
}