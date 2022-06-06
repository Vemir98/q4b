Vue.component('pr-labtests-list', {
    template: `
        <section class='labtest_list'>
        <div class="elements_title_sec">
            <div class="lt_page_title">{{ trans.project_name }} / <span class="project_name"> {{ selectedProject ? selectedProject.name : '' }}</span></div>
            <div class="elements_title_left" v-if="selectedProject">
                <form class="elements-form" action="">
                    <i class="q4bikon-search1 icon"></i>
                    <input type="text" v-on:keyup.enter="getLabtests(); getLabtestsStatistics();" v-on:keydown.enter="getLabtests(); getLabtestsStatistics();"  v-model="search" class="qc-id-to-show q4-form-input" :placeholder="trans.search + '...'"
                        value="">
                    <a class="qc_serarch_btn rotate-180 cursor-pointer" @click.stop="getLabtests(); getLabtestsStatistics();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14"
                         fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M20 7.00567C20 6.8388 19.9312 6.67942 19.8169 6.55942L13.63 0.310044C13.3856 0.063169 12.99 0.063794 12.7462 0.310044C12.5019 0.556294 12.5019 0.956294 12.7462 1.20254L17.8669 6.37442H0.625C0.28 6.37442 0 6.65692 0 7.00567C0 7.35442 0.28 7.63692 0.625 7.63692H17.8663L12.7462 12.8088C12.5019 13.055 12.5025 13.455 12.7462 13.7013C12.9906 13.9475 13.3863 13.9475 13.63 13.7013L19.8169 7.45192C19.9338 7.33379 19.9981 7.1713 20 7.00567Z"
                                  fill="#9FA2B4" />
                        </svg>
                    </a>
                </form>
            </div>
        </div>
        <div class="labtest_filters">
                <div class="labtest_filters1 row-flex">
                    <div class="labtest_filters1_left" style="max-width: 100%">
                        <div class="labtest_filter_input" v-if="!projectId">
                            <label class="filter-item-label" :class="{'labtest-disabled': !companies.length}">{{ trans.company }}</label>
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
                        <div class="labtest_filter_input" v-if="!projectId">
                            <label class="filter-item-label" :class="{'labtest-disabled': !ltStatuses.length}">{{ trans.project }}</label>
                            <multiselect 
                                v-model="selectedProject"
                                :option-height="104" 
                                :placeholder="trans.select_project" 
                                :disabled="(projects.length < 1)" 
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
                        <div class="labtest_filter_input">
                            <date-picker v-model="time" :lang="langs[currentLang]" :editable="false" :clearable="false" :disabled="false" @change="timeChanged" type="date" :range="true" format="DD/MM/YYYY"></date-picker>
                        </div>
                        <div class="labtest_filter_input">
                            <label class="table_label" :class="{'labtest-disabled': !ltStatuses.length}">
                                <span @click="toggleSelectAll('selectedStatus', 'ltStatuses')">
                                    <template v-if="selectedStatus.length < ltStatuses.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                                </span>
                            </label>
                            <multiselect v-model="selectedStatus"  
                                    :placeholder="trans.select_status" 
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
<!--                        <div class="labtest_filter_input">-->
                            <button class="labtest_filter_show_btn" @click="getLabtests();getLabtestsStatistics();">{{ trans.show }}</button>
<!--                        </div>-->
                        <div class="labtest_filters_export" v-if="items.length">
                            <a :href="getExportHref" download="lab-reports.xls">{{ trans.export }}</a>
                        </div>

                    </div>
                    <div class="labtest_filters1_right">
                        <!--<div class="labtest_filters_print"><button>Print</button></div>-->
<!--                        <div class="labtest_filters_export" v-if="items.length">-->
<!--                            <a class="report-button pdf" style="opacity: .5;cursor: auto"><i class="q4bikon-file1"></i>PDF</a>-->
<!--                        </div>-->
                        
                    </div>
                </div>
                <div class="labtest_filters2 row-flex">
                    <div class="labtest_filter_input">
                        <label class="table_label" :class="{'labtest-disabled': !structures.length}">
                            <span @click="toggleSelectAll('selectedStructure', 'structures')">
                               <template v-if="selectedStructure.length < structures.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                        <multiselect v-model="selectedStructure"  
                                :placeholder="trans.select_structure" 
                                :disabled="!structures.length" 
                                :options="structures" 
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
                    <div class="labtest_filter_input">
                        <label class="table_label" :class="{'labtest-disabled': !floors.length}">
                            <span @click="toggleSelectAll('selectedFloor', 'floors')">
                                <template v-if="selectedFloor.length < floors.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                        <multiselect v-model="selectedFloor"  
                                :placeholder="trans.select_floor_level" 
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
                  
                    <div class="labtest_filter_input">
                         <label class="table_label" :class="{'labtest-disabled': !places.length}">
                            <span @click="toggleSelectAll('selectedPlace', 'places')">
                                <template v-if="selectedPlace.length < places.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                        <multiselect v-model="selectedPlace"  
                                :placeholder="trans.select_place" 
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

                    <div class="labtest_filter_input">
                        <label class="table_label" :class="{'labtest-disabled': !elements.length}">
                            <span @click="toggleSelectAll('selectedElement', 'elements')">
                                <template v-if="selectedElement.length < elements.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                        <multiselect v-model="selectedElement"  
                                :placeholder="trans.select_element" 
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
                    
                     <div class="labtest_filter_input">                           
                        <label class="table_label" :class="{'labtest-disabled': !crafts.length}">
                            <span @click="toggleSelectAll('selectedCraft', 'crafts')">
                                <template v-if="selectedCraft.length < crafts.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                        <multiselect v-model="selectedCraft"  
                                :placeholder="trans.select_specialty" 
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
            </div>
        <div class="report-statistics" v-if="reportsStatistics !== null">
            <div class="report-statistics-item"><span>{{ trans.total }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.total }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.approved }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.approved }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.waiting }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.notApproved }}</span></div>
        </div>
            <div class="element_table" :class="{'empty': !showLoader && !items.length}">
                <div class="loader_backdrop" v-if="showLoader">
                    <div class="loader"></div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ trans.lab_control }}</th>
                            <th>{{ trans.lab_certificate_number }}</th>
                            <th>{{ trans.create_date }}</th>
                            <th>{{ trans.structure }}</th>
                            <th>{{ trans.floor_level }}</th>
                            <th>{{ trans.element }}</th>
                            <th>{{ trans.standard }}</th>
                            <th>{{ trans.lab_certificate }}</th>
                            <th>{{ trans.status }}</th>
                            <th>{{ trans.more }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="!showLoader">
                        <template v-for="(item, index) in items"> 
                            <labtest-list-item 
                            :itemData="item" 
                            :page="page"
                            :index="index"
                            :trans="trans"  
                            :project-id="selectedProject.id"
                            :from-projects="projectId ? true : false"
                            :filters="getFilters()"
                            @toggleMore="toggleMore"             
                            @deleteItem="deleteItem"
                            />                            
                        </template>
                       
                    </tbody>
                </table>
            </div>
            <confirm-modal 
                v-if="needToConfirm"
                :msg="msg" 
                :trans="trans" 
                :deletable="trans.lab_control"
                :deletable-id="deletable.id"
                :modal-data="modalData"
                @closeConfirm="needToConfirm=false"
                @deleteConfirmed="deleteConfirmed"
                >
            </confirm-modal>

            <pagination v-model="page" :records="total" :per-page="limit" @paginate="paginate" :options="{chunk:5,'chunksNavigation':'fixed'}"></pagination>
        </section>`,
    props: {
        siteUrl: {required: true},
        projectId: {required: false},
        translations: {required: true},
        statuses: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            ltStatuses:  this.getStatuses(JSON.parse(this.statuses)),
            search: '',
            time: [],
            items: [],
            reportsStatistics: null,
            page: +sessionStorage.getItem('labtests-page') || 1,
            total: 0,
            limit: 0,
            showLoader: true,
            selectedStatus: [],
            selectedCompany: null,
            selectedProject: null,
            selectedCraft: [],
            selectedPlace: [],
            selectedFloor: [],
            selectedStructure: [],
            selectedElement: [],
            objects: [],
            structures: [],
            floors: [],
            places: [],
            elements: [],
            crafts: [],
            companies: [],
            projects: [],
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
            needToConfirm: false,
            deletable: null,
            modalData: null,
            msg: ""
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        floorsDisabled() {
            return this.selectedStructure.length > 1
        },
        placesDisabled() {
            return (this.selectedStructure.length > 1) || (this.selectedFloor.length > 1)
        },
        getExportHref() {
            let url = `${this.siteUrl}${API_PATH}/projects/${this.selectedProject.id}/labtests/export_xls`;
            url += this.getUrlQueryParams();
            url += `&lang=${this.currentLang}`
            return url;
        },
    },
    created() {
        var date = new Date();
        date.setDate(1);
        date.setMonth(date.getMonth()-6);
        let end = new Date();
        end.setDate(end.getDate() + 1);
        this.time = [date, end];
        var url = window.location.pathname;
        var id = url.substring(url.lastIndexOf('/') + 1);

        this.getCompanies();
        if (this.projectId) {
            this.getProject(this.projectId);
        }
    },
    mounted() {
        this.toggleSelectAll('selectedStatus', 'ltStatuses');
    },
    watch: {
        selectedProject(val) {
            if(val) {
                 (async () => {
                    await this.getStructures();
                    await this.getElements();
                    await this.getCompanyCrafts();
                    if(this.projectId) {
                        this.getLabtestsStatistics();
                        await this.getLabtests();
                    }
                })()
            }
        },
        selectedCompany(val) {
            if(val) {
                this.projects = Object.values(val.projects);
            } else {
                this.projects = null;
            }
        },
        selectedStructure(val) {
          if (val.length) {
              if (val.length === 1) {
                  this.getObjectFloors()
              } else {
                  this.emptyFilter('selectedFloor', 'floors')
              }
          } else {
              this.emptyFilter('selectedFloor', 'floors')
          }
        },
        selectedFloor(val) {
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
        getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                    this.showLoader = false;
                })
        },
        emptyFilter(selected, list) {
            this[selected] = [];
            this[list] = [];
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        getFloorsMultiselectSelectionValue(values) {
            let vals = [];
            values.forEach(val => {
                vals.push(!val.custom_name ? val.number : val.custom_name)
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
        onSelect (option, objName) {
            let index = this[objName].findIndex(item => item.id==option.id);
            this[objName][index].checked = true;
        },
        onRemove (option, objName) {
            let index = this[objName].findIndex(item => item.id==option.id);
            this[objName][index].checked = false;
        },
        getStatuses(statusesArr) {
            let statuses = [];
            statusesArr.forEach((item, ind) => {
                statuses.push({ id: ind, name: item })
            })
            return statuses
        },
        timeChanged(){
            // console.log(this.time[0]);
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
        paginate(event){
          this.getLabtests()
        },
        getProject(id) {
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.selectedProject = response.item;
                })
        },
        getStructures() {
            let url = `/projects/${this.selectedProject.id}/entities/objects?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.structures = response.items;
                    this.toggleSelectAll('selectedStructure', 'structures');
                })
        },
        getObjectFloors() {
            let url = `/projects/entities/objects/${this.selectedStructure[0].id}/floors?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.floors = response.items;
                })
        },
        getFloorPlaces() {
            let url = `/projects/entities/floors/${this.selectedFloor[0].id}/places?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.places = response.items;
                })
        },
        deleteItem(data) {
            this.needToConfirm = true;
            let { index } = data;
            this.deletable = this.items[index];
            this.modalData = data;
            this.msg = `${this.trans.are_you_sure_to_delete}`
        },
        deleteConfirmed(data) {
            this.needToConfirm = false;
            let { index } = data;
            let items = [... this.items];
            let obj = items[index];
            if (obj.id) {
                this.sendDeleteRequest(obj.id);
            }
            items.splice(index,1);
            this.items = [...items];
        },
        sendDeleteRequest(id) {
            let url = `/projects/${this.selectedProject.id}/labtests/${id}`;
            qfetch(url, {method: 'DELETE', headers: {}});
        },
        getElements(){
            let url = `/projects/${this.selectedProject.id}/labtests/elements`;
            let param = encodeURIComponent('?search=')
            url +=  param;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.elements = response.items;
                    this.toggleSelectAll('selectedElement', 'elements');
                })
        },
        getCompanyCrafts() {
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${this.projectId ? this.selectedProject.company_id : this.selectedCompany.id}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.crafts = response.items;
                    this.toggleSelectAll('selectedCraft', 'crafts');
                })
        },
        getUrlQueryParams() {
            let object = []
            this.selectedStructure.forEach((i) => {object.push(i.id)});
            object = encodeURIComponent(JSON.stringify(object));
            let floor = [];
            this.selectedFloor.forEach((i) => {floor.push(i.id)});
            floor = encodeURIComponent(JSON.stringify(floor));
            let place = [];
            this.selectedPlace.forEach((i) => {place.push(i.id)});
            place = encodeURIComponent(JSON.stringify(place));
            let craft = [];
            this.selectedCraft.forEach((i) => {craft.push(i.id)});
            craft = encodeURIComponent(JSON.stringify(craft));
            let element = [];
            this.selectedElement.forEach((i) => {element.push(i.id)});
            element = encodeURIComponent(JSON.stringify(element));
            let status = [];
            this.selectedStatus.forEach((st) => {status.push(st.name)});
            status = encodeURIComponent(JSON.stringify(status));
            let search = this.search ? encodeURIComponent(this.search) : '';
            let from = this.time && this.time[0] ? this.time[0].toLocaleDateString("en-GB") : '';
            let to = this.time && this.time[1] ? this.time[1].toLocaleDateString("en-GB") : '';
            // return `?from=${from}&to=${to}&object_id=${object}&floor_id=${floor}&place_id=${place}&craft_id=${craft}&element_id=${element}&status=${status}&search=${search}`;
            return `?from=${from}&to=${to}&objectId=${object}&floorId=${floor}&placeId=${place}&craftId=${craft}&elementId=${element}&status=${status}&search=${search}`;
        },
        getLabtests(){
            this.showLoader = true;
            let url = `/projects/${this.selectedProject.id}/labtests`;

            let page = this.page
            if(page > 1){
                url += '/page/' + page;
            }
            url += this.getUrlQueryParams();

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let items = response.items;
                    items.forEach(i => i.more = false)
                    this.items = items;
                    this.items = response.items;
                    this.total = response.pagination.total ? parseInt(response.pagination.total) : 0;
                    this.limit = response.pagination.limit ? parseInt(response.pagination.limit) : 0;
                    this.showLoader = false;
                });
        },
        getFilters() {
            return {
                projectIds: [this.selectedProject.id],
                statuses: this.selectedStatus.map(status => status.name),
                objectIds: this.selectedStructure.map(structure => structure.id),
                floorIds: this.selectedFloor.map(floor => floor.id),
                placeIds: this.selectedPlace.map(place => place.id),
                elementIds: this.selectedElement.map(element => element.id),
                craftIds: this.selectedCraft.map(craft => craft.id),
                search: this.search,
                from: this.time && this.time[0] ? this.time[0].toLocaleDateString("en-GB") : '',
                to: this.time && this.time[1] ? this.time[1].toLocaleDateString("en-GB") : ''
            }
        },
        getLabtestsStatistics() {
            this.showLoader = true;
            let url = '/projects/statistics/labtests';

            let filters = this.getFilters();

            qfetch(url, {method: 'POST', headers: {}, body: filters})
                .then(response => {
                    this.reportsStatistics = response.item;
                    this.showLoader = false;
                });
        }
    },
});

