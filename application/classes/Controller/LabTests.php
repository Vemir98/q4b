<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 5/4/21
 * Time: 3:31 PM
 */


class Controller_LabTests extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index,edit,project,elements_type,elements_list,create' => [
            'GET' => 'read'
        ],
    ];

    public $company, $project;

    public function before()
    {
        parent::before();

    }

    public function action_create() {
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $translations = [
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
            'sheet_number' => __('Sheet Number'),
        ];

        VueJs::instance()->addComponent('labtests/create/create');
        VueJs::instance()->addComponent('labtests/create/delivery-certificates-section');
        VueJs::instance()->addComponent('labtests/create/certificates-section');
        VueJs::instance()->addComponent('labtests/create/plans-section');
        VueJs::instance()->includeMultiselect();
        VueJs::instance()->includeDateTimePiker();

        $this->template->content = View::make('labtests/create', [
            'translations' => $translations,
            'projectId' => $projectId
        ]);

    }

    public function action_project()
    {
        $translations = [
            "project_name" => __('Project name'),
            "search" => __('Search'),
            "select_status" => __('Select_status'),
            "show" => __('Show'),
            "print" => __('Print'),
            "export" => __('Export'),
            "select_structure" => __('Select_structure'),
            "select_floor_level" => __('Select floor/level'),
            "select_place" => __('Select_place'),
            "select_element" => __('Select element'),
            "select_specialty" => __('Select_specialty'),
            "lab_control" => __('Lab control'),
            "lab_certificate" => __('Lab certificate'),
            "lab_certificate_number" => __('Lab certificate number'),
            "date" => __('Date'),
            "create_date" => __('Create_Date'),
            "structure" => __('Structure'),
            "floor_level" => __('Floor/Level'),
            "element" => __('Element_item'),
            "standard" => __('Essence of work/standard'),
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
            'select_company' => __('Select Company'),
            'select_project' => __('Select project'),
            'company' => __('company'),
            'project' => __('project'),
            'total' => __('Total'),
            "approved" => __('Approved'),
        ];
        if($this->request->param('projectId')) {
            $id = $this->getUIntParamOrDie($this->request->param('projectId'));
            $project = ORM::factory('Project',$id);
            if( ! $project->loaded()){
                throw new HTTP_Exception_404;
            }
        }

        VueJs::instance()->addComponent('./confirm-modal');
        VueJs::instance()->addComponent('labtests/pr-labtests-list');
        VueJs::instance()->addComponent('labtests/labtest-list-item');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();
        $this->template->content = View::make('labtests/pr-labtests-list', ['projectId' => $this->request->param('projectId') ? $project->id : null, 'translations' => $translations]);
    }
    public function action_index()
    {
        $translations = [
            "lab_control" => __('Lab control'),
            "lab_control_menu" => __('Lab control menu'),
            "select_project" => __('Select project'),
            "company" => __('Company'),
            "status" => __('Status'),
            "active" => __('active'),
            "archive" => __('archive'),
            "suspended" => __('suspended'),
            "start_date" => __('Start Date'),
            "end_date" => __('End Date'),
        ];
        VueJs::instance()->addComponent('labtests/projects-list');
        VueJs::instance()->addComponent('labtests/project-item');
        VueJs::instance()->includeMultiselect();
        $this->template->content = View::make('labtests/projects-list', ['translations' => $translations]);
    }

    public function action_elements_type()
    {
        $id = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = ORM::factory('Project',$id);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }
        $translations = [
            "elements_type" => __('Elements Type'),
            "description" => __('Description'),
            "more" => __('More'),
            "edit" => __('Edit'),
            "save" => __('Save'),
            "search" => __('Search'),
            "delete" => __('Delete'),
            "cancel" => __('Cancel'),
            "are_you_sure" => __('Are you sure, you want'),
            "to_delete" => __('delete'),
            "element" => __('Element_item'),
            "are_you_sure_to_delete" => __('Are you sure, you want delete this'),
        ];
        VueJs::instance()->addComponent('./confirm-modal');
        VueJs::instance()->addComponent('labtests/elements-type');
        VueJs::instance()->addComponent('labtests/element-item');
        $this->template->content = View::make('labtests/elements-type', ['projectId' => $project->id, 'translations' => $translations]);
    }
    public function action_elements_list()
    {
        $id = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = ORM::factory('Project',$id);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }
        $translations = [
            "elements_list" => __('Elements List'),
            "copy_to" => __('Copy to'),
            "select_company" => __('Select Company'),
            "select_project" => __('Select project'),
            "copy" => __('Copy'),
            "save" => __('Save'),
        ];
        VueJs::instance()->addComponent('labtests/elements-list');
        VueJs::instance()->addComponent('labtests/element-crafts-item');
        VueJs::instance()->includeMultiselect();
        $this->template->content = View::make('labtests/elements-list', ['projectId' => $project->id, 'translations' => $translations]);
    }
    public function action_edit()
    {
        $id = $this->getUIntParamOrDie($this->request->param('projectId'));
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $project = ORM::factory('Project',$id);
        $labtest = ORM::factory('LabTest',$labtestId);
        if( ! $project->loaded() || ! $labtest->loaded()){
            throw new HTTP_Exception_404;
        }
        $translations = [
            "project_name" => __('Project name'),
            "save" => __('Save'),
            "close" => __('Close'),
            "search" => __('Search'),
            "waiting" => __('waiting'),
            "approve" => __('Approved'),
            "non_approve" => __('Not approved'),
            'for_reference' => __('for_reference'),
            'for_approval' => __('for_approval'),
            'to_the_tender' => __('to_the_tender'),
            'to_execute' => __('to_execute'),
            'other' => __('other'),
            'edit' => __('Edit'),
            'delete' => __('Delete'),
            "standard" => __('Essence of work/standard'),
            "plan" => __('Plan'),
            "select_plan" => __('Select_plan'),
            "description" => __('Description'),
            "select_status" => __('Select_status'),
            "select_value" => __('Select value'),
            "ticket" => __('Ticket'),
            "notes" => __('Notes'),
            "lab_cert" => __('Lab certificate'),
            "ticket_upload_date" => __('Ticket upload date'),
            "attached_files" => __('Attached files'),
            "attached_plan" => __('Attached plan'),
            "uploaded" => __('uploaded'),
            "updated_by" => __('Updated by'),
            "created_by" => __('Created by'),
            "lab_certificate" => __('Lab certificate'),
            "lab_certificate_number" => __('Lab certificate number'),
            "certificate" => __('Certificate'),
            "add_certificate" => __('Add certificate'),
            "fresh_concrete_strength" => __('Fresh concrete strength'),
            "roll_strength" => __('Roll strength'),
            "lab_control" => __('Lab control'),
            "delivery_cert" => __('Delivery certificates'),
            'plan_name' => __('Plan name'),
            'edition' => __('Edition'),
            "date" => __('Date'),
            "enter_the" => __('Enter the'),
            'amount_of_volume' => __('Amount of volume'),
            'sediment' => __('Sediment'),
            'type' => __('Type'),
            "status" => __('Status'),
            "strength_after_result" => __('Strength after/result'),
            "upload_date" => __('Upload date'),
            "are_you_sure" => __('Are you sure, you want'),
            "to_delete" => __('delete'),
            "cancel" => __('Cancel'),
            "element" => __('Element_item'),
            "are_you_sure_to_delete" => __('Are you sure, you want delete this'),
            "list_of_files" => __('list_of_files'),
        ];
        VueJs::instance()->addComponent('./confirm-modal');
        VueJs::instance()->addComponent('labtests/labtest-update');
        VueJs::instance()->includeMultiselect();
        VueJs::instance()->includeDateTimePiker();

        $this->template->content = View::make('labtests/update', ['projectId' => $project->id, 'labtestId' => $labtestId, 'translations' => $translations]);
    }
}