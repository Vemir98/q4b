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
                    :data="item"
                    :filters="filters"
                    :translations='translations'
                    @tabChanged="activeTab = 'generate-reports'"
                    @toReportDetails="goToReportDetails"
                />
            </template>
            <template v-else-if="activeTab === 'report-item'">
                <report-item
                    :data="report"
                    :filters="filters"
                    :translations='translations'
                    @toReportsList="activeTab = 'reports-list'"
                />
            </template>
        </div>
    `,
    props: {
        data: {required: true},
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
            item: [],
            report: {},
            filters: {}
        }
    },
    methods: {
        generateReports(filters) {
            this.item = this.data;
            this.filters = filters;
            this.activeTab = 'reports-list';
        },
        goToReportDetails(reportId) {
            this.report = this.item.reports.filter(report => {
                return report.id === reportId
            })[0];

            this.activeTab = 'report-item';
        }
    }
});

