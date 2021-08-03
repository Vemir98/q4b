Vue.component('reports-list', {
    template: `
    <section class='q4b-approve-el app-element-list new-styles'>
        <div class="page-title-sec flex-start">
            <div class="page-title">
                <a class="back-to-filter">
                    <i 
                        class="q4bikon-arrow_back2"
                        @click="$emit('tabChanged')"
                    ></i>
                </a>
                {{ trans.approve_element }} / 
                <span class="project_name"> ITAMAR 4</span>
            </div>
        </div>
        <div class="report-project-desc_wraper flex-start">
            <div class="report-project-desc-image">
                <img src="./img/project-img.jpg" alt="project images">
            </div>
            <div class="report-project-desc-list flex-start">
                <ul class="flex-start">
                    <li>
                        <span class="dark-blue">
                            {{ trans.company_name }}
                        </span>
                        <span class="light-blue">
                            {{ item.company_name }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_name }}
                        </span>
                        <span class="light-blue">
                            {{ item.project_name }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.owner }}
                        </span>
                        <span class="light-blue">
                            {{ item.owner }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.start_date }}
                        </span>
                        <span class="light-blue">
                            {{ item.start_date }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.end_date }}
                        </span>
                        <span class="light-blue">
                            {{ item.end_date }}
                        </span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_id }}
                        </span>
                        <span class="light-blue">
                            {{ item.project_id }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.project_status }}
                        </span>
                        <span class="light-blue">
                            {{ item.project_status }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.address }}
                        </span>
                        <span class="light-blue">
                            {{ item.address }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue">
                            {{ trans.structures_quantity }}
                        </span>
                        <span class="light-blue">
                            {{ item.structures_quantity }}
                        </span>
                    </li>
                    <li>
                        <span class="dark-blue ">
                            {{ trans.report_range }}
                        </span>
                        <span class="light-blue ">
                            <span>{{ item.report_range }}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="report-buttons">
            <div class="report-buttons-wraper" :class="{'open': toggleExportButton}" @click="toggleExportButton = !toggleExportButton">
                <span class="report-buttons-headline"><i class="q4bikon-share"></i>{{ trans.export }}</span>
                <a class="report-button pdf " href=""><i class="q4bikon-file1"></i>PDF</a>
                <a  class="report-button excel" href=""><i class="q4bikon-report"></i>Excel</a>
            </div>
        </div>

        <div class="report-list-wraper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Check N[*]</th>
                        <th>Check Date[*]</th>
                        <th>{{ trans.element }}</th>
                        <th>{{ trans.craft }}</th>
                        <th>{{ trans.floor }}</th>
                        <th>{{ trans.status }}</th>
                        <th>Approval Date[*]</th>
                        <th>{{ trans.position }}</th>
                        <th>Signer Name[*]</th>
                        <th>{{ trans.signature }}</th>
                        <th>{{ trans.more }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="report in item.reports">
                        <tr class="parent-tr" :class="{'openParent': report.show_specialities}">
                            <td scope="row" @click="toggleReportSpecialities(report)" class="parent-td">{{ report.id }}</td>
                            <td>{{ report.check_date }} </td>
                            <td>{{ report.element_name }}</td>
                            <td>&nbsp;</td>
                            <td class="td-floor">{{ report.floor }}</td>
                            <td> {{ report.status }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <button class="open-more" @click="toggleReportOptions(report)"><img src="./img/more-icon.svg" alt="">
                                    <div  class="td-options-wrap" v-if="report.show_options">
                                        <a @click="$emit('toReportDetails', report.id)"><i class="q4bikon-preview1"></i>View[*]</a>
                                        <a href=""><i class="q4bikon-uncheked"></i>{{ trans.qc_report }}</a>
                                    </div>
                                </button>
                            </td>
                        </tr>
                        <template v-for="speciality in report.specialities">
                            <tr class="child-tr" v-if="report.show_specialities">
                                <td scope="row">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>{{ speciality.name }}</td>
                                <td>&nbsp;</td>
                                <td> {{ speciality.status }}</td>
                                <td>{{ speciality.approval_date }}</td>
                                <td>{{ speciality.position }}</td>
                                <td>{{ speciality.signer_name }}</td>
                                <td class="td-sign"><img :src="speciality.signature"></td>
                                <td>&nbsp;</td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
    `,
    props: {
        data: {required: true},
        translations: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            item: this.data,
            toggleExportButton: false,
            trans: JSON.parse(this.translations),
        }
    },

    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
    },
    methods: {
        toggleReportSpecialities(report) {
            report.show_specialities = !report.show_specialities

        },
        toggleReportOptions(report) {
            report.show_options = !report.show_options
        }
    }
});

