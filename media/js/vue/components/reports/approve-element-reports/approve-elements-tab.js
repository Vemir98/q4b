Vue.component('approve-elements-tab', {
    template: `
        <div id="approve-elements-content">
                {{ userProfession }}

            <template v-if="activeTab === 'generate-reports'">
                <generate-reports
                    :statuses='statuses'
                    :translations='translations'
                    @getFiltersForReportsGenerating="generateReports"
                />
            </template>
            <template v-else-if="activeTab === 'reports-list'">
                <reports-list
                    :imageUrl="imageUrl"
                    :siteUrl="siteUrl"
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
                    :siteUrl="siteUrl"
                    :imageUrl="imageUrl"
                    :statuses="statuses"
                    :userRole="userRole"
                    :userProfession="userProfession"
                    :project="project"
                    :company="company"
                    :username="username"
                    :data="report"
                    :filters="filters"
                    :translations='translations'
                    @toReportsList="activeTab = 'reports-list'"
                    @reportDeleted="activeTab = 'reports-list'"
                />
            </template>
        </div>
    `,
    props: {
        userRole: {required: true},
        siteUrl: {required: true},
        imageUrl: {required: true},
        username: {required: true},
        statuses: {required: true},
        translations: {required: true},
        userProfession: {required: true}
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

            await this.getProject(this.filters.projectId)
            await this.getCompanies(this.filters.companyId)
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
                    this.company = response.items.filter(company => +company.id === +this.filters.companyId)[0]
                    this.showLoader = false;
                })
        },
        goToReportDetails(report) {
            report.updated = false;
            report.specialities.forEach(speciality => {
                speciality.canUpdateSignatures = false;
                speciality.canUpdateNote = false;
                speciality.canUpdateTaskStatuses = false;
            })
            this.report = report;

            this.activeTab = 'report-item';
        }
    }
});

