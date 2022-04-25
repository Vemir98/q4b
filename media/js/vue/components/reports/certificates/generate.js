Vue.component('certificates-generate', {
    template: `
    <section class='certificates-generate new-styles'>
        <div v-if="showLoader" class="loader_backdrop_vue">
            <div class="loader"></div>
        </div>
        <div class="page-title-sec flex-start">
            <div class="page-title">{{ trans.certificates }}</div>
        </div>
        <div class="filters-section">
            <div class="filters-wraper flex-start">
                <div class="filter-item">
                    <div class="multiselect-col">
                        <div class="filter-item-label" >{{ trans.company }}</div>
                        <multiselect 
                            v-model="selectedCompany"
                            :option-height="104" 
                            :placeholder="trans.select_company" 
                            :disabled="companies.length < 1" 
                            :options="companies" 
                            track-by="id" 
                            label="name" 
                            :searchable="true" 
                            :allow-empty="false" 
                            :show-labels="false"
                        >
                            <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                            <template slot="option" slot-scope="props">
                                <span>{{ props.option.name }}</span>
                            </template>
                            <template slot="option-selected" slot-scope="props">
                                <span>{{ props.option.name }}</span>
                            </template>
                        </multiselect>
                    </div>
                </div>
                <div class="filter-item">
                    <div class="multiselect-col">
                        <div class="filter-item-label" >{{ trans.project }}</div>
                        <multiselect 
                            v-model="selectedProject"
                            :option-height="104" 
                            :placeholder="trans.select_project" 
                            :disabled="projects.length < 1" 
                            :options="projects" 
                            track-by="id" 
                            label="name" 
                            :searchable="true" 
                            :allow-empty="false" 
                            :show-labels="false"
                        >
                            <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                            <template slot="option" slot-scope="props">
                                <span>{{ props.option.name }}</span>
                            </template>
                            <template slot="option-selected" slot-scope="props">
                                <span>{{ props.option.name }}</span>
                            </template>
                        </multiselect>
                    </div>
                </div>
                <div class="filter-item">
                    <div class="filter-item-label flex-between">
                    {{ trans.crafts }}
                        <label class="table_label" :class="{'q4b-disabled': !crafts.length}">
                            <span @click="toggleSelectAll('selectedCrafts', 'crafts')">
                                <template v-if="selectedCrafts.length < crafts.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                    </div>
                    <div class="multiselect-col">
                        <multiselect v-model="selectedCrafts"  
                                :placeholder="trans.set_specialities" 
                                :disabled="!crafts.length" 
                                :options="crafts" 
                                label="name" 
                                track-by="id"
                                :multiple="true" 
                                :hide-selected="false"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :internal-search="true"
                                :taggable="false"
                                :show-labels="false"                                       
                                @select="onSelect($event, 'crafts')"
                                @remove="onRemove($event, 'crafts')"
                                >
                                <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                    <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                    <span class="multiselect-option-name">{{ scope.option.name }}</span>
                                </span>
                                <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values) }} </span></template>
                                <template slot="tag">{{ '' }}</template>
                        </multiselect>
                    </div>
                </div>
                <div class="filter-item">
                    <div class="filter-item-label flex-between">
                    {{ trans.status }}
                        <label class="table_label" :class="{'q4b-disabled': !statuses.length}">
                            <span @click="toggleSelectAll('selectedStatuses', 'statuses')">
                                <template v-if="selectedStatuses.length < statuses.length">
                                   {{ trans.select_all }}
                            </template>
                            <template v-else>
                                   {{ trans.unselect_all }}
                            </template>
                            </span>
                        </label>
                    </div>
                    <div class="multiselect-col"> 
                        <multiselect 
                            v-model="selectedStatuses"  
                            :placeholder="trans.set_statuses" 
                            :disabled="!statuses.length" 
                            :options="statuses" 
                            label="name" 
                            track-by="id"
                            :multiple="true" 
                            :hide-selected="false"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :internal-search="true"
                            :taggable="false"
                            :show-labels="false"                                       
                            @select="onSelect($event, 'statuses')"
                            @remove="onRemove($event, 'statuses')"
                            >
                            <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                <span class="multiselect-option-name">{{ trans[scope.option.name] }}</span>
                            </span>
                            <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values, true) }} </span></template>
                            <template slot="tag">{{ '' }}</template>
                        </multiselect>                        
                    </div>
                </div>
                <div class="filter-item-checkbox">
                    <span class="check-task">
                        <input type="checkbox" style="position: relative !important;right: 0" v-model="sampleRequired">
                        <span class="checkboxImg"></span>
                    </span>
                    <div class="filter-item-label flex-between">
                        {{ trans.sample_required }}
                    </div>
                </div>
                <div class="filter-buttons">
                    <button 
                        class="filter-button generate"
                        :class="{'q4b-disabled': (!canGenerate)}"
                        @click="generateReports"
                    >
                    {{ trans.generate }}
                    </button>
                </div>
            </div>
        </div>
    </section>
    `,
    props: {
        translations: {required: true},
        certStatuses: {required: true},
        filters: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            showLoader: false,
            selectedCompany: null,
            selectedProject: null,
            selectedCrafts: [],
            selectedStatuses: [],
            sampleRequired: false,
            companies: [],
            projects: [],
            project: [],
            crafts: [],
            statuses: [],
        }
    },
    computed: {
        canGenerate() {
            return (this.selectedCompany && this.selectedProject && this.selectedCrafts.length)
        }
    },
    watch: {
        selectedCompany(val) {
            if (val) {
                this.projects = Object.values(val.projects);
                if(this.filters?.selectedProject) {
                    this.selectedProject = this.filters.selectedProject
                    this.filters.selectedProject = null;
                } else {
                    this.selectedProject = null;
                }
            } else {
                this.selectedCompany = null;
                this.selectedProject = null;
            }
        },
        selectedProject(val) {
            if (val) {
                this.getProjectAPI(this.selectedProject.id)
            } else {
                this.selectedProject = null
                this.project = null;
            }
        },
        project(val) {
            if(Object.keys(val).length) {
                this.getCompanyCraftsAPI();
            }
        },
    },
    methods: {
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        toggleSelectAll(selected, list) {
            if (list.length) {
                if (this[selected].length < this[list].length) {
                    this[selected] = this[list].map((i) => {
                        i.checked = true;
                        return i
                    });
                } else {
                    this[selected] = this[list].map((i) => {
                        i.checked = false;
                        return i
                    });
                    this[selected] = []
                }
            }
        },
        onSelect(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = false;
        },
        getFilters() {
            return {
                selectedCompany:  this.selectedCompany,
                selectedProject: this.selectedProject,
                selectedCrafts: this.selectedCrafts,
                selectedStatuses: this.selectedStatuses,
                sampleRequired: this.sampleRequired
            }
        },
        getCompaniesAPI(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                    if(this.filters?.selectedProject) {
                        this.selectedProject = this.filters.selectedProject
                        this.filters.selectedProject = null;
                    }
                    this.showLoader = false;
                })
        },
        getProjectAPI(id) {
            this.showLoader = true;
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                    this.toggleSelectAll('selectedCrafts', 'crafts');
                    this.showLoader = false;
                })
        },
        getCompanyCraftsAPI() {
            this.showLoader = true;
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${this.project.company_id}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.crafts = response.items;

                    if(this.filters?.selectedCrafts) {
                        this.selectedCrafts = this.filters.selectedCrafts;
                        this.selectedCrafts.forEach(craft => {
                            this.onSelect(craft, 'crafts')
                        })
                        this.filters.selectedCrafts = null;
                    } else {
                        this.toggleSelectAll('selectedCrafts', 'crafts');
                    }

                    this.showLoader = false;
                })
        },
        generateReports() {
            this.$emit('getFiltersForReportsGenerating', {
                filters: this.getFilters(),
                page: 1
            })
        }
    },
    created() {
        const encodedStatuses = JSON.parse(this.certStatuses)
        this.statuses = Object.values(encodedStatuses).map((statusName, statusIndex) => {
            return {
                id: statusIndex,
                name: statusName
            }
        })

        this.getCompaniesAPI();

        if(this.filters) {
            this.selectedCompany = this.filters.selectedCompany;
            this.selectedProject = this.filters.selectedProject;
            this.selectedCrafts = this.filters.selectedCrafts;
            this.selectedStatuses = this.filters.selectedStatuses;
            this.sampleRequired = this.filters.sampleRequired;

            this.selectedStatuses.forEach(status => {
                this.onSelect(status, 'statuses')
            })

        } else {
            this.toggleSelectAll('selectedStatuses', 'statuses');
            console.log('this.statuses data', this.statuses)
        }
    },
    mounted() {

    }
});

