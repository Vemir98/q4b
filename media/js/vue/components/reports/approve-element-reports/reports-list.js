Vue.component('reports-list', {
    template: `
    <section class='q4b-approve-el app-element-list new-styles'>
        <div v-if="showLoader" class="loader_backdrop_vue">
            <div class="loader"></div>
        </div>
        <div class="page-title-sec flex-start">
            <div class="page-title">
                <a class="back-to-filter">
                    <i 
                        class="q4bikon-arrow_back2"
                        @click="$emit('tabChanged')"
                    ></i>
                </a>
                {{ trans.approve_element }} / 
                <span class="project_name">{{ project?.name }}</span>
            </div>
        </div>
        <div class="report-project-desc_wraper flex-start" v-if="Object.keys(project).length">
            <div class="report-project-desc-image">
                <img :src="'/' + project.mainImage.path + '/' + project.mainImage.name" style="width: 100%;" alt="project images">
            </div>
            <div class="report-project-desc-list flex-start">
                <ul class="flex-start">
                    <li>
                        <span class="dark-blue">
                            {{ trans.company_name }}
                        </span>
                        <span class="light-blue">
                            {{ company.name }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_name }}
                        </span>
                        <span class="light-blue">
                            {{ project.name }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.owner }}
                        </span>
                        <span class="light-blue">
                            {{ project.owner }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.start_date }}
                        </span>
                        <span class="light-blue">
                            {{ convertTimestampToDate(project.start_date) }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.end_date }}
                        </span>
                        <span class="light-blue">
                            {{ convertTimestampToDate(project.end_date) }}
                        </span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_id }}
                        </span>
                        <span class="light-blue">
                            {{ project.id }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_status }}
                        </span>
                        <span class="light-blue">
                            {{ project.status }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.address }}
                        </span>
                        <span class="light-blue">
                            {{ project.address }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.structures_quantity }}
                        </span>
                        <span class="light-blue">
                            {{ filters.object_ids.length }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue ">
                            {{ trans.report_range }}
                        </span>
                        <span class="light-blue ">
                            <span>{{ filters.from }} - {{filters.to}}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="report-buttons">
            <div class="report-buttons-wraper" :class="{'open': toggleExportButton}" @click="toggleExportButton = !toggleExportButton">
                <span class="report-buttons-headline"><i class="q4bikon-share"></i>{{ trans.export }}</span>
<!--                <a class="report-button pdf" :href="getExportPdfHref"><i class="q4bikon-file1" download="element-approval-reports.pdf"></i>PDF</a>-->
                <a class="report-button pdf" style="opacity: .5;cursor: auto"><i class="q4bikon-file1"></i>PDF</a>
                <a  class="report-button excel" :href="getExportExcelHref" download="element-approval-reports.xls"><i class="q4bikon-report"></i>Excel</a>
<!--                <a  class="report-button excel" :href="getExportExcelHref"><i class="q4bikon-report"></i>Excel</a>-->
<!--                <a :href="getExportHref" download="lab-reports.xls">{{ trans.export }}</a>-->

            </div>
        </div>

        <div class="report-list-wraper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans.check_number }}</th>
                        <th>{{ trans.check_date }}</th>
                        <th>{{ trans.structure }}</th>
                        <th>{{ trans.element }}</th>
                        <th>{{ trans.craft }}</th>
                        <th>{{ trans.floor }}</th>
                        <th>{{ trans.status }}</th>
                        <th>{{ trans.approval_date }}</th>
                        <th>{{ trans.position }}</th>
                        <th>{{ trans.signer_name }}</th>
                        <th>{{ trans.signature }}</th>
                        <th>{{ trans.more }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="report in items">
                        <tr class="parent-tr" :class="{'openParent': report.showSpecialities}">
                            <td scope="row" @click="toggleReportSpecialities(report)" class="parent-td">{{ report.id }}</td>
                            <td>{{ convertTimestampToDate(report.created_at) }} </td>
                            <td>{{ report.object_name }}</td>
                            <td>{{ report.element_name }}</td>
                            <td>&nbsp;</td>
                            <td class="td-floor">{{ report.floor_name ? report.floor_name : report.floor_number  }}</td>
                            <td class="text-capitalize"> {{ +report.appropriate === 1 ? trans.appropriate : trans.not_appropriate }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <button class="open-more" @click="toggleReportOptions(report)"><img src="/media/img/more-icon.svg" alt="">
                                    <div  class="td-options-wrap" v-if="report.showOptions">
                                        <a @click="$emit('toReportDetails', report)"><i class="q4bikon-preview1"></i>{{ trans.view }}</a>
                                        <a style="opacity: .5;cursor: auto"><i class="q4bikon-uncheked" ></i>{{ trans.qc_report }}</a>
                                    </div>
                                </button>
                            </td>
                        </tr>
                        <template v-for="speciality in report.specialities">
                            <tr class="child-tr" v-if="report.showSpecialities">
                                <td scope="row">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>{{ speciality.craft_name }}</td>
                                <td>&nbsp;</td>
                                <td>{{ +speciality.appropriate ? trans.appropriate : trans.not_appropriate  }}</td>
                                <td>{{ speciality.signatures.length ? convertTimestampToDate(speciality.signatures[0]['created_at']) : convertTimestampToDate(speciality.updated_at) }}</td>
                                <td>{{ speciality.signatures.length ? speciality.signatures[0]['position'] : '' }}</td>
                                <td>{{ speciality.signatures.length ? speciality.signatures[0]['creator_name'] : '' }}</td>
                                <td class="td-sign">
                                <img :src="speciality.signatures.length ? imageUrl+speciality.signatures[0]['image'] : ''">
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
        <pagination 
            v-model="page" 
            :records="total" 
            :per-page="limit" 
            @paginate="paginate" 
            :options="{chunk:5,'chunksNavigation':'fixed'}"
        >          
        </pagination>
    </section>
    `,
    props: {
        siteUrl: {required: true},
        imageUrl: {required: true},
        translations: {required: true},
        filters: {required: true},
        project: {required: true},
        company: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            showLoader: false,
            items: [],
            toggleExportButton: false,
            trans: JSON.parse(this.translations),
            page: 1,
            total: 0,
            limit: 0,
        }
    },

    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        getExportExcelHref() {
            let url = `${this.siteUrl}${API_PATH}/projects/${this.project.id}/el-approvals/export_xls`;
            url += this.getQueryParamsOfFiltersForUrl();
            url += `&lang=${this.currentLang}`
            return url;
        },
        getExportPdfHref() {
            let url = `${this.siteUrl}${API_PATH}/projects/${this.project.id}/el-approvals/export_pdf`;
            url += this.getQueryParamsOfFiltersForUrl();
            url += `&lang=${this.currentLang}`
            return url;
        },
    },
    methods: {
        toggleReportSpecialities(report) {
            report.showSpecialities = !report.showSpecialities

        },
        toggleReportOptions(report) {
            report.showOptions = !report.showOptions
        },
        getFilteredReports() {
            this.showLoader = true;
            let url = `/el-approvals/list`;

            if(this.page > 1) url += '/page/' + this.page;

            qfetch(url, {method: 'POST', headers: {}, body: this.filters})
                .then((response) => {
                    response.items.forEach(report => {
                        report.showSpecialities = false;
                        report.showOptions = false;
                    })
                    this.items = response.items;
                    this.total = response.pagination.total ? parseInt(response.pagination.total) : 0;
                    this.limit = response.pagination.limit ? parseInt(response.pagination.limit) : 0;
                    this.showLoader = false;
                });
        },
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1).length > 1) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        paginate() {
            this.getFilteredReports();
        },
        getQueryParamsOfFiltersForUrl() {
            let elements = encodeURIComponent(JSON.stringify(this.filters.element_ids));
            let floors = encodeURIComponent(JSON.stringify(this.filters.floor_ids));
            let statuses = encodeURIComponent(JSON.stringify(this.filters.statuses));
            let positions = encodeURIComponent(JSON.stringify(this.filters.positions));
            let objects = encodeURIComponent(JSON.stringify(this.filters.object_ids));
            let places = encodeURIComponent(JSON.stringify(this.filters.place_ids));
            let crafts = encodeURIComponent(JSON.stringify(this.filters.speciality_ids));
            let from = this.filters.from ? this.filters.from : '';
            let to = this.filters.to ? this.filters.to : '';

            return `?from=${from}&to=${to}&object_ids=${objects}&floor_ids=${floors}&place_ids=${places}&speciality_ids=${crafts}&element_ids=${elements}&statuses=${statuses}&positions=${positions}&company_id=${this.company.id}&project_id=${this.project.id}`;
        },
    },
    mounted() {
        this.getFilteredReports();
    }
});

