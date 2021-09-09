Vue.component('labtest-update', {
    template: `
    <section class='lt_edit'>     
        <div class="elements_title_sec">
            <div class="lt_page_title">{{ trans.project_name }} / <span class="project_name"> {{ project ? project.name : '' }}</span></div>
        </div>
        <form @submit.prevent="onSubmit">
            <div class="labtest_edit_container">
                <div class="loader_backdrop" v-if="showLoader">
                    <div class="loader"></div>
                </div>
                <div class="epage_btns">
                    <div class="epage_title">{{ trans.edit }}</div>
                    <div class="epage_delete cursor-pointer" v-if="isSuperAdmin()" @click="deleteLabtest()">{{ trans.delete }}</div>
                </div>
                <div class="epage_header">
                    <div class="header_top_props">
                        <div class="header_top_propertie">
                            <div class="header_top_prop_name">{{ trans.lab_control }}</div>
                            <div class="header_top_prop_value">{{ labtest.id }}</div>
                        </div>
                        <div class="header_top_propertie">
                            <div class="header_top_prop_name">{{ trans.lab_certificate_number }}</div>
                            <div class="header_top_prop_value">{{ labtest.certNumber }}</div>
                        </div>
                    </div>
                    <div class="epage_header_bottom">
                        <div class="lt_project_proprtrties_edit">
                            <div class="lt_project_proprtrty_item">
                                <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-project"></i>
                                </div>
                                <div class="lt_project_proprtrty_name">{{ labtest.buildingName }}</div>
                            </div>
                             <div class="lt_project_proprtrty_item">
                                <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-element"></i>
                                </div>
                                <div class="lt_project_proprtrty_name">{{ labtest.elementName }}</div>
                            </div>
                            <div class="lt_project_proprtrty_item">
                                <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-worker"></i>
                                </div>
                                <div class="lt_project_proprtrty_name">{{ labtest.craftName }}</div>
                            </div>
                            <div class="lt_project_proprtrty_item">
                                <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-baseline-stairs"></i>
                                </div>    
                                <div v-if="labtest.floor_custom_name" class="lt_project_proprtrty_name">
                                    {{labtest.floorCustomName}}<span class="bidi-override" style="font-weight: 600"> ({{ labtest.floorNumber }})</span>
                                </div>    
                                <div v-else>
                                    <span  class="bidi-override" style="font-weight: 600">
                                        {{ labtest.floorNumber }}
                                    </span>
                                </div>                               
                            </div>   
                            <div class="lt_project_proprtrty_item" v-if="labtest.placeId">
                                <template v-if="labtest.placeType==='private'">
                                    <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-room-key"></i></div>
                                </template>
                                <template v-else>
                                    <div class="lt_project_proprtrty_icon"><i class="icon q4bikon-public"></i></div>
                                </template>
                                <div class="lt_project_proprtrty_name">{{ labtest.placeCustomNumber }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ltest_main_info">
                    <div class=" ltest_info_wraper">
                        <div class="ltest_info_certificate">
                            <div class="ltest_info_certificate_title">{{ trans.delivery_cert }}</div>
                            <div class="ltest_info_certificate_area">
                                <div class="labtest_edit_textarea">
                                     <textarea cols="30" rows="10" name="delivery_cert" v-model="labtest.deliveryCert"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="ltest_info_standard">
                            <div class="ltest_info_standard_title">{{ trans.standard }}</div>
                            <div class="ltest_info_standard_area">
                            <div class="labtest_edit_textarea">
                                <textarea cols="30" rows="10" name="standard" @input="emptyErrors" v-validate="'required'" v-model="labtest.standard"></textarea>
                            </div>
                            <span v-show="errors.has('standard')" class="help is-danger">{{ errors.first('standard') }}</span>
                            </div>
                        </div>
                       <div class="ltest_info_select">
                            <div class="input_item_label" v-show="labtest.status">{{ trans.select_status }}</div>
                            <multiselect 
                                v-model="labtest.status"
                                name="status"
                                @change="emptyErrors" 
                                v-validate="'required'" 
                                :option-height="104" 
                                :placeholder="trans.select_status" 
                                :disabled="!ltStatuses.length" 
                                :options="ltStatuses" 
                                label="name" 
                                :searchable="true" 
                                :allow-empty="false" 
                                :show-labels="false"
                            >
                                <template slot="singleLabel" slot-scope="props">{{ trans[props.option] }}</template>
                                <template slot="option" slot-scope="props">
                                <span>{{ trans[props.option] }}</span>
                                </template>
                            </multiselect>  
                            <span v-show="errors.has('status')" class="help is-danger">{{ errors.first('status') }}</span>    
                        </div>                                
                       <div class="ltest_info_plan" v-if="plan">
                            <div class="ltest_info_plan_top">
                                <div class="ltest_info_plan_select">
                                    <div class="ltest_info_plan_select_title">{{ trans.plan }}</div>
                                </div>
                            </div>
                            <div class="ltest_info_plan_main">
                                <div class="ltest_info_plan_name">
                                    <div class="ltest_info_plan_nameTT">{{ trans.plan_name }}:</div>
                                    <div class="ltest_info_plan_name_value">{{ plan.name }}</div>
                                </div>
                                <div class="ltest_info_plan_properties_wrap">
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_name">{{ trans.edition }}</div>
                                        <div class="ltest_info_plan_property_value">{{ plan.edition }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_name">{{ trans.date }}</div>
                                        <div class="ltest_info_plan_property_value">{{ getDate(plan.createdAt) }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_name">{{ trans.status }}</div>
                                        <div class="ltest_info_plan_property_value">{{ trans[plan.status] }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property" v-if="plan.filePath && plan.fileName">
                                        <div class="ltest_info_plan_property_name">{{ trans.attached_plan }}</div>
                                        <div class="ltest_info_plan_property_value">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16"
                                                viewBox="0 0 12 16" fill="none">
                                                <path
                                                    d="M11.7932 4.21309L7.91082 0.213091C7.77812 0.0763638 7.59882 0 7.41177 0H1.41176C0.631765 0 0 0.650909 0 1.45455V14.5455C0 15.3491 0.631765 16 1.41176 16H10.5882C11.3682 16 12 15.3491 12 14.5455V4.72727C12 4.53455 11.9259 4.34982 11.7932 4.21309ZM7.05882 12.3636H3.52941C3.13976 12.3636 2.82353 12.0378 2.82353 11.6364C2.82353 11.2349 3.13976 10.9091 3.52941 10.9091H7.05882C7.44847 10.9091 7.76471 11.2349 7.76471 11.6364C7.76471 12.0378 7.44847 12.3636 7.05882 12.3636ZM8.47059 9.45455H3.52941C3.13976 9.45455 2.82353 9.12873 2.82353 8.72727C2.82353 8.32582 3.13976 8 3.52941 8H8.47059C8.86024 8 9.17647 8.32582 9.17647 8.72727C9.17647 9.12873 8.86024 9.45455 8.47059 9.45455ZM7.76471 5.09091C7.37506 5.09091 7.05882 4.76509 7.05882 4.36364V1.38473L10.656 5.09091H7.76471Z"
                                                    fill="#1EBCE8" />
                                            </svg>
                                            <a :href="getPlanFilePath()" @click.prevent="handlePlanFileClick()" style="display: inline-block; vertical-align: middle;">
                                                <span class="ltest_info_plan_image_mame">{{ plan.fileName }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ltest_main_info_inputs">
                        <div class="ltest_main_inputs_wraper">
                            <template v-if="ltCraftParamsList">
                                <div class="ltest_main_input" v-for="(item, index) in ltCraftParamsList">
                                    <div class="input_item_label" v-show="item.value">{{ getParamName(item) }}</div>
                                    <input 
                                        type="number" 
                                        autocomplete="off" 
                                        :placeholder="getParamPlaceholder(item)" 
                                        :name="item.name" 
                                        @input="emptyErrors" 
                                        v-validate="'numeric'" 
                                        v-model="item.value"
                                    >
                                    <span v-show="errors.has(item.name)" class="help is-danger">{{ errors.first(item.name) }}</span>
                                </div>
                            </template>
                            <div class="ltest_main_input">
                                <div class="input_item_label" v-show="labtest.strengthAfter">{{ trans.strength_after_result }}</div>
                                <multiselect 
                                    v-model="labtest.strengthAfter" 
                                    :option-height="104" 
                                    @change="emptyErrors" 
                                    :placeholder="trans.strength_after_result" 
                                    :disabled="!strengthAfterOptions.length" 
                                    :options="strengthAfterOptions" 
                                    label="name" 
                                    :searchable="true" 
                                    :allow-empty="false" 
                                    :show-labels="false"
                                >
                                    <template slot="singleLabel" slot-scope="props">{{ props.option }}</template>
                                    <template slot="option" slot-scope="props">
                                        <span>{{ props.option }}</span>
                                    </template>
                                </multiselect>   
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ticket info -->
                <div class="labtest_ticket_info" :class="{'add_certificate': creating}">
                <div class="labtest_ticket_info_top">
                    <div class="labtest_ticket_info_title">{{ trans.certificate }}</div>
                    <div class="labtest_ticket_info_add" @click="emptyTicket">{{ trans.add_certificate }}</div>
                </div>

                <div class=" ltest_info_wraper ">

                    <div class="ltest_info_standard">
                        <div class="ltest_info_standard_title">{{ trans.description }}</div>
                        <div class="ltest_info_standard_area">
                            <div class="labtest_edit_textarea">
                                <textarea 
                                    cols="30" 
                                    rows="10" 
                                    v-model="ticket.description" 
                                    name="description" 
                                    @input="emptyErrors" 
                                    v-validate="'required'" 
                                    :class="{'disabled': (ticket.id && !(isSuperAdmin()))}"
                                ></textarea>
                            </div>
                            <span v-show="errors.has('description')" class="help is-danger">{{ errors.first('description') }}</span>
                        </div>
                    </div>
                    <div class="ltest_info_select">
                        <div class="input_item_label" v-show="ticket.number">{{ trans.lab_certificate }}</div>
                        <input type="text" v-model="ticket.number" autocomplete="off" :placeholder="trans.lab_certificate" name="ticket_number" :class="{'disabled': (ticket.id && !(isSuperAdmin()))}">
                    </div>
                    <div class="ltest_info_select">
                        <div class="input_item_label" v-show="ticket.freshStrength">{{ trans.fresh_concrete_strength }}</div>
                        <input type="number" v-model="ticket.freshStrength" autocomplete="off" :placeholder="trans.fresh_concrete_strength" @input="emptyErrors" :class="{'disabled': (ticket.id && !(isSuperAdmin()))}" name="fresh_strength" v-validate="'numeric'">
                        <span v-show="errors.has('fresh_strength')" class="help is-danger">{{ errors.first('fresh_strength') }}</span>
                    </div>
                    <div class="ltest_info_select">
                        <div class="input_item_label" v-show="ticket.rollStrength">{{ trans.roll_strength }}</div>
                        <input type="number" v-model="ticket.rollStrength" autocomplete="off" :placeholder="trans.roll_strength" @input="emptyErrors" :class="{'disabled': (ticket.id && !(isSuperAdmin()))}" name="roll_strength" v-validate="'numeric'">
                        <span v-show="errors.has('roll_strength')" class="help is-danger">{{ errors.first('roll_strength') }}</span>
                    </div>
                </div>
                <div class=" ltest_info_wraper notes">
                    <div class="ltest_info_certificate">
                        <div class="ltest_info_certificate_title">{{ trans.notes }}</div>
                        <div class="ltest_info_certificate_area">
                            <div class="labtest_edit_textarea">
                                 <textarea cols="30" rows="10" v-model="ticket.notes" name="notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="ltest_info_plan">
                        <div class="ltest_info_plan_top">
                            <div class="ltest_info_plan_select">
                                <div class="ltest_info_plan_select_title">{{ trans.lab_certificate }}</div>
                            </div>
                        </div>
                        <div class="ltest_info_plan_main">
                            <div class="ltest_info_plan_properties_wrap">                                
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.upload_date }}</div>
                                </div>
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.status }}</div>
                                </div>
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.description }}</div>
                                </div>
                                                             
                            </div>
                            <template v-for="ticketItem in tickets">
                                <div class="ltest_info_plan_properties_wrap cursor-pointer" :class="{'approved': ticketItem.status==='approve', 'current': ticketItem.id === ticket.id}" @click="selectTicket(ticketItem)">
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ getDate(ticketItem.createdAt) }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ trans[ticketItem.status] }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ ticketItem.description }}</div>
                                    </div>
                                </div>
                            </template>  

                        </div>                        
                    </div>
                </div>
                <div class="labtest_edit_bottom">
                    <div class="labtest_attachment">
                        <div class="ltest_attachment_title">{{ trans.attached_files }}</div>
                        <div class="attach_file" :class="{'labtest-disabled': !enableSave }" @click="handleAttachFileClick()"></div>
                        <input type="file" id="fileInput" @change="fileChange($event)" style="opacity: 0" />
                        <div class="ltest_attachment_wraper">
                            <template v-for="(image, index) in images">
                                <div class="ltest_attachment_item" v-if="!image.planId" :key="index">
                                    <div v-if="fileIsImage(image.fileName)" class="ltest_attachment_icon"><i class="icon q4bikon-file" @click="openModal(index)"></i></div>
                                    <div v-else class="ltest_attachment_icon">
                                        <a v-if="image.filePath.length > 1" :href="imagePath(image.filePath,image.fileName)" target="_blank"><i class="icon q4bikon-file"></i></a>
                                        <i v-else class="icon q4bikon-file" style="cursor: default"></i>
                                    </div>
                                    <div class="ltest_attachment_right">
                                        <div class="ltest_attachment_name">{{ image.fileName }}</div>
                                        <div class="ltest_attachment_line"></div>
                                        <div class="ltest_attachment_Uploaded">{{ trans.uploaded }}</div>
                                        <button class="remove_image" v-if="enableSave" @click.stop.prevent="deleteImage(index)"></button>
                                    </div>
                                </div>    
                            </template>                                                  
                        </div>
                    </div>
                    <div class="labtest_editor">
                        <div class="bidi-override">
                            <div class="labtest_editor_item" v-if="labtest['updateUser']">
                                <div class="labtest_editor_by"> {{ trans.updated_by }} </div>
                                <div class="labtest_editor_name_sec">
                                    <span> {{ labtest['updateUser'] }}</span>
                                    :
                                    <span class="labtest_editor_name_sec">{{ getDate(labtest.updatedAt) }}</span>
                                </div>
                            </div>
                            <div class="labtest_editor_item" v-if="labtest['createUser']">
                                <div class="labtest_editor_by"> {{ trans.created_by }} </div>
                                <div class="labtest_editor_name_sec">
                                    <span> {{ labtest['createUser'] }}</span>
                                    :
                                    <span class="labtest_editor_name_sec">{{ getDate(labtest.createDate) }}</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            </div>
            <div class="lt_ticket_btns">
                <button class="lt_ticket_save" type="submit" :class="{'labtest-disabled': !enableSave || errors.items.length}">{{ trans.save }}</button>
                <button class="lt_ticket_close" @click.stop.prevent="closePage()">{{ trans.close }}</button>
            </div>
        </form>
        <confirm-modal 
                v-if="needToConfirm"
                :msg="msg" 
                :trans="trans" 
                :deletable="trans.lab_control"
                :deletable-id="labtest.id"
                :modal-data="modalData"
                @closeConfirm="needToConfirm=false"
                @deleteConfirmed="deleteConfirmed"
                >
            </confirm-modal>
        <div id="image-editor" :class="{'hidden': !showEditor}">
            <div class="" id="pixel-perfect-editor"></div>
        </div>
   </section>

`,
    /** Props
     * labtestId: 201
     * projectId: 52
     * statuses: "["waiting", "approve", "non_approve"]"
     * siteUrl:"https://qforb.net/"
     * translations: "{
     *  "project_name":"Project name",
     *  "save":"Save",
     *  "close":"Close",
     *  "search":"Search",
     *  "waiting":"Waiting",
     *  "approve":"Approved",
     *  "non_approve":"Not approved",
     *  "for_reference":"For reference",
     *  "for_approval":"For approval",
     *  "to_the_tender":"To the tender",
     *  "to_execute":"To execute",
     *  "other":"Other",
     *  "edit":"Edit",
     *  "delete":"Delete",
     *  "standard":"Essence of work/standard",
     *  "plan":"Plan",
     *  "select_plan":"Select plan",
     *  "description":"Description",
     *  "select_status":"Select status",
     *  "select_value":"Select value",
     *  "ticket":"Ticket",
     *  "notes":"Notes",
     *  "lab_cert":"Lab certificate",
     *  "ticket_upload_date":"Ticket upload date",
     *  "attached_files":"Attached files",
     *  "attached_plan":"Attached plan",
     *  "uploaded":"uploaded",
     *  "updated_by":"Updated by",
     *  "created_by":"Created by",
     *  "lab_certificate":"Lab certificate",
     *  "lab_certificate_number":"Lab certificate number",
     *  "certificate":"Certificate",
     *  "add_certificate":"Add certificate",
     *  "fresh_concrete_strength":"Fresh concrete strength",
     *  "roll_strength":"Roll strength",
     *  "lab_control":"Lab control",
     *  "delivery_cert":"Delivery certificates",
     *  "plan_name":"Plan name",
     *  "edition":"Edition",
     *  "date":"Date",
     *  "enter_the":"Enter the",
     *  "amount_of_volume":"Amount of volume",
     *  "sediment":"Sediment",
     *  "type":"Type",
     *  "status":"Status",
     *  "strength_after_result":
     *  "Strength after/result",
     *  "upload_date":"Upload date"
     *  }"
     */
    props: {
        labtestId: {required: true},
        projectId: {required: true},
        statuses: {required: true},
        siteUrl: {required: true},
        translations: {required: false},
        role: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },

    data() {
        return {
            showLoader: false,
            canUserChoosePlan: !!this.canChoosePlan,
            project: null,
            labtest: {},
            plan: null,
            items: [],
            trans: JSON.parse(this.translations),
            search: '',
            ltStatuses: JSON.parse(this.statuses),
            strengthAfterOptions: [0, 3, 7, 14, 21, 28, 60, 90],
            labtestCraftParams: null,
            ltCraftParamsList: null,
            ltCrafts: null,
            labCraft: null,
            ticket: {},
            tickets: [],
            editor: null,
            showEditor: false,
            images: [],
            openedImageIndex: null,
            imagesOld: [],
            isModalVisible: false,
            creating: false,
            needToConfirm: false,
            modalData: null,
            msg: ""
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        enableSave() {
            return !this.ticket.id || (this.ticket.id && (this.ticket.id === this.labtest.ticketId));
        },
    },
    created() {
        // this.getLabtestCraftParams();
        if (this.projectId) {
            this.getProject(this.projectId);
        }
        if (this.labtestId) {
            this.getLabtest(this.projectId);
        }
    },
    mounted() {
        this.$validator.localize('msg');

        var config = {
            onLoad: (editor) => {
                editor.markup.appendHtml('<button id="close" class="editor-top-buttons editor-close-button">' + this.trans.close + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#close').addEventListener('click',(e) => {
                        this.closeModal()
                    })
                })
                editor.markup.appendHtml('<button id="save" class="editor-top-buttons editor-save-button">' + this.trans.save + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#save').addEventListener('click',(e) => {
                        this.saveImage()
                    })
                })
            }
        };
        Object.assign(config, window.EDITOR_CONFIG )
        this.editor = new PixelPerfect('#pixel-perfect-editor',config);
    },
    watch: {
        projectId(val) {
            if(val) {
                this.getProject(val);
                this.getElements();
            }
        },
        labtestId(val) {
            if(val) {
                this.getLabtest(val);
            }
        },
        project(val) {
            if(val) {
                this.getElements();
            }
        },
        labtest(val) {
            if (val) {
                this.getLtCrafts();
            }
            if(val && val.planId) {
                this.getLabtestPlan();
            }
        }
    },
    methods: {
        getParamValue(item, list) {
            let val = item.defaultValue;
            list.forEach((p) => {
                if (p.clpId == item.id) {
                    val = parseInt(p.value)
                }
            })
            return val
        },
        fileIsImage(file){
            let ext = file.split('.').pop().toLowerCase()
            return ['jpe','jpeg','jpg','png','tif','tiff'].includes(ext)
        },
        imagePath(path,name){
            if(path.indexOf('https://') >= 0){
                return path + '/' + name
            }
            return '/' + path + '/' + name
        },
        handleAttachFileClick() {
            document.getElementById('fileInput').click()
        },
        isSuperAdmin() {
            return this.role === 'super_admin'
        },
        emptyTicket() {
            this.creating = true;
            this.ticket = {};
            this.images = [];
        },
        closePage(){
            window.location.href = this.siteUrl + "/labtests/project/" + this.projectId
        },
        getPlanFilePath() {
            return this.plan.imagePath;
        },
        async onSubmit() {
            await this.$validator.validateAll();
            if (!this.errors.any()) {
                this.sendLabtestSaveRequest();
                if (this.ticket.id && (this.ticket.id === this.labtest.ticketId)) {
                    this.sendLabtestTicketUpdateRequest();
                } else {
                    this.sendLabtestTicketSaveRequest();
                }
            }
        },
        emptyErrors(e) {
            const name = e.target.name;
            this.errors.remove(name)
        },
        selectTicket(ticket) {
            this.creating = false;
            this.getLabtestTicket(ticket.id)
        },
        getParamName(item) {
            return item['name'+this.capitalizeFirstLetter(this.currentLang)]
        },
        getParamPlaceholder(item) {
            let pName = item['name'+this.capitalizeFirstLetter(this.currentLang)];
            let str = this.trans.enter_the;
            // pName = pName.toLowerCase().split(' ').join('_');
            return str + ' ' + pName;
        },
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        getLabtest(id) {
            let url = `/projects/${id}/entities/labtest/${this.labtestId}?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.labtest = response;
                    this.getLabtestTicket(this.labtest.ticketId);
                    this.getLabtestTickets();
                })
        },
        getLabtestTicket(id) {
            if (!this.labtest.ticketId) return;
            let url = `/projects/${this.projectId}/entities/labtest_ticket/${id}`;
            qfetch(url, {method: 'GET', headers: {
                "Cache-directive": "no-cache",
                "Cache-control": "no-cache",
                "Pragma-directive": "no-cache",
                "Pragma": "no-cache",
                "Expires": "0"
            }})
                .then(response => {
                    this.ticket = {};
                    this.ticket = response;
                    this.images = [];
                    if(this.ticket.images && this.ticket.images.length > 0){
                        this.ticket.images.map((el) => {
                            let ext = el.fileName.split('.')[1]
                            this.images.push({
                                id: el.id,
                                fileName: el.fileName,
                                fileOriginalName: el.fileOriginalName,
                                filePath: el.filePath,
                                src: '/' + el.filePath + '/' + el.fileName + '?' + Q4U.timestamp(),
                                ext: ext === 'jpg' ? 'jpeg' : ext,
                            });
                        });
                    }
                })
        },
        getLabtestTickets() {
            let url = `/projects/${this.projectId}/labtests/${this.labtestId}/tickets`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.tickets = response.items;
                })
        },
        getLabtestCraftParams() {
            let url = `/projects/${this.projectId}/entities/labtest_craft_params/${this.labtestId}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.labtestCraftParams = response;
                    console.log('labtestCraftParams',this.labtestCraftParams);
                    let newArr = [...this.ltCraftParamsList];
                    newArr.forEach((item, ind) => {
                        let newItem = JSON.parse(JSON.stringify(item));
                        newItem.value = this.getParamValue(item, this.labtestCraftParams);
                        newArr[ind] = newItem;
                    })
                    this.ltCraftParamsList = [...newArr];

                    console.log(22, this.ltCraftParamsList);
                })
        },
        getLtCrafts() {
            let url = `/projects/labtests/bound_crafts`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.ltCrafts = response.items;
                    this.labCraft = this.ltCrafts.find((item) => {
                        let itemCraftName = item.craftName;
                        let labtestCraftName = this.labtest.craftName;
                        if (itemCraftName && labtestCraftName) {
                            if (itemCraftName.trim() === labtestCraftName.trim()) {
                                return item;
                            }
                        }
                    })
                    if (this.labCraft) {
                        this.getltCraftParametersList()
                    }
                })
        },
        getltCraftParametersList() {
            let url = `/projects/labtests/bound_craft_params/${this.labCraft.id}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.ltCraftParamsList = response.items;
                    this.ltCraftParamsList.forEach((i) => {
                        i.value = "";
                    })
                    this.getLabtestCraftParams()

                })
        },
        getLabtestPlan() {
            let url = `/projects/${this.projectId}/entities/labtest_plan/${this.labtestId}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.plan = response
                })
        },
        sendLabtestTicketUpdateRequest() {
            let url = `/projects/${this.project.id}/labtests/${this.labtest.id}/tickets/${this.ticket.id}`;
            this.editing = true;
            let data = JSON.parse(JSON.stringify(this.ticket));
            data.status = this.labtest.status;
            data.images = this.images;
            data.imagesOld = this.imagesOld;
            qfetch(url, {method: 'PUT', headers: {}, body: data})
                .then(response => {
                    this.editing = false;
                    this.closePage()
                    // this.getLabtest(this.labtestId);
                }).catch(() => {
                this.editing = false;
            })
        },
        sendLabtestTicketSaveRequest() {
            this.showLoader = true;
            let data = JSON.parse(JSON.stringify(this.ticket));
            data.status = this.labtest.status;
            data.images = this.images;

            let url = `/projects/${this.project.id}/labtests/${this.labtest.id}/tickets`;
            this.editing = true;
            qfetch(url, {method: 'POST', headers: {}, body: data})
                .then(response => {
                    this.editing = false;
                    // this.getLabtest(this.labtestId);
                    this.showLoader = false;
                    this.closePage()
                }).catch(() => {
                this.editing = false;
                this.showLoader = false;
            })
        },
        getParams() {
            let params = [];
            if (this.ltCraftParamsList) {
                this.ltCraftParamsList.forEach((cr) => {
                    if (cr.value) {
                        let d = {
                            clId: cr.clId,
                            clpId: cr.id,
                            value: cr.value
                        }
                        params.push(d)
                    }
                })
            }

            return params;
        },
        sendLabtestSaveRequest(elArr) {
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/${this.labtest.id}`;
            let data = {labtest: this.labtest, labtestCraftParams: this.getParams()}
            console.log('data', data);
            qfetch(url, {method: 'PUT', headers: {}, body: data})
                .then(response => {
                    this.creating = false;
                    this.showLoader = false;
                    this.getLtCrafts();
                }).catch(() => {
                this.creating = false;
                this.showLoader = false;
            })
        },
        addItem() {
            let newItem = { name: '' };
            this.items.unshift(newItem)
        },
        toggleMore(data) {
            let {item, index} = data;
            let obj = {...item};
            obj.more = !obj.more;
            item = {...obj};
            if (obj.more) {
                var items = this.items.map((item) => {
                    item.more = false
                    return item;
                });
            } else {
                var items = [...this.items];
            }
            items[index] = obj;
            this.items = [...items];
        },
        sendSaveRequest(newElements) {
            let url = `/projects/${this.project.id}/labtests/elements`;

            qfetch(url, {method: 'POST', headers: {}, body: newElements})
                .then(response => {
                    this.search = '';
                    this.items = response.items;
                })
        },
        deleteLabtest() {
            this.needToConfirm = true;
            this.modalData = {};
            this.msg = `${this.trans.are_you_sure_to_delete}`;
        },
        deleteConfirmed() {
            let url = `/projects/${this.project.id}/labtests/${this.labtestId}`;
            qfetch(url, {method: 'DELETE', headers: {}})
                .then(response => {
                    window.location = `/labtests/project/${this.projectId}`
                })
        },
        sendDeleteRequest(id) {
            let url = `/projects/${this.project.id}/labtests/elements/${id}`;
            qfetch(url, {method: 'DELETE', headers: {}});
        },
        getProject(id) {
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                })
        },
        getElements(){
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/elements`;
            let param = encodeURIComponent('?search='+ this.search)
            url +=  param;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let items = response.items;
                    items.forEach(i => i.more = false)
                    this.items = items;
                    this.showLoader = false;
                })
        },
        saveImage(){

            console.log(this.openedImageIndex)
            this.editor.tool('crop').cancel();
            let openedIndex = this.openedImageIndex;
            let image = {...this.images[openedIndex]};
            let imgs = this.images;
            let ext = image.ext === 'pdf' ? 'jpeg': image.ext
            image.src = this.editor.getCanvasDataAs(ext);
            console.log(image.ext)
            if(image.id){
               this.imagesOld.push(image.id)
                image.id = null;
            }

            if(image.planId){
                delete image.planId;
            }
            imgs[openedIndex] = {...image};
            this.images = JSON.parse(JSON.stringify(imgs));
            this.closeModal();
        },
        handlePlanFileClick() {
            let plan = this.plan;
            if(plan.fileOriginalName){
                let ext = plan.fileName.split('.')[1]
                this.images.unshift({
                    id: null,
                    fileName: Q4U.timestamp() + '.' + ext,
                    fileOriginalName: plan.fileName,
                    filePath: plan.imagePath,
                    src: plan.imagePath,
                    ext: ext === 'jpg' ? 'jpeg' : ext,
                    planId: plan.id
                });
            }

            this.openModal(0);
        },
        openModal(index){
            this.openedImageIndex = index;
            console.log(this.openedImageIndex)
            setTimeout( () => {
                this.editor.resetCanvas(true);
                this.editor.setActiveTool('select')
                this.editor.loadBackgroundImageFromUrl(this.images[index].src , (error) => {
                    if(error){
                        this.closeModal();
                    }
                })
                this.showEditor = true;
                document.getElementsByTagName('body')[0].classList.add("hide_body_scroll");
            }, 1000)
        },

        closeModal(){
            this.editor.tool('crop').cancel();
            document.getElementsByTagName('body')[0].classList.remove("hide_body_scroll");
            if (this.images[this.openedImageIndex].planId) {
                this.images.splice(this.openedImageIndex, 1);
            }
            this.openedImageIndex = null;
            this.showEditor = false;
        },
        fileChange(event){
            let image = event.target.files[0];
            if(image){
                let ext = image.type.split('/')[1];
                if(!['jpe','jpeg','jpg','png','tif','tiff','pdf'].includes(ext)) {
                    ext = image.name.split('.').pop().toLowerCase()
                }
                let fileReader = new FileReader();
                fileReader.onload = (e) => {
                    this.images.unshift({
                        id: null,
                        fileName: Q4U.timestamp() + '.' + ext,
                        fileOriginalName: image.name,
                        filePath: '',
                        src: e.target.result,
                        ext: ext === 'jpg' ? 'jpeg' : ext
                    });
                }
                fileReader.readAsDataURL(image);
            }
        },
        deleteImage(index){
            if(this.images[index]){
                if(this.images[index].id){
                    this.imagesOld.push(this.images[index].id)
                }
                this.images.splice(index, 1);
            }
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    },

});

