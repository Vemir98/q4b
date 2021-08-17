Vue.component('approve-elements-tab', {
    template: `
        <div id="approve-elements-content">
            <template v-if="activeTab === 'generate-reports'">
                <generate-reports
                    :statuses='statuses'
                    :translations='translations'
                    @getFiltersForReportsGenerating="generateReports"
                />
            </template>
            <template v-else-if="activeTab === 'reports-list'">
                <reports-list
                    :project="project"
                    :company="company"
                    :filters="filters"
                    :translations='translations'
                    @tabChanged="activeTab = 'generate-reports'"
                    @toReportDetails="goToReportDetails"
                />
            </template>
            <template v-else-if="activeTab === 'report-item'">
                <report-item
                    :project="project"
                    :company="company"
                    :username="username"
                    :data="report"
                    :filters="filters"
                    :translations='translations'
                    @toReportsList="activeTab = 'reports-list'"
                />
            </template>
        </div>
    `,
    props: {
        username: {required: true},
        statuses: {required: true},
        translations: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            activeTab: 'generate-reports',
            report: {},
            project: {},
            company: {},
            filters: {}
        }
    },
    methods: {
        async generateReports(filters) {
            this.filters = filters;

            await this.getProject(this.filters.project_id)
            await this.getCompanies(this.filters.company_id)
            this.activeTab = 'reports-list';
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
        getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/list';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.company = response.items.filter(company => +company.id === this.filters.company_id)[0]
                    this.showLoader = false;
                })
        },
        goToReportDetails(report) {
            this.report = report;

            this.activeTab = 'report-item';
        }
    }
});

