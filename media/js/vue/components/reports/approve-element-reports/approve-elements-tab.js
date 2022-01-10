Vue.component('approve-elements-tab', {
    template: `
        <div id="approve-elements-content">
            <template v-if="activeTab === 'generate-reports'">
                <generate-reports
                    :statuses='statuses'
                    :translations='translations'
                    :filters="filters"
                    :projectId="projectId"
                    @getFiltersForReportsGenerating="generateReports"
                />
            </template>
            <template v-else-if="activeTab === 'reports-list'">
                <reports-list
                    :imageUrl="imageUrl"
                    :siteUrl="siteUrl"
                    :project="project"
                    :company="company"
                    :filters="transformedFilters"
                    :translations='translations'
                    :page="page"
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
        userProfession: {required: true},
        projectId: {required: true}
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
            filters: null,
            transformedFilters: {},
            page: 1
        }
    },
    methods: {
        async generateReports({filters, page}) {
            this.filters = filters;
            this.page = page;

            this.transformedFilters = {
                'companyId': +this.filters.selectedCompany.id,
                'projectId': +this.filters.selectedProject.id,
                'objectIds': this.filters.selectedStructures.map(structure => +structure.id),
                'elementIds': this.filters.selectedElements.map(element => +element.id),
                'specialityIds': this.filters.selectedCrafts.map(craft => +craft.id),
                'placeIds': this.filters.selectedPlaces.map(place => +place.id),
                'floorIds': this.filters.selectedFloors.map(floor => +floor.id),
                'managerStatuses': this.filters.selectedManagerStatuses.map(status => status.name.toLowerCase()),
                'statuses': this.filters.selectedStatuses.map(status => +status.id),
                'positions': this.filters.selectedPositions.map(position => position.name),
                'from': this.filters.time[0] ? this.filters.time[0].toLocaleDateString("en-GB") : '',
                'to': this.filters.time[1] ? this.filters.time[1].toLocaleDateString("en-GB") : '',
                'primarySupervision': this.filters.primarySupervision
            };

            await this.getProject(this.transformedFilters.projectId)
            await this.getCompanies(this.transformedFilters.companyId)
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
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.company = response.items.filter(company => +company.id === +this.transformedFilters.companyId)[0]
                    this.showLoader = false;
                })
        },
        goToReportDetails(data) {
            data.report.updated = false;
            data.report.specialities.forEach(speciality => {
                speciality.canUpdateSignatures = false;
                speciality.canUpdateNote = false;
                speciality.canUpdateTaskStatuses = false;
            })
            this.report = data.report;

            this.page = data.page;
            this.activeTab = 'report-item';
        }
    }
});

