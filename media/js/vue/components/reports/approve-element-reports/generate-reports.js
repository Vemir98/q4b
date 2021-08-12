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
                    {{ trans.status }}
                        <label class="table_label" :class="{'labtest-disabled': !ltStatuses.length}">
                            <span @click="toggleSelectAll('selectedStatuses', 'ltStatuses')">
                                <template v-if="selectedStatuses.length < ltStatuses.length">
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
                            :disabled="!ltStatuses.length" 
                            :options="ltStatuses" 
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
                            @select="onSelect($event, 'ltStatuses')"
                            @remove="onRemove($event, 'ltStatuses')"
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
                    <div class="filter-item-label" >Positions[*]</div>
                    <div class="multiselect-col">
                        <multiselect 
                            :option-height="104" 
                            :placeholder="trans.set_positions" 
                            :disabled="options.length < 1" 
                            :options="options" 
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
            options: [
                {
                    id: 1,
                    "name": "San Martin",
                    "checked": false
                },
                {
                    id: 2,
                    "name": "San Nicolas",
                    "checked": false
                },
                {
                    id: 3,
                    "name": "San Francisco",
                    "checked": false
                }
            ],
            selectedCompany: null,
            selectedProject: null,
            selectedStructures: [],
            ltStatuses:  this.getStatuses(this.statuses),
            trans: JSON.parse(this.translations),
            selectedFloors: [],
            selectedPlaces: [],
            selectedElements: [],
            selectedCrafts: [],
            selectedStatuses: [],
            companies: [],
            cmpProjects: [],
            project: [],
            structures: [],
            floors: [],
            places: [],
            elements: [],
            crafts: [],
            txtPrivateResult : "",
            txtPublicResult : "",
            txtTotalResult : "",
            showDropDown: false,
            langs: {
                ru: {
                    formatLocale: {
                        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                        monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                        weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                        weekdaysShort: ['Вoс', 'Пон', 'Вто', 'Сре', 'Чет', 'Пят', 'Суб'],
                        weekdaysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                },
                he: {
                    formatLocale: {
                        months: ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'],
                        monthsShort: ['ינו', 'פבר', 'מרץ', 'אפר', 'מאי', 'יוני', 'יולי', 'אוג', 'ספט', 'אוק', 'נוב', 'דצמ'],
                        weekdays: ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'],
                        weekdaysShort: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
                        weekdaysMin: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
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
            return (this.selectedCompany && this.selectedProject)
        },
        getIds(selectedItem) {
            return this[selectedItem].map(item => item.id)
        }
    },
    watch: {
        selectedCompany(val) {
            if (val) {
                this.getCmpProjects()
            } else {
                this.selectedCompany = null
            }
            this.selectedProject = null;
        },
        selectedProject(val) {
            if (val) {
                this.getProject(this.selectedProject.id)
            } else {
                this.selectedProject = null
            }
        },
        project(val) {
            if(val) {
                this.getStructures();
                this.getElements();
                this.getCompanyCrafts();
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
                    this.emptyFilter('selectedPlace', 'places')
                }
            } else {
                this.emptyFilter('selectedPlace', 'places')
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
            let url = '/companies/entities/list';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
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
                    this.showLoader = false;
                })
        },
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
                    this.toggleSelectAll('selectedStructures', 'structures');
                    this.showLoader = false;
                })
        },
        getStructureFloors() {
            this.showLoader = true;
            let url = `/projects/entities/objects/${this.selectedStructures[0].id}/floors?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.floors = response.items;
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
            let index = this[objName].findIndex(item => item.id==option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => item.id==option.id);
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
                    this.toggleSelectAll('selectedElements', 'elements');
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
                    this.toggleSelectAll('selectedCrafts', 'crafts');
                    this.showLoader = false;
                })
        },
        getStatuses(statusesArr) {
            let statuses = [];
            statusesArr.forEach((item, ind) => {
                statuses.push({ id: ind, name: item })
            })
            return statuses
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        generateReports() {
            this.$emit('getFiltersForReportsGenerating', {
                'company_id': +this.selectedCompany.id,
                'project_id': +this.selectedProject.id,
                'object_ids': this.selectedStructures.map(structure => +structure.id),
                'element_ids': this.selectedElements.map(element => +element.id),
                'speciality_ids': this.selectedCrafts.map(craft => +craft.id),
                'place_ids': this.selectedPlaces.map(place => +place.id),
                'floor_ids': this.selectedFloors.map(floor => +floor.id),
                'statuses': this.selectedStatuses.map(status => status.id),
                'from': this.time[0] ? this.time[0].toLocaleDateString("en-GB") : '',
                'to': this.time[1] ? this.time[1].toLocaleDateString("en-GB") : ''
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
        this.toggleSelectAll('selectedStatuses', 'ltStatuses');
    }
});

