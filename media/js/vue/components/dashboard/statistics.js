Vue.component('statistics', {
    template: `
        <div id="dashboard-content" class="new-styles">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
<!--            <button @click="resetStatistics">reset</button>-->
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
                                                <div class="statistics-data-icon blue"></div>
                                                <div class="statistics-data-descr">{{ trans.other_qc_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span>{{ statistics.qc.data.others }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links"><a :href="siteUrl + '/reports#tab_qc_controls'" target="_blank">{{ trans.show_full_reports }}</a></div>
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
                        <div class="dashboard-statistics-links"><a :href="siteUrl + '/reports/place'" target="_blank">{{ trans.show_full_reports }}</a></div>
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
<!--                        <div class="dashboard-statistics-links"><a href="#">{{ trans.show_full_reports }}</a></div>-->
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
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.places_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.delivery.data.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.deliveries_done }}</div>
                                            </div>
                                        </td>
                                        <td><span class="green bold">{{ statistics.delivery.data.delivery }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.pre_deliveries_done }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.delivery.data.preDelivery }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links"><a :href="siteUrl + '/reports/delivery'" target="_blank">{{ trans.show_full_reports }}</a></div>
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
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon black"></div>
                                                <div class="statistics-data-descr">{{ trans.ears_in_system }}</div>
                                            </div>
                                        </td>
                                        <td><span class="black bold">{{ statistics.ear.data.total }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon red"></div>
                                                <div class="statistics-data-descr">{{ trans.not_appropriate_ears }}</div>
                                            </div>
                                        </td>
                                        <td><span class="red bold">{{ statistics.ear.data.notAppropriate }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="statistics-data-item">
                                                <div class="statistics-data-icon green"></div>
                                                <div class="statistics-data-descr">{{ trans.appropriate_ears }}</div>
                                            </div> 
                                        </td>
                                        <td><span class="green bold">{{ statistics.ear.data.appropriate }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="dashboard-statistics-links"><a :href="siteUrl + '/reports/approve_element'" target="_blank">{{ trans.show_full_reports }}</a></div>
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
                        <div class="dashboard-statistics-links"><a :href="siteUrl + '/reports/labtests'" target="_blank">{{ trans.show_full_reports }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        siteUrl: {required: true}
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
        }
    },
    data() {
        return {
            showLoader: false,
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
        }
    },
    watch: {
        companies(companies) {
            this.projects = [];
            this.selectedProjects = [];

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

            this.generateStatistics();
        },
        selectedCompanies(companies) {
            this.projects = [];
            this.selectedProjects = [];

            companies.forEach(company => {
                if(company.projects) {
                    this.projects = this.projects.concat(Object.values(company.projects))
                    this.toggleSelectAll('selectedProjects', 'projects');
                }
            }, this)
        },
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
        getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
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

            this.getQcStatistics(params);
            this.getPlaceStatistics(params);
            this.getCertificatesStatistics(params);
            this.getDeliveryStatistics(params);
            this.getEarStatistics(params);
            this.getLabControlStatistics(params);
            this.statisticsPeriodName = this.selectedRange.name;
        },
        getQcStatistics(params) {
            this.showLoader = true;
            let url = '/projects/statistics/qc';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    // console.log('QC', response.item)
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
                                data: this.statistics.qc.data.others,
                                backgroundColor: 'rgba(157, 156, 201, 1)',
                                borderColor: 'rgba(157, 156, 201, 1)',
                            }
                        ],
                        [this.trans.invalid, this.trans.repaired, this.trans.other],
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
                    // console.log('EAR', response.item)
                    this.statistics.ear.data = response.item;
                    this.showLoader = false;
                    this.statistics.ear.chart = this.createChart(
                        this.$refs['ear-chart'],
                        'pie',
                        [
                            {
                                data: this.statistics.ear.data.notAppropriate,
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                            },
                            {
                                data: this.statistics.ear.data.appropriate,
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                                borderColor: 'rgba(15, 154, 96, 1)',
                            },
                        ],
                        [this.trans.not_appropriate, this.trans.appropriate],
                        this.statistics.ear.data.total
                    );
                })
        },
        getDeliveryStatistics(params) {
            this.showLoader = true;

            let url = '/projects/statistics/delivery';
            qfetch(url + params, {method: 'GET', headers: {}})
                .then(response => {
                    // console.log('DELIVERY', response.item)
                    this.statistics.delivery.data = response.item;
                    this.showLoader = false;
                    this.statistics.delivery.chart = this.createChart(
                        this.$refs['delivery-chart'],
                        'bar',
                        [
                            {
                                label: this.trans.delivery,
                                data: [this.statistics.delivery.data.delivery],
                                backgroundColor: 'rgba(15, 154, 96, 1)',
                            },
                            {
                                label: this.trans.pre_delivery,
                                data: [this.statistics.delivery.data.preDelivery],
                                backgroundColor: 'rgba(255, 0, 0, 1)',
                            }
                        ],
                        [this.trans.places],
                        this.statistics.delivery.data.total
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
                    // console.log('CERTIFICATES', response.item)
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
                                borderWidth: 1
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
        }
    },
    created() {
        this.getCompanies();
    },
    mounted() {
    }
});

