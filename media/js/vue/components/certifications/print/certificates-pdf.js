Vue.component('certificates-pdf', {
    template: `
            <div class='q4b-approve-el app-element-list new-styles'>
                <div class="universal-certification">
                    <div class="print-project-wrapper" v-if="Object.keys(project).length">
                        <div class="print-project">
                            <div class="print-project-image">
                                <img 
                                    :src="project.mainImage ? ('/' + project.mainImage.path + '/' + project.mainImage.name) : '/media/img/project-img.jpg'"
                                    alt="project images"
                                >
                            </div>
                            <div class="print-project-description">
                                <div class="print-project-description-section">
                                    <div><span class="q4b-print-label-inline">{{ trans.company_name }}: <bdi>&#x200E;{{ company?.name }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.project_name }}: <bdi>&#x200E;{{ project.name }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.owner }}: <bdi>&#x200E;{{ project.owner }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.start_date }}: <bdi>&#x200E;{{ convertTimestampToDate(project.start_date) }}&#x200E;</bdi></span></div>
                                </div>
                                <div class="print-project-description-section">
                                    <div><span class="q4b-print-label-inline">{{ trans.end_date }}: <bdi>&#x200E;{{ convertTimestampToDate(project.end_date) }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.project_id }}: <bdi>&#x200E;{{ project.id }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.project_status }}: <bdi>&#x200E;{{ project.status }}&#x200E;</bdi></span></div>
                                    <div><span class="q4b-print-label-inline">{{ trans.address }}: <bdi>&#x200E;{{ project.address }}&#x200E;</bdi></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-break"> </div>
                    <div class="craft-cert" v-for="(craft, craftIndex) in filteredCrafts" :key="craft.id">
                        <h2 class="print-craft-label">{{craft.name}}</h2><br>
                        <div class="print-craft-certificates">
                            <template v-if="craft?.items?.length > 0">
                                <div class="print-certificate-wrapper" v-for="(certificate, certificateIndex) in craft.items">
                                    <div class="print-certificate-content">
                                        <div class="print-certificate-name">
                                            <span class="q4b-print-label-inline">{{ trans.certificate_description }}: <bdi>&#x200E;{{ certificate.name }}&#x200E;</bdi></span>
    <!--                                        <div><bdi>{{ certificate.name }}</bdi></div>-->
    <!--                                        <span></span>-->
                                        </div>
                                        <div class="print-certificate-sample-required">
                                            <div v-if="certificate.sampleRequired === true || certificate.sampleRequired === '1'">
                                                <span class="q4b-print-label-inline">{{ trans.sample_required }}</span>
                                            </div>
                                        </div>
                                        <div class="print-certificate-update-date">
                                            <span class="q4b-print-label-inline">{{ trans.update_date }}: <bdi>&#x200E;{{ certificate.updatedAt ? convertTimestampToDate(certificate.updatedAt) : '' }}&#x200E;</bdi></span>
                                        </div>
                                        <div class="print-certificate-approved-by" v-if="certificate.approverName">
                                            <bdi><span class="q4b-print-label-inline">{{ trans.approved_by }}: </span></bdi>
                                            <bdi><span>{{ certificate.approverName }}</span></bdi>
                                        </div>
                                        <div class="print-certificate-status">
                                            <bdi><span class="q4b-print-label-inline">{{ trans.status }}: </span></bdi>
                                            <bdi><span>{{ trans[certificate.status] }}</span></bdi>
                                        </div>
                                    </div>
                                    <div class="print-certificate-participants-wrapper">
                                        <div class="print-certificate-participants-content">
                                            <div class="print-certificate-participants-title">
                                                <span class="q4b-print-text-bold">{{ trans.participants_list }}</span>
                                            </div>
                                            <div class="print-certificate-participants">
                                                <div 
                                                    class="print-certificate-participant"
                                                    v-for="(participant, participantIndex) in certificate.participants"
                                                >
                                                    <span>&#x200E;{{ participant.name }}&#x200E; <bdi>( &#x200E;{{ participant.position }}&#x200E; )</bdi></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="print-certificate-chapters-wrapper">
                                        <div class="print-certificate-chapters-content">
                                            <div class="print-certificate-chapters-title">
                                                <span class="q4b-print-text-bold">{{ trans.chapters_list }}</span>
                                            </div>
                                            <div class="print-certificate-chapters">
                                                <div class="print-certificate-chapter" v-for="(chapter, chapterIndex) in certificate.chapters">
                                                    <div class="print-certificate-chapters-name">
                                                        <bdi><span class="q4b-print-label-inline">{{ trans.chapter }}: </span></bdi>
                                                        <bdi><span>{{ chapter.name }}</span></bdi>
                                                    </div>
                                                    <div class="q4b-textarea">
                                                        <div class="q4b-input-label">{{ trans.chapter_content }}</div>
                                                        <div class="q4b-text-wrapped"><bdo dir="rtl"><span>{{ chapter.text }}</span></bdo></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
    `,
    props: {
        translations: {required: true},
        crafts: {required: true},
        project: {required: true},
        company: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },
    data() {
        return {
            trans: JSON.parse(this.translations),

        };
    },
    computed: {
      filteredCrafts() {
          return this.crafts.filter(craft => {
              return craft.items.length > 0
          })
      }
    },
    methods: {
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1) > 9) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
    },
    mounted() {
        console.log('PROJECT', this.project)
        console.log('COMPANY', this.company)
        console.log('CRAFTS', this.crafts)
        // window.addEventListener('load', () => {
        //     var parentWidth = $(".scrollable-table").width();
        //     console.log(parentWidth - 40)
        //     $(".scrollable-table").css({
        //         width:  parentWidth - 40 + "px"
        //     })
        // })

    }
});