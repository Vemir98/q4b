Vue.component('statistics-pr', {
    template: `
        <div class="pdf-main">
            <div class="pdf-padding">
                <div class="pdf-heading">
                    <div class="pdf-logos">
                        <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                        <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
                    </div>
                    <div class="pdf-info">
                        <div class="project-logo">
                            <img src="/media/img/new-images/quality.png" alt="xzxzz">
                        </div>
        
                        <div class="pdf-lists">
                            <div class="pdf-list-top">
                                <ul class="pdf-ul">
                                    <li>AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAa</li>
                                    <li>שם עובד :</li>
                                </ul>
        
        
        
                                <ul class="pdf-ul">
                                    <li>תאריך :</li>
                                </ul>
        
                            </div>
                            <div class="pdf-list-bottom">
        
                                <ul class="pdf-ul">
                                    <li>שם הפרויקט :</li>
                                    <li>מבנה :</li>
                                    <li>קומה :</li>
                                </ul>
                                <ul class="pdf-ul">
                                    <li>מרחב :</li>
                                    <li>מחסן/'ם :</li>
                                    <li>חניה/ות :</li>
                                </ul>
                            </div>
                        </div>
        
                    </div>
                </div>
                <div class="pdf-content">
        
                    <!--personal details -->
                    <div class="pdf-content-title">
                        פרטי לקוח/ות
                    </div>
                        <div class="pr-dt">
                            <div class="pr-dt-info">
                                <ul class="pdf-ul">
                                    <li>inchvor mi ban</span></li>
                                </ul>
                            </div>
                            <div class="img">
                                <img src="/media/data/customers/121211221.jpg" alt="">
                            </div>
                        </div>
                </div>
                <div class="pdf-footer">
                    <p>--</p>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        siteUrl: {required: true},
        userPreferencesTypes: {required: true},
        userId: {required: true}
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
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
            }
        }
    },
    methods: {
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
    },
    created() {

    },
    mounted() {
        alert('barev')
        if(window.location.search) {
            (function(){
                if(window.opener) {
                    //window.opener.csrf = document.querySelector(Q4U.options.csrfTokenSelector).content;

                    // window.print();
                    // setTimeout(function () {
                        // window.close();
                    // }, 3000);
                }
            })()
        }
    }
});
