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
                <img 
                    :src="project.mainImage ? ('/' + project.mainImage.path + '/' + project.mainImage.name) : '/media/img/project-img.jpg'"
                    style="width: 100%;" alt="project images"
                >
            </div>
            <div class="report-project-desc-list flex-start">
                <ul class="flex-start">
                    <li>
                        <span class="dark-blue">
                            {{ trans.company_name }}
                        </span>
                        <span class="light-blue">
                            {{ company?.name }}
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
                            {{ filters.objectIds.length }}
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
<!--                <a  class="report-button excel" :href="getExportExcelHref" download="element-approval-reports.xls"><i class="q4bikon-report"></i>Excel</a>-->
                <a  class="report-button excel" :href="getExportExcelHref"><i class="q4bikon-report"></i>Excel</a>
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
                        <th>{{ trans.notes_description }}</th>
                        <th>{{ trans.craft }}</th>
                        <th>{{ trans.floor }}</th>
                        <th>{{ trans.status }}</th>
                        <th>{{ trans.approval_date }}</th>
<!--                        <th>{{ trans.position }}</th>-->
                        <th>{{ trans.signer_name }}</th>
                        <th>{{ trans.signature }}</th>
                        <th>{{ trans.more }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="report in items">
                        <tr class="parent-tr" :class="{'openParent': report.showSpecialities}" :key="report.id">
                            <td scope="row" @click="toggleReportSpecialities(report)" class="parent-td">{{ report.id }}</td>
                            <td>{{ convertTimestampToDate(report.createdAt) }} </td>
                            <td>{{ report.objectName }}</td>
                            <td>{{ report.elementName }}</td>
                            <td class="tab-description">{{ report.notice }}</td>
                            <td>&nbsp;</td>
                            <td class="td-floor">{{ report.floorName ? report.floorName : report.floorNumber  }}</td>
                            <td class="text-capitalize"> {{ +report.appropriate === 1 ? trans.appropriate : trans.not_appropriate }}</td>
                            <td>{{ report.managerSignature ? convertTimestampToDate(report.managerSignature.createdAt) : '&nbsp;' }}</td>
<!--                            <td>{{ report.managerSignature ? report.managerSignature.position : '&nbsp;' }}</td>-->
                            <td>{{ report.managerSignature ? report.managerSignature.name : '&nbsp;' }} {{ report.managerSignature ? report.managerSignature.position : '&nbsp;' }}</td>
                            <td class="td-sign tab-signature">
                                <img :src="report.managerSignature ? imageUrl+report.managerSignature.image : ''">
                            </td>
                            <td>
                                <button class="open-more" @click="toggleReportOptions(report)"><img src="/media/img/more-icon.svg" alt="">
                                    <div  class="td-options-wrap" v-if="report.showOptions">
                                        <a @click="goToReportDetails(report)"><i class="q4bikon-preview1"></i>{{ trans.view }}</a>
                                        <a 
                                            v-if="hasQc(report)"
                                            @click="getGenerateQcHref(report)"
                                        >
                                            <i class="q4bikon-uncheked" ></i>
                                            {{ trans.qc_report }}
                                        </a>
                                        <a v-else class="el-app-disabled">
                                            <i class="q4bikon-uncheked" ></i>
                                            {{ trans.qc_report }}
                                        </a>
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
                                <td>&nbsp;</td>
                                <td>{{ speciality.craftName }}</td>
                                <td>&nbsp;</td>
                                <td>{{ +speciality.appropriate ? trans.appropriate : trans.not_appropriate  }}</td>
                                <td>{{ speciality.signatures.length ? convertTimestampToDate(speciality.signatures[0]['createdAt']) : convertTimestampToDate(speciality.updatedAt) }}</td>
<!--                                <td>{{ speciality.signatures.length ? speciality.signatures[0]['position'] : '' }}</td>-->
                                <td>{{ speciality.signatures.length ? speciality.signatures[0]['name'] : '' }} {{ speciality.signatures.length ? speciality.signatures[0]['position'] : '' }}</td>
                                <td class="td-sign">
                                <img :src="speciality.signatures.length ? imageUrl+speciality.signatures[0]['image'] : ''">
                                </td>
                                <td class="td-view-qc">
                                    <i v-if="speciality.qualityControl" @click="getGenerateQcHref(report, speciality)" class="q4bikon-uncheked" style="cursor: pointer"></i>
                                    &nbsp;
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
        <pagination 
            v-model="currentPage" 
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
        company: {required: true},
        page: {required: true}
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
            currentPage: this.page,
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

            if(this.currentPage > 1) url += '/page/' + this.currentPage;

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
            const month = ((date.getMonth()+1) > 9) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        paginate() {
            this.getFilteredReports();
        },
        getQueryParamsOfFiltersForUrl() {
            let elements = encodeURIComponent(JSON.stringify(this.filters.elementIds));
            let floors = encodeURIComponent(JSON.stringify(this.filters.floorIds));
            let statuses = encodeURIComponent(JSON.stringify(this.filters.statuses));
            let positions = encodeURIComponent(JSON.stringify(this.filters.positions));
            let objects = encodeURIComponent(JSON.stringify(this.filters.objectIds));
            let places = encodeURIComponent(JSON.stringify(this.filters.placeIds));
            let crafts = encodeURIComponent(JSON.stringify(this.filters.specialityIds));
            let from = this.filters.from ? this.filters.from : '';
            let to = this.filters.to ? this.filters.to : '';

            return `?from=${from}&to=${to}&objectIds=${objects}&floorIds=${floors}&placeIds=${places}&specialityIds=${crafts}&elementIds=${elements}&statuses=${statuses}&positions=${positions}&companyId=${this.company.id}&projectId=${this.project.id}`;
        },
        goToReportDetails(report) {
            this.$emit('toReportDetails', {
                report: report,
                page: this.currentPage
            })
        },
        async getQcs(qcId) {
            this.showLoader = true;

            let url = '/quality-controls/get/'+qcId+'?fields=createdAt';
            try {
                let result = await qfetch(url, {method: 'GET', headers: {}});
                this.showLoader = false;
                return result.item.createdAt;
            } catch (e) {
                this.showLoader = false;
                console.log(e)
            }
        },
        hasQc(report) {
           let result = report.specialities.filter(spec => {
               return spec.qualityControl;
           })

            return result.length > 0
        },
        async getGenerateQcHref(report, speciality) {
            let url = `${this.siteUrl}/reports/generate`;

            if(!this.hasQc(report)) {
                return false;
            }

            if(speciality) {
                this.showLoader = true;
                let result = await qfetch('/quality-controls/get/'+speciality.qualityControl+'?fields=createdAt', {method: 'GET', headers: {}});
                this.showLoader = false;

                let date = this.convertTimestampToDate(result.item.createdAt);
                let queryParams = `?from=${date}&to=${date}&crafts[]=${speciality.craftId}&company=${this.company.id}&project=${this.project.id}&el_app_id=${report.id}#tab_qc_controls`;
                url += queryParams;

                window.open(url);
            } else {

                let range = [];
                this.showLoader = true;

                for(spec of report.specialities) {
                    if(spec.qualityControl) {
                        let url = '/quality-controls/get/'+spec.qualityControl+'?fields=createdAt';
                        let result = await qfetch(url, {method: 'GET', headers: {}});
                        range.push(+result.item.createdAt)
                    }
                }
                this.showLoader = false;

                let from = this.convertTimestampToDate(''+Math.max(...range));
                let to = this.convertTimestampToDate(Math.min(...range));

                let specIds = report.specialities.map(spec => 'crafts[]=' +spec.craftId)

                if(!specIds.length) {
                    return false;
                }

                let queryParams = `?from=${from}&to=${to}&${specIds.join('&')}&company=${this.company.id}&project=${this.project.id}&el_app_id=${report.id}#tab_qc_controls`;
                url += queryParams;

                window.open(url);
            }
        },
    },
    mounted() {
        this.getFilteredReports();
    }
});

