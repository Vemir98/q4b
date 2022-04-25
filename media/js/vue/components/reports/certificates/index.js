Vue.component('certificates-reports', {
    template: `
        <div id="certificates-reports-content">
            <template v-if="activeTab === 'generate'">
                <certificates-generate
                    :translations='translations'
                    :certStatuses="statuses"
                    :filters="filters"
                    @getFiltersForReportsGenerating="generateReports"
                />
            </template>
            <template v-else-if="activeTab === 'list'">
                <certificates-list
                    :imageUrl="imageUrl"
                    :siteUrl="siteUrl"
                    :project="project"
                    :company="company"
                    :filters="transformedFilters"
                    :translations='translations'
                    :page="page"
                    @tabChanged="activeTab = 'generate'"
                />
            </template>
        </div>
    `,
    props: {
        translations: {required: true},
        statuses: {required: true},
        siteUrl: {required: true},
        imageUrl: {required: true},
    },
    components: {
    },
    data() {
        return {
            activeTab: '',
            project: {},
            company: {},
            filters: null,
            transformedFilters: null,
            page: 1
        }
    },
    methods: {
        async generateReports({filters, page}) {
            this.filters = filters;
            this.page = page;
            //
            // console.log('FILTERS', filters)
            // return false;

            this.transformedFilters = {
                'companyId': +this.filters.selectedCompany.id,
                'projectId': +this.filters.selectedProject.id,
                'specialityIds': this.filters.selectedCrafts.map(craft => +craft.id),
                'statuses': this.filters.selectedStatuses.map(status => status.name),
                'sampleRequired': this.filters.sampleRequired,
            };

            // console.log('this.transformedFilters', this.transformedFilters)
            // return false;

            await this.getProjectAPI(this.transformedFilters.projectId)
            await this.getCompaniesAPI(this.transformedFilters.companyId)
            this.activeTab = 'list';
        },
        getProjectAPI(projectId) {
            this.showLoader = true;
            let url = `/projects/${projectId}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                    this.showLoader = false;
                })
        },
        getCompaniesAPI(companyId){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.company = response.items.filter(company => +company.id === +companyId)[0]
                    this.showLoader = false;
                })
        },
    },
    mounted() {
        if(window.location.search) {
            const urlParams = new URLSearchParams(window.location.search);

            const elAppId = urlParams.get('el_app_id') ? urlParams.get('el_app_id') : null;
            if(elAppId) {
                this.getReportAPI(elAppId)
            }
        } else {
            this.activeTab = 'generate';
        }
    }
});

