Vue.component('projects-list', {
    template: `


    <section class='lt_projects'>
    <div class="lt_page_title">{{ trans.lab_control_menu }}</div>

    <div class="lt_projects_select">
       <div class="form" >
            <div class="row-flex">
                <div class="multiselect-col">
                    <multiselect 
                        v-model="selectedCompany"  
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
                    </multiselect>                
                </div>
            </div>
        </div>
    </div>

    <div class="lt_projects_container">
        <div class="lt_projects_wraper">
            <div v-if="showLoader" class="loader_backdrop">
                <div class="loader"></div>
            </div>
                <project-item 
                    v-for="item in items" 
                    :item="item" 
                    :key="item.id"
                    :image="'/media/img/project_default'+ (Math.floor(Math.random() * 4) + 1) +'.png'"
                    :trans="trans"
                />
        </div>
    </div>
    <pagination 
        v-model="page" 
        :records="total" 
        :per-page="limit" 
        @paginate="paginate" 
        :options="{chunk:5,'chunksNavigation':'fixed'}"
        >
    </pagination>
</section>`,
    /** Props
     * siteUrl:"https://qforb.net/"
     * redirectUrl:"labtests/project/"
     * statuses: "["waiting", "approve", "non_approve"]"
     * translations:"{
     * "lab_control":"Lab control",
     * "select_project":"Select project",
     * "company":"Company",
     * "status":"Status",
     * "active":"Active",
     * "archive":"Archive",
     * "suspended":"Suspended",
     * "start_date":"Start Date",
     * "end_date":"End Date"
     * }"
     */
    props: {
        siteUrl: {required: true},
        translations: {required: true},
        redirectUrl: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            items: [],
            page: 1,
            total: 0,
            limit: 0,
            projects: [],
            selectedCompany: null,
            showLoader: false
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        }
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
    },
    mounted() {
        this.getProjects();
        this.getAllProjects();
    },
    watch: {
        'selectedCompany': function(){
            this.redirectTo()
        }
    },
    methods: {
        paginate(event){
          this.getProjects()
        },
        getProjects(){
            this.showLoader = true;
            let page = this.page
            let url = '/projects/entities/labtests_projects';
            if(page > 1){
                url += '/page/' + page;
            }
            if(this.selectedCompany){
               url += '?company_id=' + this.selectedCompany.id
            }
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.items = response.items;
                    this.total = parseInt(response.pagination.total)
                    this.limit = parseInt(response.pagination.limit)
                    this.showLoader = false;
                })
        },
        redirectTo(){
            if(this.selectedCompany.id){
                window.location.href = this.siteUrl + this.redirectUrl + this.selectedCompany.id
            }
        },
        getAllProjects(){
            qfetch('/projects/entities/labtests_all_projects_list', {method: 'GET', headers: {}})
                .then(response => {
                    this.projects = response.items;
                });
        },
    },
});

