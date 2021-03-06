Vue.component('generate-reports', {
    template: `
    <section class='q4b-approve-el new-styles'>
        <div v-if="showLoader" class="loader_backdrop_vue">
            <div class="loader"></div>
        </div>
        <div class="page-title-sec flex-start">
            <div class="page-title"> {{ trans.approve_element }} </div>
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
                            :disabled="(cmpProjects.length < 1)" 
                            :options="cmpProjects" 
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
                        <div class="filter-item-label flex-between">
                        {{ trans.structure }}
                        <label class="table_label" :class="{'labtest-disabled': !structures.length}">
                                <span @click="toggleSelectAll('selectedStructures', 'structures')">
                                   <template v-if="selectedStructures.length < structures.length">
                                           {{ trans.select_all }}
                                    </template>
                                    <template v-else>
                                           {{ trans.unselect_all }}
                                    </template>
                                </span>
                            </label>
                        </div>
                            <multiselect 
                                v-model="selectedStructures"  
                                :placeholder="trans.set_structures" 
                                :disabled="project.length < 1" 
                                :options="structures" 
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
                                @change="getObjectFloors()"                                     
                                @select="onSelect($event, 'structures')"
                                @remove="onRemove($event, 'structures')"
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
                <div class="filter-item datepicker">
                    <div class="filter-item-label" >{{ trans.date }}</div>
                    <date-picker 
                        v-model="time"
                        :lang="langs[currentLang]"
                        :editable="false" 
                        :clearable="false"
                        :disabled="false" 
                        @change="timeChanged" 
                        type="date"
                        :range="true" 
                        format="DD/MM/YYYY"
                    >
                    </date-picker>
                </div>
                <div class="filter-item">
                    <div class="multiselect-col">
                        <div class="filter-item-label flex-between" >
                        {{ trans.floor_level }}
                            <label class="table_label" :class="{'labtest-disabled': !floors.length}">
                                <span @click="toggleSelectAll('selectedFloors', 'floors')">
                                    <template v-if="selectedFloors.length < floors.length">
                                           {{ trans.select_all }}
                                    </template>
                                    <template v-else>
                                           {{ trans.unselect_all }}
                                    </template>
                                </span>
                            </label>
                        </div>
                            <multiselect v-model="selectedFloors"  
                                :placeholder="trans.set_floor_level" 
                                :disabled="!floors.length || floorsDisabled" 
                                :options="floors" 
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
                                @select="onSelect($event, 'floors')"
                                @remove="onRemove($event, 'floors')"
                                >
                                <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                    <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                    <span class="multiselect-option-name">{{ scope.option.custom_name ? scope.option.custom_name : scope.option.number }}</span>
                                </span>                                   
                                <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getFloorsMultiselectSelectionValue(values) }} </span></template>
                                <template slot="tag">{{ '' }}</template>
                            </multiselect>
                    </div>
                </div>
                <div class="filter-item">
                    <div class="filter-item-label flex-between">
                    {{ trans.places }}
                        <label class="table_label" :class="{'labtest-disabled': !places.length}">
                            <span @click="toggleSelectAll('selectedPlaces', 'places')">
                                <template v-if="selectedPlaces.length < places.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                    </div>
                    <div class="multiselect-col">
                        <multiselect v-model="selectedPlaces"  
                                :placeholder="trans.set_places" 
                                :disabled="!places.length || placesDisabled" 
                                :options="places" 
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
                                @select="onSelect($event, 'places')"
                                @remove="onRemove($event, 'places')"
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
                    {{ trans.element }}
                        <label class="table_label" :class="{'labtest-disabled': !elements.length}">
                            <span @click="toggleSelectAll('selectedElements', 'elements')">
                                <template v-if="selectedElements.length < elements.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                    </div>
                    <div class="multiselect-col">
                        <multiselect v-model="selectedElements"  
                                :placeholder="trans.set_elements" 
                                :disabled="!elements.length" 
                                :options="elements" 
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
                                @select="onSelect($event, 'elements')"
                                @remove="onRemove($event, 'elements')"
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
                    {{ trans.crafts }}
                        <label class="table_label" :class="{'labtest-disabled': !crafts.length}">
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
                    {{ trans.manager_status }}
                        <label class="table_label" :class="{'labtest-disabled': !elManagerStatuses.length}">
                            <span @click="toggleSelectAll('selectedManagerStatuses', 'elManagerStatuses')">
                                <template v-if="selectedManagerStatuses.length < elManagerStatuses.length">
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
                            v-model="selectedManagerStatuses"  
                            :placeholder="trans.set_statuses" 
                            :disabled="!elManagerStatuses.length" 
                            :options="elManagerStatuses" 
                            label="name" 
                            track-by="id"
                            @change="getObjectFloors()" 
                            :multiple="true" 
                            :hide-selected="false"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :internal-search="true"
                            :taggable="false"
                            :show-labels="false"                                       
                            @select="onSelect($event, 'elManagerStatuses')"
                            @remove="onRemove($event, 'elManagerStatuses')"
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
                <div class="filter-item">
                    <div class="filter-item-label flex-between">
                    {{ trans.status }}
                        <label class="table_label" :class="{'labtest-disabled': !elStatuses.length}">
                            <span @click="toggleSelectAll('selectedStatuses', 'elStatuses')">
                                <template v-if="selectedStatuses.length < elStatuses.length">
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
                            :disabled="!elStatuses.length" 
                            :options="elStatuses" 
                            label="name" 
                            track-by="id"
                            @change="getObjectFloors()" 
                            :multiple="true" 
                            :hide-selected="false"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :internal-search="true"
                            :taggable="false"
                            :show-labels="false"                                       
                            @select="onSelect($event, 'elStatuses')"
                            @remove="onRemove($event, 'elStatuses')"
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
                <div class="filter-item">
                    <div class="filter-item-label flex-between">
                        {{ trans.positions }}
                        <label class="table_label" :class="{'labtest-disabled': !positions.length}">
                            <span @click="toggleSelectAll('selectedPositions', 'positions')">
                                <template v-if="selectedPositions.length < positions.length">
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
                            v-model="selectedPositions"  
                            :placeholder="trans.set_positions" 
                            :disabled="positions.length < 1" 
                            :options="positions" 
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
                            @select="onSelect($event, 'positions')"
                            @remove="onRemove($event, 'positions')"
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
                <div class="filter-item-checkbox">
                    <span class="check-task">
                        <input type="checkbox" v-model="primarySupervision">
                        <span class="checkboxImg"></span>
                    </span>
                    <div class="filter-item-label flex-between">
                        {{ trans.primary_supervision }}
                    </div>
                </div>
                <div class="filter-item-checkbox">
                    <span class="check-task">
                        <input type="checkbox" v-model="partialProcess">
                        <span class="checkboxImg"></span>
                    </span>
                    <div class="filter-item-label flex-between">
                        {{ trans.partial_process }}
                    </div>
                </div>
                <div class="filter-buttons">
                    <button 
                        class="filter-button generate"
                        :class="{'labtest-disabled': (!canGenerate)}"
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
        statuses: {required: true},
        translations: {required: true},
        filters: {required: true},
        projectId: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            items: [],
            count: 0,
            requested: false,
            time: [],
            queryProjectId: null,
            selectedCompany: null,
            selectedProject: null,
            selectedStructures: [],
            elManagerStatuses:  this.getStatuses(this.statuses),
            elStatuses: [
                {id: 1, name: 'appropriate'},
                {id: 0, name: 'not_appropriate'}
            ],
            trans: JSON.parse(this.translations),
            selectedFloors: [],
            selectedPlaces: [],
            selectedElements: [],
            selectedCrafts: [],
            selectedManagerStatuses: [],
            selectedStatuses: [],
            selectedPositions: [],
            primarySupervision: false,
            partialProcess: false,
            companies: [],
            cmpProjects: [],
            project: [],
            structures: [],
            floors: [],
            places: [],
            elements: [],
            crafts: [],
            positions: [],
            txtPrivateResult : "",
            txtPublicResult : "",
            txtTotalResult : "",
            showDropDown: false,
            langs: {
                ru: {
                    formatLocale: {
                        months: ['????????????', '??????????????', '????????', '????????????', '??????', '????????', '????????', '????????????', '????????????????', '??????????????', '????????????', '??????????????'],
                        monthsShort: ['??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????'],
                        weekdays: ['??????????????????????', '??????????????????????', '??????????????', '??????????', '??????????????', '??????????????', '??????????????'],
                        weekdaysShort: ['??o??', '??????', '??????', '??????', '??????', '??????', '??????'],
                        weekdaysMin: ['????', '????', '????', '????', '????', '????', '????'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                },
                he: {
                    formatLocale: {
                        months: ['??????????', '????????????', '??????', '??????????', '??????', '????????', '????????', '????????????', '????????????', '??????????????', '????????????', '??????????'],
                        monthsShort: ['??????', '??????', '??????', '??????', '??????', '????????', '????????', '??????', '??????', '??????', '??????', '??????'],
                        weekdays: ['??????????', '??????', '??????????', '??????????', '??????????', '????????', '??????'],
                        weekdaysShort: ['??\'', '??\'', '??\'', '??\'', '??\'', '??\'', '??????'],
                        weekdaysMin: ['??\'', '??\'', '??\'', '??\'', '??\'', '??\'', '??????'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                }
            },
            showLoader: false
        }
    },
    computed: {
        floorsDisabled() {
            return this.selectedStructures.length > 1
        },
        placesDisabled() {
            return (this.selectedStructures.length > 1) || (this.selectedFloors.length > 1)
        },
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        canGenerate() {
            return (this.selectedCompany && this.selectedProject && this.selectedStructures.length)
        },
        getIds(selectedItem) {
            return this[selectedItem].map(item => item.id)
        }
    },
    watch: {
        selectedCompany(val) {
            if (val) {
                this.cmpProjects = Object.values(val.projects);
                if(this.filters?.selectedProject) {
                    this.selectedProject = this.filters.selectedProject
                    this.filters.selectedProject = null;
                } else if(this.queryProjectId) {
                    this.selectedProject = this.cmpProjects.filter(project => +project.id === +this.queryProjectId)[0]
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
                this.getProject(this.selectedProject.id)
            } else {
                this.selectedProject = null
                this.project = [];
                this.structures = [];
                this.floors = [];
                this.places = [];
                this.elements = [];
                this.crafts = [];
                this.positions = [];
                this.selectedStructures = [];
                this.selectedFloors = [];
                this.selectedPlaces = [];
                this.selectedElements = [];
                this.selectedCrafts = [];
                this.selectedPositions = [];
            }
        },
        project(val) {
            if(Object.keys(val).length) {
                this.getStructures();
                this.getElements();
                this.getCompanyCrafts();
                this.getPositions();
            }
        },
        selectedStructures(val) {
            if (val.length) {
                if (val.length === 1) {
                    this.getStructureFloors()
                } else {
                    this.emptyFilter('selectedFloors', 'floors')
                }
            } else {
                this.emptyFilter('selectedFloors', 'floors')
            }
        },
        selectedFloors(val) {
            if (val.length) {
                if (val.length === 1) {
                    this.getFloorPlaces()
                } else {
                    this.emptyFilter('selectedPlaces', 'places')
                }
            } else {
                this.emptyFilter('selectedPlaces', 'places')
            }
        },
    },
    methods: {
        timeChanged() {},
        emptyFilter(selected, list) {
            this[selected] = [];
            this[list] = [];
        },
        getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                        if(this.queryProjectId) {
                            this.selectedCompany = this.companies.filter(company => {
                                return ((Object.values(company.projects).filter(project => +project.id === +this.queryProjectId)).length > 0)
                            })[0]

                        }
                    this.showLoader = false;
                })
        },
        getCmpProjects(){
            if (!this.selectedCompany) return
            this.showLoader = true;
            let url = `/companies/${this.selectedCompany.id}/entities/projects`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.cmpProjects = response.items ? response.items : [];
                    if(this.filters?.selectedProject) {
                        this.selectedProject = this.filters.selectedProject
                        this.filters.selectedProject = null;
                    }
                    this.showLoader = false;
                })
        },
        // async getProjectsbyIds(ids) {
        //     if (!this.selectedCompany) return;
        //     this.showLoader = true;
        //     let projects = [];
        //     for(let id of ids) {
        //         let url = `/projects/${id}/entities/project?fields=id,name`;
        //         let result = await qfetch(url, {method: 'GET', headers: {}})
        //         projects.push(result.item)
        //     }
        //     this.showLoader = false;
        //     this.cmpProjects = projects;
        //     if(this.filters?.selectedProject) {
        //         this.selectedProject = this.filters.selectedProject
        //         this.filters.selectedProject = null;
        //     }
        // },
        getProject(id) {
            this.showLoader = true;
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                    this.showLoader = false;
                })
        },
        getStructures() {
            this.showLoader = true;
            let url = `/projects/${this.project.id}/entities/objects?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.structures = response.items;
                    if(this.filters?.selectedStructures) {
                        this.selectedStructures = this.filters.selectedStructures;
                        this.selectedStructures.forEach(structure => {
                            this.onSelect(structure, 'structures')
                        })
                        this.filters.selectedStructures = null;
                    } else {
                        this.toggleSelectAll('selectedStructures', 'structures');
                    }
                    this.showLoader = false;
                })
        },
        getStructureFloors() {
            this.showLoader = true;
            let url = `/projects/entities/objects/${this.selectedStructures[0].id}/floors?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.floors = response.items;
                    if(this.filters?.selectedFloors) {
                        this.selectedFloors = this.filters.selectedFloors;
                        this.selectedFloors.forEach(floor => {
                            this.onSelect(floor, 'floors')
                        })
                        this.filters.selectedFloors = null;
                    }
                    this.showLoader = false;
                })
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
        getFloorsMultiselectSelectionValue(values) {
            let vals = [];
            values.forEach(val => {
                vals.push(!val.custom_name ? val.number : val.custom_name)
            });
            return vals.join(', ');
        },
        getFloorPlaces() {
            this.showLoader = true;
            let url = `/projects/entities/floors/${this.selectedFloors[0].id}/places?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.places = response.items;
                    if(this.filters?.selectedPlaces) {
                        this.selectedPlaces = this.filters.selectedPlaces;
                        this.selectedPlaces.forEach(place => {
                            this.onSelect(place, 'places')
                        })
                        this.filters.selectedPlaces = null;
                    }
                    this.showLoader = false;
                })
        },
        getElements(){
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/elements`;
            let param = encodeURIComponent('?search=')
            url +=  param;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.elements = response.items;
                    if(this.filters?.selectedElements) {
                        this.selectedElements = this.filters.selectedElements;
                        this.selectedElements.forEach(element => {
                            this.onSelect(element, 'elements')
                        })
                        this.filters.selectedElements = null;
                    } else {
                        this.toggleSelectAll('selectedElements', 'elements');
                    }
                    this.showLoader = false;
                })
        },
        getCompanyCrafts() {
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
        getStatuses(statusesArr) {
            let statuses = [];
            statusesArr.forEach((item, ind) => {
                statuses.push({ id: ind, name: item })
            })
            return statuses;
        },
        getPositions() {
            this.showLoader = true;
            let url = `/el-approvals/positions/${this.project.id}`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.positions = response.items;
                    if(!this.positions.length) {
                        this.selectedPositions = [];
                    }
                    if(this.filters?.selectedPositions) {
                        this.selectedPositions = this.filters.selectedPositions;
                        this.selectedPositions.forEach(position => {
                            this.onSelect(position, 'positions')
                        })
                        this.filters.selectedPositions = null;
                    }
                    // this.toggleSelectAll('selectedPositions', 'positions');
                    this.showLoader = false;
                })
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        getFilters() {
           return {
               selectedCompany:  this.selectedCompany,
               selectedProject: this.selectedProject,
               selectedStructures: this.selectedStructures,
               selectedElements: this.selectedElements,
               selectedCrafts: this.selectedCrafts,
               selectedPlaces: this.selectedPlaces,
               selectedFloors: this.selectedFloors,
               selectedManagerStatuses: this.selectedManagerStatuses,
               selectedStatuses: this.selectedStatuses,
               selectedPositions: this.selectedPositions,
               time: this.time,
               primarySupervision: this.primarySupervision,
               partialProcess: this.partialProcess
           }
        },
        getFiltersFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);

            return {
                companyId: urlParams.get('companyId') ? urlParams.get('companyId') : null,
                projectId: urlParams.get('projectId') ? urlParams.get('projectId') : null,
                objectIds: urlParams.get('objectIds') ? JSON.parse(urlParams.get('objectIds')) : null,
                elementIds: urlParams.get('elementIds') ? JSON.parse(urlParams.get('elementIds')) : null,
                specialityIds: urlParams.get('specialityIds') ? JSON.parse(urlParams.get('specialityIds')) : null,
                placeIds: urlParams.get('placeIds') ? JSON.parse(urlParams.get('placeIds')) : null,
                floorIds: urlParams.get('floorIds') ? JSON.parse(urlParams.get('floorIds')) : null,
                statuses: urlParams.get('statuses') ? JSON.parse(urlParams.get('statuses')) : null,
                positions: urlParams.get('positions') ? JSON.parse(urlParams.get('positions')) : null,
                from: urlParams.get('from') ? urlParams.get('from') : null,
                to: urlParams.get('to') ? urlParams.get('to') : null,
                primarySupervision: urlParams.get('primarySupervision') ? urlParams.get('primarySupervision') : false,
                partialProcess: urlParams.get('partialProcess') ? urlParams.get('partialProcess') : false,
            }
        },
        generateReports() {
            // console.log(this.getFilters());
            // return false;
            this.$emit('getFiltersForReportsGenerating', {
                filters: this.getFilters(),
                page: 1
            })
        }
    },
    created() {
        let date = new Date();
        let end = new Date();
        let url = window.location.pathname;
        let id = url.substring(url.lastIndexOf('/') + 1);

        date.setDate(1);
        date.setMonth(date.getMonth()-6);
        end.setDate(end.getDate() + 1);
        this.time = [date, end];

        this.getCompanies();
    },
    mounted() {
        if(this.filters) {
            this.selectedCompany = this.filters.selectedCompany
            this.selectedProject = this.filters.selectedProject
            this.selectedStructures = this.filters.selectedStructures
            this.selectedElements = this.filters.selectedElements
            this.selectedCrafts = this.filters.selectedCrafts
            this.selectedPlaces = this.filters.selectedPlaces
            this.selectedFloors = this.filters.selectedFloors
            this.selectedManagerStatuses = this.filters.selectedManagerStatuses
            this.selectedStatuses = this.filters.selectedStatuses
            this.primarySupervision = this.filters.primarySupervision
            this.partialProcess = this.filters.partialProcess

            this.selectedPositions = this.filters.selectedPositions
            this.time = this.filters.time

            this.selectedStatuses.forEach(elStatus => {
                this.onSelect(elStatus, 'elStatuses')
            })
            this.selectedManagerStatuses.forEach(managerStatus => {
                this.onSelect(managerStatus, 'elManagerStatuses')
            })

            let url = location.href.split("/");
            if(url.length === 6) {
                url.pop();
                window.history.replaceState('object', document.title, url.join('/'));
            }
        } else {
            if(window.location.search) {
                this.$emit('getFiltersForReportsGenerating', this.getFiltersFromUrl())
            } else {
                if(this.projectId) {
                    this.queryProjectId = this.projectId
                }
            }

            this.toggleSelectAll('selectedManagerStatuses', 'elManagerStatuses');
            this.toggleSelectAll('selectedStatuses', 'elStatuses');
        }

    }
});

