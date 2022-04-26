Vue.component('statistics', {
    template: `
        <div id="dashboard-content" class="new-styles">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="page-title-sec flex-start">
                <div class="page-title"> {{ trans.dashboard_new }} </div>
            </div>
            <div class="dashboard-statistics">
                <div class="dashboard-filters">
                    <div class="main-filters">
                        <div class="filter-item">
                            <div class="multiselect-col">
                                <div class="filter-item-label flex-between">
                                {{ trans.companies }}
                                <label class="filter-label" :class="{'labtest-disabled': false}">
                                    <span @click="toggleSelectAll('selectedCompanies', 'companies')">
                                       <template v-if="selectedCompanies.length < companies.length">
                                               {{ trans.select_all }}
                                        </template>
                                        <template v-else>
                                               {{ trans.unselect_all }}
                                        </template>
                                    </span>
                                </label>
                                </div>
                                    <multiselect 
                                        v-model="selectedCompanies"  
                                        :placeholder="trans.companies" 
                                        :disabled="companies.length < 1" 
                                        :options="companies" 
                                        label="name" 
                                        track-by="id"
                                        :multiple="true" 
                                        :hide-selected="false"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        :internal-search="true"
                                        :taggable="false"
                                        :show-labels="false"
                                        @select="onSelect($event, 'companies')"
                                        @remove="onRemove($event, 'companies')"  
                                    >
                                        <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                            <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                            <span class="multiselect-option-name">{{ scope.option.name }}</span>
                                        </span>
                                        <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values) }} </span></template>
                                        <template slot="tag">{{ '' }}</template>
                                    </multiselect> 
                            </div>
                        </div>
                        <div class="filter-item">
                            <div class="multiselect-col">
                                <div class="filter-item-label flex-between">
                                {{ trans.projects }}
                                <label class="filter-label" :class="{'labtest-disabled': false}">
                                    <span @click="toggleSelectAll('selectedProjects', 'projects')">
                                       <template v-if="selectedProjects.length < projects.length">
                                               {{ trans.select_all }}
                                        </template>
                                        <template v-else>
                                               {{ trans.unselect_all }}
                                        </template>
                                    </span>
                                </label>
                                </div>
                                    <multiselect 
                                        v-model="selectedProjects"  
                                        :placeholder="trans.projects" 
                                        :disabled="projects.length < 1" 
                                        :options="projects" 
                                        label="name" 
                                        track-by="id"
                                        :multiple="true" 
                                        :hide-selected="false"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        :internal-search="true"
                                        :taggable="false"
                                        :show-labels="false"  
                                        @select="onSelect($event, 'projects')"
                                        @remove="onRemove($event, 'projects')" 
                                    >
                                        <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                            <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                            <span class="multiselect-option-name">{{ scope.option.name }}</span>
                                        </span>
                                        <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values) }} </span></template>
                                        <template slot="tag">{{ '' }}</template>
                                    </multiselect> 
                            </div>
                        </div>
                        <div class="filter-buttons">
                            <button 
                                class="filter-button"
                                :class="{'labtest-disabled': (!canGenerateStatistics)}"
                                @click="generateStatistics"
                            >
                                {{ trans.show }}
                            </button>
                        </div>
                    </div>
                    <div class="period-filter">
                        <div class="filter-item">
                            <div class="multiselect-col">
                                <div class="filter-item-label" >{{ trans.date }}</div>
                                <multiselect 
                                    v-model="selectedRange"
                                    :option-height="104" 
                                    :placeholder="trans.date" 
                                    :disabled="ranges.length < 1" 
                                    :options="ranges" 
                                    track-by="id" 
                                    label="name" 
                                    :searchable="true" 
                                    :allow-empty="false" 
                                    :show-labels="false"
                                >
                                    <template slot="singleLabel" slot-scope="props">{{ trans[props.option.name] }}</template>
                                    <template slot="option" slot-scope="props">
                                        <span>{{ trans[props.option.name] }}</span>
                                    </template>
                                    <template slot="option-selected" slot-scope="props">
                                        <span>{{ trans[props.option.name] }}</span>
                                    </template>
                                </multiselect>
                            </div>
                        </div>
                        <div
                            class="labtest_filters_export"
                            @click="printPdf"
                        >
                            <a>{{ trans.export }}</a>
                        </div>
                    </div>
                </div>
                <div class="dashboard-filter-results">
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.qc_report }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="qc-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.qc.data" class="dashboard-statistics-item-data">
                                <table>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.qc.data.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.invalid_qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.qc.data.invalid }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.repaired_qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="green bold">{{ statistics.qc.data.repaired }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon purple"></div>
                                                <div class="statistics-data-descr">{{ trans.existing_and_for_repair_qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="purple bold">{{ statistics.qc.data.existingAndForRepair }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon blue"></div>
                                                <div class="statistics-data-descr">{{ trans.other_qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span>{{ statistics.qc.data.others }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports#tab_qc_controls'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.place_report }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="place-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.place.data" class="dashboard-statistics-item-data"> 
                                <table>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                        <th>{{ trans.with_qc }}</th>
                                        <th>{{ trans.no_qc }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.places_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.place.data.total.total }}</span></td>
                                        <td><span class="green bold">{{ statistics.place.data.total.withQc }}</span></td>
                                        <td><span class="gray bold">{{ statistics.place.data.total.withoutQc }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.private_places }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.place.data.private.total }}</span></td>
                                        <td><span class="green bold">{{ statistics.place.data.private.withQc }}</span></td>
                                        <td><span class="gray bold">{{ statistics.place.data.private.withoutQc }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.public_places }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.place.data.public.total }}</span></td>
                                        <td><span class="green bold">{{ statistics.place.data.public.withQc }}</span></td>
                                        <td><span class="gray bold">{{ statistics.place.data.public.withoutQc }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports/place'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.certificates }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="certificates-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.certificates.data" class="dashboard-statistics-item-data">
                                <table>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.certificates_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.certificates.data.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.not_approved_certificates }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.certificates.data.notApproved }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.approved_certificates }}</div>
                                            </div>
                                        </td>
                                        <td><span class="green bold">{{ statistics.certificates.data.approved }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports/certificates'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.delivery_report }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="delivery-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.delivery.data" class="dashboard-statistics-item-data">
                                <table>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                        <th>{{ trans.public }}</th>
                                        <th>{{ trans.private }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon dark"></div>
                                                <div class="statistics-data-descr">{{ trans.places_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="dark bold">{{ statistics.delivery.data.total.total }}</span></td>
                                        <td><span class="dark bold">{{ statistics.delivery.data.public.total }}</span></td>
                                        <td><span class="dark bold">{{ statistics.delivery.data.private.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.deliveries_done }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.delivery.data.total.delivery }}</span></td>
                                        <td><span class="black bold">{{ statistics.delivery.data.public.delivery }}</span></td>
                                        <td><span class="black bold">{{ statistics.delivery.data.private.delivery }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.pre_deliveries_done }}</div>
                                            </div>
                                        </td>
                                        <td><span class="green bold">{{ statistics.delivery.data.total.preDelivery }}</span></td>
                                        <td><span class="green bold">{{ statistics.delivery.data.public.preDelivery }}</span></td>
                                        <td><span class="green bold">{{ statistics.delivery.data.private.preDelivery }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports/delivery'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.ears }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="ear-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.ear.data" class="dashboard-statistics-item-data">
                                <table> 
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                        <th>{{ trans.waiting }}</th>
                                        <th>{{ trans.approved }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.ears_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.ear.data.total.total }}</span></td>
                                        <td><span class="black bold">{{ statistics.ear.data.total.waiting }}</span></td>
                                        <td><span class="black bold">{{ statistics.ear.data.total.approved }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.not_appropriate_ears }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.ear.data.notAppropriate.total }}</span></td>
                                        <td><span class="red bold">{{ statistics.ear.data.notAppropriate.waiting }}</span></td>
                                        <td><span class="red bold">-</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.appropriate_ears }}</div>
                                            </div> 
                                        </td>
                                        <td><span class="green bold">{{ statistics.ear.data.appropriate.total }}</span></td>
                                        <td><span class="green bold">{{ statistics.ear.data.appropriate.waiting }}</span></td>
                                        <td><span class="green bold">{{ statistics.ear.data.appropriate.approved }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon orange"></div>
                                                <div class="statistics-data-descr">{{ trans.partial_process }}</div>
                                            </div> 
                                        </td>
                                        <td><span class="orange bold">{{ statistics.ear.data.partialProcess.total }}</span></td>
                                        <td><span class="orange bold">-</span></td>
                                        <td><span class="orange bold">{{ statistics.ear.data.partialProcess.approved }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports/approve_element'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                    <div v-show="showStatistics" class="dashboard-statistics-item">
                        <div class="dashboard-statistics-item-title">
                            <span>{{ trans.lab_control_reports }}</span>
                        </div>
                        <div class="dashboard-statistics-item-range">
                            <span>{{ trans.analytics_for }} {{ trans[statisticsPeriodName] }}</span>
                        </div>
                        <div class="dashboard-statistics-item-content">
                            <div class="dashboard-statistics-item-chart">
                                <canvas class="dashboard-chart" ref="labTests-chart" width="200" height="200"></canvas>
                            </div>
                            <div v-if="statistics.labTests.data" class="dashboard-statistics-item-data">
                                <table>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>{{ trans.total }}</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.lab_controls_sent }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.labTests.data.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.approved_lab_controls }}</div>
                                            </div>
                                        </td>
                                        <td><span class="green bold">{{ statistics.labTests.data.approved }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.not_approved_lab_controls }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.labTests.data.notApproved }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links" v-if="!exportPdf"><a :href="siteUrl + '/reports/labtests'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        siteUrl: {required: true},
        userPreferencesTypes: {required: true},
        userId: {required: true},
        isMobile: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        canGenerateStatistics() {
            return (this.selectedProjects.length && this.selectedCompanies.length && this.selectedRange)
        },
        getExportPdfUrl() {
            let url = `${this.siteUrl}/dashboard/export_pdf`;
            url += this.getQueryParamsOfFiltersForUrl();
            url += `&lang=${this.currentLang}`
            return url;
        },
        getSelectedCompaniesNames() {
            return this.selectedCompanies.map(company => company.name).join(', ')
        },
        getSelectedProjectsNames() {
            return this.selectedProjects.map(project => project.name).join(', ')
        }
    },
    data() {
        return {
            showLoader: false,
            exportPdf: false,
            trans: JSON.parse(this.translations),
            companies: [],
            projects: [],
            ranges: [
                {id: 1, name: 'today'},
                {id: 2, name: 'yesterday'},
                {id: 3, name: '7_days'},
                {id: 4, name: 'monthly'},
                {id: 5, name: 'quarterly'},
                {id: 6, name: 'half_year'},
                {id: 7, name: 'one_year'},
                {id: 8, name: 'two_years'},
                {id: 9, name: 'three_years'},
                {id: 10, name: 'four_years'},
            ],
            selectedCompanies: [],
            selectedProjects: [],
            selectedRange: null,
            statisticsPeriodName: "",
            langs: {
                ru: {
                    formatLocale: {
                        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                        monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                        weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                        weekdaysShort: ['Вoс', 'Пон', 'Вто', 'Сре', 'Чет', 'Пят', 'Суб'],
                        weekdaysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                },
                he: {
                    formatLocale: {
                        months: ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'],
                        monthsShort: ['ינו', 'פבר', 'מרץ', 'אפר', 'מאי', 'יוני', 'יולי', 'אוג', 'ספט', 'אוק', 'נוב', 'דצמ'],
                        weekdays: ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'],
                        weekdaysShort: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
                        weekdaysMin: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                }
            },
            showStatistics: false,
            statistics: {
              qc: {
               data: null,
               chart: null,
              },
              place: {
                data: null,
                chart: null,
              },
              ear: {
                data: null,
                chart: null,
              },
              delivery: {
                data: null,
                chart: null,
              },
              labTests: {
                data: null,
                chart: null,
              },
              certificates: {
                data: null,
                chart: null,
              },
            },
            companiesToIgnore: [13,15,31,66],
            // companiesToIgnore: [],
            userPrefTypes: JSON.parse(this.userPreferencesTypes),
            dashboardFilters: null
        }
    },
    watch: {
        companies(companies) {
            this.projects = [];
            this.selectedProjects = [];

            if(this.dashboardFilters) {
                this.dashboardFilters.companies = this.dashboardFilters.companies.map(companyId => +companyId);
                this.dashboardFilters.projects = this.dashboardFilters.projects.map(projectId => +projectId);

                this.selectedRange = this.ranges.filter(range => {
                    return +range.id === +this.dashboardFilters.range[0]
                })[0]

                this.selectedCompanies = this.companies.filter(company => {
                    company.checked = this.dashboardFilters.companies.includes(+company.id)
                    if(company.checked && company.projects) {
                        this.projects = this.projects.concat(Object.values(company.projects))
                    }
                    return company.checked
                }, this)

                this.selectedProjects = this.projects.filter(project => {
                    project.checked = this.dashboardFilters.projects.includes(+project.id)
                    return project.checked
                }, this)

            } else {
                this.selectedCompanies = companies;

                this.selectedCompanies.map(company => {
                    company.checked = true;
                    return company
                })

                this.selectedRange = this.ranges.filter(range => {
                    return range.id === 4
                })[0]

                companies.forEach(company => {
                    if(company.projects) {
                        this.projects = this.projects.concat(Object.values(company.projects))
                        this.toggleSelectAll('selectedProjects', 'projects');
                    }
                }, this)
            }
            this.generateStatistics();
        },
        selectedCompanies(companies) {
            this.projects = [];
            this.selectedProjects = [];

            if(this.dashboardFilters) {
                companies.forEach(company => {
                    company.checked = this.dashboardFilters.companies.includes(+company.id)
                    if(company.checked && company.projects) {
                        this.projects = this.projects.concat(Object.values(company.projects))
                    }
                    return company.checked
                }, this)

                this.selectedProjects = this.projects.filter(project => {
                    project.checked = this.dashboardFilters.projects.includes(+project.id)
                    return project.checked
                }, this)

            } else {
                companies.forEach(company => {
                    if(company.projects) {
                        this.projects = this.projects.concat(Object.values(company.projects))
                        this.toggleSelectAll('selectedProjects', 'projects');
                    }
                }, this)
            }

            this.dashboardFilters = null;
        }
    },
    methods: {
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        timeChanged() {},
        onSelect(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = false;
        },
        toggleSelectAll(selected, list) {
            if (list.length) {
                if (this[selected].length < this[list].length) {
                    this[selected] = this[list].map((i) => {
                        i.checked = true;
                        return i
                    });
                } else {
                    this[selected] = this[list].map((i) => {
                        i.checked = false;
                        return i
                    });
                    this[selected] = []
                }
            }
        },
        async getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            const dashboardFiltersUrl = `/user/${this.userId}/preferences/get/${this.userPrefTypes.Dashboard}`;
            const dashboardFilters = await qfetch(dashboardFiltersUrl, {method: 'GET', headers: {}})


            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    response.items = response.items.filter(company => {
                        return !this.companiesToIgnore.includes(+company.id)
                    }, this)

                    if(dashboardFilters.item) {
                        this.dashboardFilters = dashboardFilters.item;
                    }

                    this.companies = response.items ? response.items : [];
                    this.showLoader = false;
                })
        },
        getPeriodFromRange(range) {

            let result = {
                from: new Date(),
                to: new Date(),
            }

            switch (range) {
                case 'today':
                break;
                case 'yesterday':
                    result.from.setDate(result.from.getDate() - 1)
                break;
                case '7_days':
                    result.from.setDate(result.from.getDate() - 7)
                break;
                case 'monthly':
                    result.from.setMonth(result.from.getMonth() - 1)
                break;
                case 'quarterly':
                    result.from.setMonth(result.from.getMonth() - 3)
                break;
                case 'half_year':
                    result.from.setMonth(result.from.getMonth() - 6)
                break;
                case 'one_year':
                    result.from.setFullYear(result.from.getFullYear()-1);
                break;
                case 'two_years':
                    result.from.setFullYear(result.from.getFullYear()-2);
                break;
                case 'three_years':
                    result.from.setFullYear(result.from.getFullYear()-3);
                    break;
                case 'four_years':
                    result.from.setFullYear(result.from.getFullYear()-4);
                    break;
            }

            return result;
        },
        generateStatistics() {
            this.showStatistics = true;
            this.resetStatistics();

            let projectIds = this.selectedProjects.map(project => 'projectIds[]=' +project.id)
            let period = this.getPeriodFromRange(this.selectedRange.name);
            let from = (Math.round(period.from.getTime()/1000));
            let to = (Math.round(period.to.getTime()/1000));


            let params = `?${projectIds.join('&')}&from=${from}&to=${to}`;
            if(!this.dashboardFilters && this.canGenerateStatistics) {
                this.setUserPreferencesAPI(this.userPrefTypes.Dashboard)
            }

            if(this.canGenerateStatistics) {
                this.getQcStatistics(params);
                this.getPlaceStatistics(params);
                this.getCertificatesStatistics(params);
                this.getDeliveryStatistics(params);
                this.getEarStatistics(params);
                this.getLabControlStatistics(params);
            }

            this.statisticsPeriodName = this.selectedRange.name;
        },
        getQcStatistics(params) {
            this.showLoader = true;
            let url = '/projects/statistics/qc';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.qc.data = response.item;
                    this.showLoader = false;
                    this.statistics.qc.chart = this.createChart(
                        this.$refs['qc-chart'],
                        'pie',
                        [
                            {
                                data: this.statistics.qc.data.invalid,
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                            },
                            {
                                data: this.statistics.qc.data.repaired,
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                                borderColor: 'rgba(15, 154, 96, 1)',
                            },
                            {
                                data: this.statistics.qc.data.existingAndForRepair,
                                backgroundColor: 'rgba(213, 21, 211, 1)',
                                borderColor: 'rgba(213, 21, 211, 1)',
                            },
                            {
                                data: this.statistics.qc.data.others,
                                backgroundColor: 'rgba(157, 156, 201, 1)',
                                borderColor: 'rgba(157, 156, 201, 1)',
                            }
                        ],
                        [this.trans.invalid, this.trans.repaired, this.trans.existing_and_for_repair_qc_in_system, this.trans.other],
                        this.statistics.qc.data.total
                    );
                })
        },
        getPlaceStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/place';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.place.data = response.item;
                    this.showLoader = false;
                    this.statistics.place.chart = this.createChart(
                        this.$refs['place-chart'],
                        'bar',
                        [
                            {
                                label: this.trans.no_qc,
                                data: [
                                    this.statistics.place.data.private.withoutQc,
                                    this.statistics.place.data.public.withoutQc,
                                ],
                                backgroundColor: 'rgba(0,92,135, 1)',
                            },
                            {
                                label: this.trans.with_qc,
                                data: [
                                    this.statistics.place.data.private.withQc,
                                    this.statistics.place.data.public.withQc,
                                ],
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                            },
                        ],
                        [this.trans.private, this.trans.public],
                        {
                            private: this.statistics.place.data.private.total,
                            public: this.statistics.place.data.public.total
                        }
                    );
                })
        },
        getEarStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/ear';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.ear.data = response.item;
                    this.showLoader = false;
                    this.statistics.ear.chart = this.createChart(
                        this.$refs['ear-chart'],
                        'pie',
                        [
                            {
                                data: this.statistics.ear.data.notAppropriate.total,
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                            },
                            {
                                data: this.statistics.ear.data.appropriate.total,
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                                borderColor: 'rgba(15, 154, 96, 1)',
                            },
                            {
                                data: this.statistics.ear.data.partialProcess.total,
                                backgroundColor: 'rgba(255, 144, 0, 1)',
                                borderColor: 'rgba(255 ,144 , 0, 1)',
                            },
                        ],
                        [this.trans.not_appropriate, this.trans.appropriate, this.trans.partial_process],
                        this.statistics.ear.data.total.total
                    );
                })
        },
        getDeliveryStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/delivery';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.delivery.data = response.item;
                    this.showLoader = false;
                    this.statistics.delivery.chart = this.createChart(
                        this.$refs['delivery-chart'],
                        'bar',

                        [
                            {
                                label: this.trans.deliveries_done,
                                data: [
                                    this.statistics.delivery.data.private.delivery,
                                    this.statistics.delivery.data.public.delivery,
                                ],
                                backgroundColor: 'rgba(0,92,135, 1)',
                            },
                            {
                                label: this.trans.pre_deliveries_done,
                                data: [
                                    this.statistics.delivery.data.private.preDelivery,
                                    this.statistics.delivery.data.public.preDelivery,
                                ],
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                            },
                        ],
                        [this.trans.private, this.trans.public],
                        {
                            private: this.statistics.delivery.data.private.total,
                            public: this.statistics.delivery.data.public.total
                        }
                    );
                })
        },
        getLabControlStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/labtests';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.labTests.data = response.item;
                    this.showLoader = false;
                    this.statistics.labTests.chart = this.createChart(
                        this.$refs['labTests-chart'],
                        'pie',
                        [
                            {
                                data: this.statistics.labTests.data.notApproved,
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                            },
                            {
                                data: this.statistics.labTests.data.approved,
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                                borderColor: 'rgba(15, 154, 96, 1)',
                            }
                        ],
                        [this.trans.not_approved, this.trans.approved],
                        this.statistics.labTests.data.total
                    );
                })
        },
        getCertificatesStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/certificates';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    this.statistics.certificates.data = response.item;
                    this.showLoader = false;
                    this.statistics.certificates.chart = this.createChart(
                        this.$refs['certificates-chart'],
                        'bar',
                        [
                            {
                                label: this.trans.approved,
                                data: [this.statistics.certificates.data.approved],
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                            },
                            {
                                label: this.trans.not_approved,
                                data: [this.statistics.certificates.data.notApproved],
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                            }
                        ],
                        [this.trans.certificates],
                        this.statistics.certificates.data.total
                );
                })
        },
        resetStatistics() {

            if(this.statistics.qc.chart) {
                this.statistics.qc.chart.destroy();
                this.statistics.qc.chart = null;
            }
            if(this.statistics.place.chart) {
                this.statistics.place.chart.destroy();
                this.statistics.place.chart = null;
            }
            if(this.statistics.ear.chart) {
                this.statistics.ear.chart.destroy();
                this.statistics.ear.chart = null;
            }
            if(this.statistics.delivery.chart) {
                this.statistics.delivery.chart.destroy();
                this.statistics.delivery.chart = null;
            }
            if(this.statistics.labTests.chart) {
                this.statistics.labTests.chart.destroy();
                this.statistics.labTests.chart = null;
            }
            if(this.statistics.certificates.chart) {
                this.statistics.certificates.chart.destroy();
                this.statistics.certificates.chart = null;
            }

            this.statistics.qc.data = null;
            this.statistics.place.data = null;
            this.statistics.ear.data = null;
            this.statistics.delivery.data = null;
            this.statistics.labTests.data = null;
            this.statistics.certificates.data = null;
            this.statisticsPeriodName = "";
        },
        createChart(ref, type, data, labels, total) {

            switch (type) {
                case 'bar':
                    return new Chart(ref, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: data.map(item => {
                                    return {
                                        label: item.label,
                                        data: item.data,
                                        backgroundColor: item.backgroundColor,
                                    }
                            })
                        },
                        options: {
                            tooltips: {
                                displayColors: true,
                                callbacks:{
                                    mode: 'x',
                                    label: (tooltipItem, context) => {
                                        if (typeof total === 'object' && !Array.isArray(total) && total !== null) {
                                            switch (context.labels[tooltipItem.index]) {
                                                case this.trans.private:
                                                    return context.datasets[tooltipItem.datasetIndex].label + ": " + this.getPercent(context.datasets[tooltipItem.datasetIndex].data[tooltipItem.index], total.private);
                                                case this.trans.public:
                                                    return context.datasets[tooltipItem.datasetIndex].label + ": " + this.getPercent(context.datasets[tooltipItem.datasetIndex].data[tooltipItem.index], total.public);
                                            }
                                        } else {
                                            return context.datasets[tooltipItem.datasetIndex].label + ": " + this.getPercent(context.datasets[tooltipItem.datasetIndex].data[tooltipItem.index], total);
                                        }
                                    },
                                },
                            },
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    gridLines: {
                                        display: false,
                                    }
                                }],
                                yAxes: [{
                                    stacked: true,
                                    ticks: {
                                        beginAtZero: true,
                                    },
                                    type: 'linear',
                                }]
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: { display: false },
                        },
                    })
                case 'pie':
                    return new Chart(ref, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '# of Votes',
                                data: (data.filter(item => +item.data > 0).length > 0) ? data.map(item => item.data) : [1],
                                backgroundColor: (data.filter(item => +item.data > 0).length > 0) ? data.map(item => item.backgroundColor) : ['rgba(237,237,241,1)'],
                                borderColor: (data.filter(item => +item.data > 0).length > 0) ? data.map(item => item.borderColor) : ['rgba(237,237,241,1)'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            tooltips: {
                                callbacks: {
                                    mode: 'label',
                                    label: (tooltipItem, context) => {
                                        if((data.filter(item => +item.data > 0).length === 0)) {
                                            return this.trans.no_data;
                                        }
                                        return context.labels[tooltipItem.index] + ": " + this.getPercent(context.datasets[tooltipItem.datasetIndex].data[tooltipItem.index], total);
                                    },
                                }
                            },
                            responsive: true,
                        }
                    })
            }
        },
        getPercent(current, total) {
            return ((+current * 100) / +total).toFixed(2) + "%";
        },
        getQueryParamsOfFiltersForUrl() {
            let projectIds = encodeURIComponent(JSON.stringify(this.selectedProjects.map(project => +project.id)));
            let range = encodeURIComponent(JSON.stringify(this.selectedRange?.name));

            return `?projectIds=${projectIds}&range=${range}`;
        },
        setUserPreferencesAPI(type) {
            let url = `/user/${this.userId}/preferences/set/${type}`;

            const data = {
                preferences: {
                    companies: this.selectedCompanies.map(company => +company.id),
                    projects: this.selectedProjects.map(project => +project.id),
                    range: this.selectedRange.id
                }
            }

            qfetch(url, {method: 'POST', headers: {},  body: data})
                .then(response => {
                })
        },
        printPdf() {
            // if pdf container already exists removing it
            let oldPdfContainer = document.getElementsByClassName('dashboard-print');
            if(oldPdfContainer.length) {
                Array.from(oldPdfContainer).forEach(node => {
                    document.body.removeChild(node)
                })
            }

            let statistics = document.getElementsByClassName('dashboard-filter-results')[0];

            let elements = statistics.children;
            let elementsArray = Array.from(elements);

            let pdfContainer = document.createElement('div')
            pdfContainer.classList.add('dashboard-print')
            pdfContainer.classList.add('new-styles')
            pdfContainer.style.display = 'none';

            let filtersContainer = document.createElement('div')
            filtersContainer.classList.add('dashboard-print-container')

            let filters = document.createElement('div');
            filtersContainer.classList.add('dashboard-filters-pdf')

            filters.innerHTML = `
                <div>
                    <span class="filter-title-pdf">${this.trans.report_range}:</span><span>${this.trans[this.selectedRange?.name]}</span>
                </div>
                <br>
                <div>
                    <span class="filter-title-pdf">${this.trans.companies}:</span><span>${this.getSelectedCompaniesNames}</span>
                </div>
                <br>
                <div>
                    <span class="filter-title-pdf">${this.trans.projects}:</span><span>${this.getSelectedProjectsNames}</span>
                </div>`;

            filtersContainer.appendChild(filters);
            pdfContainer.appendChild(filtersContainer)

            let statisticsContainer = document.createElement('div')
            statisticsContainer.classList.add('dashboard-print-container')

            for(let i = 0; i < elementsArray.length; i++) {
                if((i % 2 === 0) && i !== 0) {
                    pdfContainer.appendChild(statisticsContainer)
                    statisticsContainer = document.createElement('div')
                    statisticsContainer.classList.add('dashboard-print-container')
                }
                let newElement = elementsArray[i].cloneNode(true);
                let oldCanvas = elementsArray[i].getElementsByTagName('canvas')[0];
                let canvasToReplace = newElement.getElementsByTagName('canvas')[0];

                let newCanvas = this.cloneCanvas(oldCanvas)
                document.body.appendChild(newCanvas)
                canvasToReplace.parentNode.replaceChild(newCanvas, canvasToReplace)

                statisticsContainer.appendChild(newElement)

                if(i === (elementsArray.length - 1)) {
                    pdfContainer.appendChild(statisticsContainer)
                }
            }

            document.body.appendChild(pdfContainer);
            window.print();
        },
        cloneCanvas(oldCanvas) {
            //create a new canvas
            let newCanvas = document.createElement('canvas');
            let context = newCanvas.getContext('2d');

            //set dimensions
            newCanvas.width = oldCanvas.width;
            newCanvas.height = oldCanvas.height;
            newCanvas.style.width = oldCanvas.style.width;
            newCanvas.style.height = oldCanvas.style.height;

            //apply the old canvas to the new one
            context.drawImage(oldCanvas, 0, 0);

            //return the new canvas
            return newCanvas;
        }
    },
    created() {
        this.getCompanies();
    },
    mounted() {

    }
});
