Vue.component('element-approval-create', {
    template: `
        <div id="element-approval-create-content">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="page-title-sec flex-start">
                <div class="page-title"> {{ trans.create_lab_control }} </div>
            </div>
            <div class="filters-section">
                <div class="filters-wraper flex-start">
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.structure }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedStructure"
                                :option-height="104" 
                                :placeholder="trans.select_structure" 
                                :disabled="structures.length < 1" 
                                :options="structures" 
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
                            <div class="filter-item-label" >{{ trans.floor }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedFloor"
                                :option-height="104" 
                                :placeholder="trans.select_floor" 
                                :disabled="floors.length < 1" 
                                :options="floors" 
                                track-by="id" 
                                label="name" 
                                :searchable="true" 
                                :allow-empty="false"
                                :show-labels="false"
                            >
                                <template slot="singleLabel" slot-scope="props">{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</template>
                                <template slot="option" slot-scope="props">
                                    <span>{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</span>
                                </template>
                                <template slot="option-selected" slot-scope="props">
                                    <span>{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</span>
                                </template>
                            </multiselect>
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.place }}</div>
                            <multiselect 
                                v-model="selectedPlace"
                                :option-height="104" 
                                :placeholder="trans.select_place" 
                                :disabled="places.length < 1" 
                                :options="places" 
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
                            <div class="filter-item-label" >{{ trans.element }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedElement"
                                :option-height="104" 
                                :placeholder="trans.select_element" 
                                :disabled="elements.length < 1" 
                                :options="elements" 
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
                </div>
                <div class="filters-wraper">
                    <div class="text-area-field">
                        <div class="text-area-field-label">{{ trans.notes_description }}</div>
                        <textarea cols="30" rows="10"></textarea>
                    </div>
                    <div class="specialities-section">
                        <div class="specialities-title">{{ trans.crafts }}</div>
                        <div class="specialities-content" v-if="elementCrafts.length">
                        <template v-for="craft in elementCrafts">
                            <report-speciality
                                :key="craft.id"
                                :userRole="userRole"
                                :siteUrl="siteUrl"
                                :taskStatuses="taskStatuses"
                                :translations="translations"
                                :projectId="projectId"
                                :userProfession="userProfession"
                                :username="username"
                                :initialSpeciality="craft"
                            />
                        </template>
                        </div>
                    </div>
                </div>
            </div>
            
            {{ elementCrafts }}
        </div>
    `,
    props: {
        userRole: {required: true},
        siteUrl: {required: true},
        imageUrl: {required: true},
        username: {required: true},
        statuses: {required: true},
        translations: {required: true},
        userProfession: {required: true},
        projectId: {required: true},

    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            taskStatuses: JSON.parse(this.statuses),
            showLoader: false,
            structures: [],
            floors: [],
            places: [],
            elements: [],
            crafts: [],
            tasks: [],
            selectedProject: null,
            selectedStructure: null,
            selectedFloor: null,
            selectedPlace: null,
            selectedElement: null,
        }
    },
    computed: {
        elementCrafts() {
            if(this.selectedElement) {

                let elementCrafts = this.crafts.filter(craft => {
                    return this.selectedElement.crafts.includes(craft.id)
                })

                return elementCrafts.filter(elementCraft => {
                    return (this.tasks.filter(task => {
                        return (task.crafts.filter(taskCraft => {
                            return +taskCraft.id === +elementCraft.id
                        }).length > 0)
                    }).length > 0)
                })
            } else {
                return []
            }
        }
    },
    watch: {
        selectedStructure(structure) {
            if (structure) {
                this.selectedFloor = null;
                this.selectedPlace = null;
                this.floors = [];
                this.places = [];
                this.getStructureFloorsAPI();
            }
        },
        selectedFloor(floor) {
            if (floor) {
                this.selectedPlace = null;
                this.places = [];
                this.getFloorPlacesAPI();
            }
        },
        selectedElement(element) {

        },
    },
    methods: {
        getProjectAPI(id) {
            this.showLoader = true;
            let url = `/projects/${id}/entities/project?fields=id,name,company_id`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.selectedProject = response.item;
                    this.showLoader = false;
                    if(this.selectedProject) {
                        this.getCompanyCraftsAPI(this.selectedProject?.company_id);
                    }
                })
        },
        getCompanyCraftsAPI(companyId) {
            this.showLoader = true;
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${companyId}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.crafts = response.items.map(craft => {
                        craft.name = craft.name.trim();
                        return craft;
                    });
                    console.log('CRAFTS', this.crafts)
                    this.showLoader = false;
                })
        },
        getStructuresAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/entities/objects?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.structures = response.items;
                    this.showLoader = false;
                })
        },
        getElementsAPI(){
            this.showLoader = true;
            let url = `/projects/${this.projectId}/labtests/elements_with_crafts`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.elements = response.items;
                    this.showLoader = false;
                })
        },
        getStructureFloorsAPI() {
            this.showLoader = true;
            let url = `/projects/entities/objects/${this.selectedStructure.id}/floors?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.floors = response.items;
                    this.showLoader = false;
                })
        },
        getFloorPlacesAPI() {
            this.showLoader = true;
            let url = `/projects/entities/floors/${this.selectedFloor.id}/places?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.places = response.items;
                    this.showLoader = false;
                })
        },
        getTasksAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/tasks/list`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.tasks = response.items.filter(task => {
                        return task.status === this.taskStatuses.Enabled
                    })
                })
        },
    },
    created() {
        this.getProjectAPI(this.projectId);

        this.getStructuresAPI();
        this.getElementsAPI();
        this.getTasksAPI();
    },
    mounted() {

    }
});

