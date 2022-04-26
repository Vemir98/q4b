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
                            {{ filters?.objectIds?.length }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue ">
                            {{ trans.report_range }}
                        </span>
                        <span class="light-blue ">
                            <span>{{ filters?.from }} - {{filters?.to}}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="report-statistics">
            <div class="report-statistics-item"><span>{{ trans.total }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.total }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.approved }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.approved }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.waiting }}:</span><span class="q4b-text-bold"> {{ reportsStatistics?.notApproved }}</span></div>
        </div>
        <div class="report-buttons">
            <div class="report-search">
                <input type="text" v-model="searchValue" :placeholder="trans.search_by_element_number" @keydown.enter="getReportById">
                <i @click="getReportById">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14"
                         fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M20 7.00567C20 6.8388 19.9312 6.67942 19.8169 6.55942L13.63 0.310044C13.3856 0.063169 12.99 0.063794 12.7462 0.310044C12.5019 0.556294 12.5019 0.956294 12.7462 1.20254L17.8669 6.37442H0.625C0.28 6.37442 0 6.65692 0 7.00567C0 7.35442 0.28 7.63692 0.625 7.63692H17.8663L12.7462 12.8088C12.5019 13.055 12.5025 13.455 12.7462 13.7013C12.9906 13.9475 13.3863 13.9475 13.63 13.7013L19.8169 7.45192C19.9338 7.33379 19.9981 7.1713 20 7.00567Z"
                              fill="#9FA2B4" />
                    </svg>
                </i>
            </div>
            <div class="report-buttons-wraper" :class="{'open': toggleExportButton}" @click="toggleExportButton = !toggleExportButton">
                <span class="report-buttons-headline"><i class="q4bikon-share"></i>{{ trans.export }}</span>
                <a class="report-button pdf" style="opacity: .5;cursor: auto"><i class="q4bikon-file1"></i>PDF</a>
                <a  class="report-button excel" :href="getExportExcelHref"><i class="q4bikon-report"></i>Excel</a>
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
                            <td class="text-capitalize"> {{ getReportStatus(report) }}</td>
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
                                <td style="word-break: break-all" colspan="4">{{ speciality.notice }}</td>
                                <td>{{ +speciality.primarySupervision ? trans.primary_supervision : '&nbsp;' }}</td>
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
        <warning-popup
            v-if="showWarningPopup"
            :message="''"
            :translations="translations"
            @onClose="showWarningPopup = false"
        />
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
            reportsStatistics: null,
            projectId: this.project.id,
            searchValue: '',
            showWarningPopup: false
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
        }
    },
    watch: {
      // project(project) {
      //     if(this.filters) {
      //         this.getEarStatistics(project.id);
      //     }
      // }
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
            if(this.filters) {
                let elements = encodeURIComponent(JSON.stringify(this.filters.elementIds));
                let floors = encodeURIComponent(JSON.stringify(this.filters.floorIds));
                let statuses = encodeURIComponent(JSON.stringify(this.filters.statuses));
                let positions = encodeURIComponent(JSON.stringify(this.filters.positions));
                let objects = encodeURIComponent(JSON.stringify(this.filters.objectIds));
                let places = encodeURIComponent(JSON.stringify(this.filters.placeIds));
                let crafts = encodeURIComponent(JSON.stringify(this.filters.specialityIds));
                let from = this.filters.from ? this.filters.from : '';
                let to = this.filters.to ? this.filters.to : '';
                let primarySupervision = this.filters.primarySupervision ? encodeURIComponent(JSON.stringify('1')) : encodeURIComponent(JSON.stringify('0'));

                return `?from=${from}&to=${to}&objectIds=${objects}&floorIds=${floors}&placeIds=${places}&specialityIds=${crafts}&elementIds=${elements}&statuses=${statuses}&positions=${positions}&companyId=${this.company.id}&projectId=${this.project.id}&primarySupervision=${primarySupervision}`;
            } else {
                return '';
            }
        },
        goToReportDetails(report) {
            this.$emit('toReportDetails', {
                report: report,
                page: this.currentPage
            })
        },
        async getQcs(qcId) {
            this.showLoader = true;

            let url = '/quality-controls/get/'+qcId+'?fields=dueDate&all=true';
            try {
                let result = await qfetch(url, {method: 'GET', headers: {}});
                this.showLoader = false;
                return result.item.dueDate;
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
                let result = await qfetch('/quality-controls/get/'+speciality.qualityControl+'?fields=dueDate', {method: 'GET', headers: {}});
                this.showLoader = false;

                let date = this.convertTimestampToDate(result.item.dueDate);
                let queryParams = `?from=${date}&to=${date}&crafts[]=${speciality.craftId}&company=${this.company.id}&project=${this.project.id}&el_app_id=${report.id}#tab_qc_controls`;
                url += queryParams;

                window.open(url);
            } else {

                let range = [];
                this.showLoader = true;

                for(spec of report.specialities) {
                    if(spec.qualityControl) {
                        let url = '/quality-controls/get/'+spec.qualityControl+'?fields=dueDate';
                        let result = await qfetch(url, {method: 'GET', headers: {}});
                        range.push(+result.item.dueDate)
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
        getReportStatus(report) {
            if(report.partialProcess === "1") {
                return this.trans.partial_process;
            } else {
                return (report.appropriate === "1") ? this.trans.appropriate : this.trans.not_appropriate;
            }
        },
        getEarStatistics(projectId) {
            this.showLoader = true;

            let url = '/projects/statistics/ear';
            qfetch(url, {method: 'POST', headers: {}, body: this.filters})
                .then(response => {
                    this.reportsStatistics = response.item;
                    this.showLoader = false;
                })
        },
        getReportById() {
            this.showLoader = true;
            let url = `/projects/${this.project.id}/el-approvals/${this.searchValue}`;

            qfetch(url, {method: 'GET', headers: {}})
                .then((response) => {
                    if(response.item) {
                        this.goToReportDetails(response.item)
                    } else {
                        this.showWarningPopup = true;
                    }
                    this.showLoader = false;

                })
                .catch((error) => {
                    this.showWarningPopup = true;
                    this.showLoader = false;
                });
        }
    },
    mounted() {
        if(!this.filters) {
            window.history.replaceState({}, document.title, window.location.toString().split("?")[0]);
            this.$emit('tabChanged')
        } else {
            this.getFilteredReports();
            this.getEarStatistics();
        }
    }
});

