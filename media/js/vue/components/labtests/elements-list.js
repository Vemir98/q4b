Vue.component('elements-list', {
    template: `
        <section class='lt_elements_list'>
            <div class="elements_title_sec">
                <div class="lt_page_title">{{ trans.elements_list }} / <span class="project_name"> {{ project ? project.name : '' }}</span></div>               
            </div>
            <div class="lt_elements_list_inputs" v-if="items.length">
                <div class="lt_elements_list_input_headline">{{ trans.copy_to }}:</div>
                <div class="lt_elements_list_input">
                    <div class="lt_projects_select">
                        <div class="form new-styles" >
                            <div class="row-flex">
                                <div class="multiselect-col">
                                    <multiselect v-model="selectedCompany"  :option-height="104" :placeholder="trans.select_company" :disabled="companies.length < 1" :options="companies" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                                        <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                                        <template slot="option" slot-scope="props">
                                        <span>{{ props.option.name }}</span>
                                        </template>
                                    </multiselect>      
                                </div>
                            </div>
                        </div>          
                    </div>
                </div>
                <div class="lt_elements_list_input">
                    <div class="lt_projects_select">
                        <div class="form new-styles" >
                            <div class="row-flex">
                                <div class="multiselect-col">
                                    <multiselect v-model="selectedProject"  :option-height="104" :placeholder="trans.select_project" :disabled="cmpProjects.length < 1" :options="cmpProjects" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                                        <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                                        <template slot="option" slot-scope="props">
                                        <span>{{ props.option.name }}</span>
                                        </template>
                                    </multiselect> 
                                </div>
                            </div>
                        </div>               
                    </div>
                </div>
                <div class="lt_elements_list_input_button">
                    <button @click="copy" :class="{'labtest-disabled': !selectedCompany || !selectedProject || copyInProgress}">{{ trans.copy }} </button>
                </div>
            </div>
            
            <div class="elements_type_wrap" :class="{'empty': !showLoader && !items.length}">
                <div class="loader_backdrop" v-if="showLoader">
                    <div class="loader"></div>
                </div>
                <template v-show="!showLoader">
                    <element-crafts-item v-for="(item, index) in items"
                        :trans="trans"
                        :itemData="item"
                        :index="index"
                        :cmpCrafts="cmpCrafts"
                        @addOrRemoveCraft="addOrRemoveCraft">                           
                    </element-crafts-item>
                </template>
            </div>
                    
            <div class="elements_save_btn" v-if="cmpCrafts.length">
                <button :class="{ 'labtest-disabled': editing }" v-if="items.length" @click="save">{{ trans.save }}</button>
            </div>
        
        </section>
`,
    /** Props
     * projectId: 52
     * translations:"{
     * "elements_list":"Elements List",
     * "copy_to":"Copy to",
     * "select_company":"Select company",
     * "select_project":"Select project",
     * "copy":"Copy",
     * "save":"Save"
     * }"
     */
    props: {
        projectId: {required: true},
        translations: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },

    data() {
        return {
            project: null,
            items: [],
            showLoader: true,
            copyInProgress: false,
            trans: JSON.parse(this.translations),
            search: '',
            cmpCrafts: [],
            editing: false,
            selectedCompany: null,
            selectedProject: null,
            companies: [],
            cmpProjects: [],
        }
    },
    computed: {
    currentLang() {
            return $(".header-current-lang").data("lang")
        },
    },
    created() {
        let projectId = this.currentLang === 'en' ? location.pathname.split('/')[3] : location.pathname.split('/')[4];
        this.getProject(projectId);
    },
    mounted() {
        if (this.projectId) {
            this.getProject(this.projectId);
        }
        $(document).ready(() => {
            $(document).on('click', '.element_type_item', function(event) {
                if ($(event.target).hasClass('element_type_item') || $(event.target).hasClass('element_type_name')) {
                    $(this).toggleClass('open');
                }
            })
        })
    },
    watch: {
        projectId(val) {
            if(val) {
                this.getProject(val);
            }
        },
        project(val) {
            if(val) {
                this.getCompanies();
                this.getElements();
                this.getCompanyCrafts();
            }
        },
        selectedCompany(val) {
            if (val) {
                this.getCmpProjects()
            } else {
                this.selectedCompany = null
            }
            this.selectedProject = null;
        }
    },
    methods: {
        getCmpProjects(){
            if (!this.selectedCompany) return
            let url = `/companies/${this.selectedCompany.id}/entities/projects`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.cmpProjects = response.items ? response.items : [];
                })
        },
        getCompanies(){
            let url = '/companies/entities/list';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                })
        },
        addOrRemoveCraft(data) {
            let { index, craftId } = data;
            let item = this.items[index];
            let ind = item.crafts.indexOf(craftId);
            if (ind != -1) {
                item.crafts.splice(ind, 1)
            } else {
                item.crafts.push(craftId)
            }
        },
        prepareElCraftsData() {
            let elArr = [];
            this.items.forEach(i => {
                let data = {id: i.id, crafts: i.crafts}
                elArr.push(data)
            })
            return elArr
        },
        copy() {
            let elArr = [];
            this.items.forEach(i => {
                let crafts = this.cmpCrafts.filter((item) => {
                    return i.crafts.includes(item.id)
                })
                let data = {id: i.id, name: i.name, crafts: crafts}
                elArr.push(data)
            })
            if (elArr.length) {
                this.sendCopyRequest(elArr);
            }
        },
        save() {
            let elArr = this.prepareElCraftsData();
            if (elArr.length) {
                this.sendSaveRequest(elArr);
            }
        },
        sendCopyRequest(elArr) {
            this.copyInProgress = true;
            let data = {
                elements: elArr,
                companyId: this.project.company_id
            }
            let url = `/projects/${this.selectedProject.id}/labtests/copy/elements`;
            this.editing = true;

            qfetch(url, {method: 'POST', headers: {}, body: data})
                .then(() => {
                    this.copyInProgress = false;
                    this.editing = false;
                    this.selectedProject = null;
                }).catch(()=> {
                    this.editing = false;
                    this.copyInProgress = false;
            });
        },
        sendSaveRequest(elArr) {
            let url = `/projects/${this.project.id}/labtests/elements/assign`;
            this.editing = true;

            qfetch(url, {method: 'PUT', headers: {}, body: elArr})
                .then(response => {
                    this.getElements();
                    this.editing = false;
                }).catch(() => {
                this.editing = false;
            })
        },
        getElements() {
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/elements_with_crafts`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let items = response.items;
                    items.forEach(i => i.more = false)
                    this.items = items;
                    setTimeout(() => {
                        this.showLoader = false;
                    }, 200)
                })
                .catch(() => {
                    setTimeout(() => {
                        this.showLoader = false;
                    }, 200)
                })
        },
        getProject(id) {
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                })
        },
        getCompanyCrafts() {
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${this.project.company_id}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let cmpCrafts = response.items;
                    cmpCrafts.forEach(i => i.checked = false)
                    this.cmpCrafts = cmpCrafts;
                })
        },
    },

});

