Vue.component('certificates-list', {
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
                {{ trans.certificates }} / 
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
                </ul>
            </div>
        </div>
        <div class="report-statistics">
            <div class="report-statistics-item"><span>{{ trans.total }}:</span><span class="q4b-text-bold"> {{ certificateStatistics?.total }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.approved }}:</span><span class="q4b-text-bold"> {{ certificateStatistics?.approved }}</span></div>
            <div class="report-statistics-item"><span>{{ trans.waiting }}:</span><span class="q4b-text-bold"> {{ certificateStatistics?.notApproved }}</span></div>
        </div>
        <div class="report-buttons">
            <div class="report-buttons-wraper" :class="{'open': toggleExportButton}" @click="toggleExportButton = !toggleExportButton">
                <span class="report-buttons-headline"><i class="q4bikon-share"></i>{{ trans.export }}</span>
                <a class="report-button pdf" style="opacity: .5;cursor: auto"><i class="q4bikon-file1"></i>PDF</a>
                <a class="report-button excel" style="opacity: .5;cursor: auto"><i class="q4bikon-report"></i>Excel</a>
            </div>
        </div>

        <div class="universal-certification">
            <div class="craft-cert" v-for="(craft, craftIndex) in crafts" :key="craft.id">
                <h2 class="craft-label">{{craft.name}}</h2><br>
                <div class="table craft-table">
                    <table v-if="craft?.certificates?.length > 0" id="cert-table">
                        <thead>
                            <th>
                                {{ trans.description }}
                            </th>
                            <th>
                                {{ trans.sample_required }}
                            </th>
                            <th>
                                {{ trans.update_date }}
                            </th>
                            <th>
                                {{ trans.approved_by }}
                            </th>
                            <th>
                                {{ trans.status }}
                            </th>
                        </thead>
                        <tbody>
                            <template v-for="(certificate, certificateIndex) in craft.certificates">
                                <tr class="parent-tr" :class="{'openParent': certificate.showChapters}" :key="certificate.id">
                                    <td class="parent-td" @click="toggleCertificateChapters(craftIndex, certificateIndex)" >
                                        <span>{{ certificate.name }}</span>
                                    </td>
                                    <td v-if="certificate.sampleRequired === '1'"><div  class="q4b-checked-icon"></div></td>
                                    <td v-else>&nbsp;</td>
                                    <td>{{ certificate.updatedAt ? convertTimestampToDate(certificate.updatedAt) : '' }}</td>
                                    <td>
                                        {{ certificate.approverName }}
                                    </td>
                                    <td>{{ trans[certificate.status] }}</td>
                                </tr>
                                <tr v-if="certificate.showChapters">
                                    <td colspan="5">
                                        <div class="report_certificate_participants_wrapper">
                                            <div class="report_certificate_participants_title">
                                                <span>{{ trans.participants_list }}</span>
                                            </div>
                                            <div class="report_certificate_participants">
                                                <div 
                                                    class="report_certificate_participant"
                                                    v-for="(participant, participantIndex) in certificate.participants"
                                                >
                                                    <div class="report_certificate_participant_name">{{ participant.name }}</div>
                                                    <div class="report_certificate_participant_position">{{ participant.position }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="report_certificate_chapters_wrapper">
                                            <div class="report_certificate_chapters_title">
                                                <span>{{ trans.chapters_list }}</span>
                                            </div>
                                            <div class="report_certificate_chapters">
                                                <div class="report_certificate_chapter" v-for="(chapter, chapterIndex) in certificate.chapters">
                                                    <div class="report_certificate_chapter_name">{{ chapter.name }}</div>
                                                    <div class="q4b-textarea">
                                                        <div class="q4b-input-label">{{ trans.chapter_content }}</div>
                                                        <textarea cols="30" rows="10" readonly>{{ chapter.text }}</textarea>
                                                    </div>
                                                    <div class="report_certificate_chapter_images">
                                                        <div class="row">
                                                            <template v-for="(image, imageIndex) in chapter.images">
                                                                <div class="col-md-6 rtl-float-right">
                                                                    <div class="report_certificate_chapter_image">
                                                                        <h4 class="report_certificate_chapter_image_title">
                                                                            {{ image.originalName }}
                                                                            <span class="report_certificate_chapter_image_title_uploaded">
                                                                            ({{ trans.uploaded }}: {{ convertTimestampToDate(image.createdAt) }} )
                                                                            </span>
                                                                        </h4>
                                                                        <div class="report_certificate_chapter_image_image">
                                                                            <a :href="image.fullPath" target="_blank">
                                                                                <img :src="image.fullPath" :alt="image.originalName">
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!--        <pagination -->
<!--            v-model="currentPage" -->
<!--            :records="total" -->
<!--            :per-page="limit" -->
<!--            @paginate="paginate" -->
<!--            :options="{chunk:5,'chunksNavigation':'fixed'}"-->
<!--        >          -->
<!--        </pagination>-->
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
            crafts: [],
            toggleExportButton: false,
            trans: JSON.parse(this.translations),
            currentPage: this.page,
            total: 0,
            limit: 0,
            statistics: null,
            projectId: this.project.id,
            certificateStatistics: null
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
    },
    methods: {
        getFilteredReportsAPI() {
            this.showLoader = true;
            let url = `/projects/certificates`;

            if(this.currentPage > 1) url += '/page/' + this.currentPage;

            qfetch(url, {method: 'POST', headers: {}, body: this.filters})
                .then((response) => {
                    this.crafts.map(craft => {
                        craft.certificates = response.items.filter(certificate => {
                            return +certificate.craftId === +craft.id
                        })
                        return craft;
                    })
                    // this.certificates = response.items;
                    // this.total = response.pagination.total ? parseInt(response.pagination.total) : 0;
                    // this.limit = response.pagination.limit ? parseInt(response.pagination.limit) : 0;
                    this.showLoader = false;
                    console.log('this.crafts', this.crafts)
                    this.getCertificatesStatisticsAPI()

                });
        },
        getCraftsAPI() {
            this.showLoader = true;
            let url = `/companies/entities/crafts_by_ids`;

            qfetch(url, {method: 'POST', headers: {}, body: {specialityIds: this.filters.specialityIds}})
                .then((response) => {
                    this.showLoader = false;
                    this.crafts = response.items;
                    this.getFilteredReportsAPI();
                });
        },
        getCertificatesStatisticsAPI() {
            this.showLoader = true;
            let url = '/projects/statistics/certificates';

            let specialitiesIds = this.filters.specialityIds.map(craftId => 'specialitiesIds[]=' +craftId)
            let statuses = this.filters.statuses.map(statusName => 'statuses[]=' +statusName)

            let params = `?projectIds[]=${this.filters.projectId}&${specialitiesIds.join('&')}&sampleRequired=${this.filters.sampleRequired ? '1' : '0'}&${statuses.join('&')}`;

            qfetch(url + params, {method: 'GET', headers: {}})
                .then((response) => {
                    this.certificateStatistics = response.item;
                    this.showLoader = false;
                    console.log('this.certificateStatistics', this.certificateStatistics)
                });
        },
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1) > 9) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        // paginate() {
        //     this.getFilteredReportsAPI();
        // },
        goToReportDetails(report) {
            this.$emit('toReportDetails', {
                report: report,
                page: this.currentPage
            })
        },
        toggleCertificateChapters(craftIndex, certificateIndex) {
            let crafts = [].concat(this.crafts);
            crafts[craftIndex].certificates[certificateIndex].showChapters = !crafts[craftIndex].certificates[certificateIndex].showChapters;
            this.crafts = crafts;
        },
    },
    created() {
        if(!this.filters) {
            // window.history.replaceState({}, document.title, window.location.toString().split("?")[0]);
            this.$emit('tabChanged')
        } else {
            this.getCraftsAPI()
        }
    },
    mounted() {

    }
});

