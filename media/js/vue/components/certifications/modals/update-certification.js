Vue.component('update-certification', {
    template: `
        <div class="modal modal-cover">
            <div class="q4b-modal" @click.stop>
                <div class="q4b-modal-header">
                    <h1>{{ trans.update_certificate_for }} {{ craft.name }}</h1>
                </div>
                <div class="modal-separator"></div>
                <div class="q4b-modal-body">
                    <div class="create-certificate-form">
                        <div class="certificate-form">
                            <div class="certificate-data">
                                <div class="q4b-input-text" ref="certificateDescription">
                                    <div class="q4b-input-label">{{ trans.certificate_description }}</div>
                                    <input 
                                        type="text"
                                        autocomplete="off"
                                        class="q4b-input-text"
                                        :class="{'q4b-input-error-border': (errors.has('certificateDescription') && showErrors)}"
                                        v-validate.immediate="'required'"
                                        name="certificateDescription"
                                        :placeholder="trans.enter_certificate_description"
                                        :disabled="(!canChangeCertificate)"
                                        v-model="certificate.name"
                                    >
                                    <span v-show="errors.has('certificateDescription') && showErrors" class="q4b-error-text">{{ errors.first('certificateDescription') }}</span>
                                </div>
                                <div class="multiselect-col">
                                    <div class="filter-item-label" >{{ trans.status }}</div>
                                    <multiselect 
                                        v-model="selectedStatus"
                                        :option-height="104" 
                                        placeholder="select status" 
                                        :disabled="(certificateStatuses.length < 1) || !canChangeCertificateStatus" 
                                        :options="certificateStatuses" 
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
                                <div class="filter-item-checkbox" style="margin-top: 30px;">
                                    <span class="check-task" :class="{'q4b-disabled': !canChangeCertificate}">
                                        <input
                                            type="checkbox"
                                            style="position: relative !important;right: 0"
                                            v-model="certificate.sampleRequired"
                                        >
                                        <span class="checkboxImg"></span>
                                    </span>
                                    <div class="filter-item-label flex-between">{{ trans.sample_required }}</div>
                                </div>
                            </div>
                            <div ref="certificateParticipants">
                                <certificate-participants
                                    :translations="translations"
                                    :data="craftCertificate.participants"
                                    :canChange="canChangeCertificate"
                                    :showErrors="showErrors"
                                    @participantsUpdated="updateCertificateParticipants($event)"
                                />
                            </div>
                        </div>
                        <div class="certificate-chapters-form">
                            <template v-for="(certificateChapter, chapterIndex) in certificate.chapters">
                                <certificate-chapter
                                    :translations="translations"
                                    :certificateChapter="certificateChapter"
                                    :globalChapters="filteredGlobalChapters"
                                    :canChange="canChangeCertificate"
                                    :showErrors="showErrors"
                                    :scrollToChapter="(scrollToChapter === chapterIndex)"
                                    @chapterUpdated="updateCertificateChapter($event)"
                                    @chapterDeleted="deleteCertificateChapter($event)"
                                    @scrolled="scrollToChapter = null"
                                    :key="certificateChapter.uid"
                                />
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-separator"></div>
                <div class="q4b-modal-footer">
                    <button
                        class="q4b-btn-success add-chapter-btn"
                        :class="{'q4b-disabled': (!canCreateChapter || !canChangeCertificate)}"
                        @click="addCertificateChapter"
                    >{{ trans.add_chapter }}</button>
                    <span v-show="errors.has('certificateChaptersRequired') && showErrors" class="q4b-error-text">{{ errors.first('certificateChaptersRequired') }}</span>
                    <div class="modal-footer-buttons">
                        <button 
                            class="q4b-btn-success"
                            :class="{'q4b-disabled': (!canUpdateCertificate)}"
                            @click="updateCertificate(certificate)"
                        >{{ trans.update }}</button>
<!--                        <button -->
<!--                            class="q4b-btn-success"-->
<!--                            :class="{'q4b-disabled': (!canUpdateCertificate || !certificateValidation)}"-->
<!--                            @click="updateCertificate(certificate)"-->
<!--                        >{{ trans.update }}</button>-->
                        <button 
                            class="q4b-modal-close-btn" 
                            @click="$emit('onClose')"
                        >{{ trans.close }}</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: { required: true },
        craft: {required: true},
        craftCertificate: {required: true},
        globalChapters: { required: true },
        statuses: {required: true},
        userRole: {required: true}
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            initialCertificate: null,
            certificate: JSON.parse(JSON.stringify(this.craftCertificate)),
            certificateStatuses: null,
            selectedStatus: null,
            showErrors: false,
            scrollToChapter: null
        }
    },
    computed: {
        canCreateChapter() {
            return (this.certificate.chapters.length < this.globalChapters.length)
        },
        canChangeCertificate() {
            return this.initialCertificate.status === this.statuses.Waiting;
        },
        canChangeCertificateStatus() {
            const roles = ['super_admin', 'corporate_admin', 'corporate_infomanager', 'company_admin', 'company_infomanager', 'company_manager', 'general_admin', 'general_infomanager', 'project_admin'];

            switch (this.initialCertificate.status) {
                case this.statuses.Waiting:
                    return roles.includes(this.userRole);
                case this.statuses.Approved:
                    return this.userRole === 'super_admin';
            }
        },
        canSetCertificateStatusToWaiting() {
            const roles = ['super_admin'];
            return ((this.initialCertificate.status === this.statuses.Approved) && roles.includes(this.userRole))
        },
        canUpdateCertificateName() {
            //certificate name
            let canUpdateCertificateName = false;

            if((this.certificate.name !== this.initialCertificate.name)) {
                canUpdateCertificateName = true;
            }
            return canUpdateCertificateName;
        },
        canUpdateCertificateSampleRequired() {
            let canUpdateCertificateSampleRequired = false;
            if((this.certificate.sampleRequired !== this.initialCertificate.sampleRequired)) {
                canUpdateCertificateSampleRequired = true;
            }
            return canUpdateCertificateSampleRequired;

        },
        canUpdateCertificateStatus() {
            let canUpdateCertificateStatus = false;
            if((this.selectedStatus.name !== this.initialCertificate.status)) {
                canUpdateCertificateStatus = true;
            }
            return canUpdateCertificateStatus;

        },
        canUpdateCertificateParticipants() {
            //certificate participants
            let canUpdateParticipants = false;
            const newParticipants = (this.certificate.participants.filter(participant => !participant.id))

            const participantsHasSameSize = (this.certificate.participants?.length === this.initialCertificate.participants?.length);
            if((!participantsHasSameSize) || (newParticipants.length > 0)) {
                canUpdateParticipants = true;
            }
            return canUpdateParticipants;
        },
        canUpdateCertificateChapters() {
            //certificate chapters
            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;
            let canUpdateChapters = false;

            const hasNewChapters = (this.certificate.chapters.find(chapter => !chapter.id))
            const chaptersHasSameSize = (this.certificate.chapters?.length === this.initialCertificate.chapters?.length)

            if(hasNewChapters || !chaptersHasSameSize) {
                canUpdateChapters = true;
            }
            if(!hasNewChapters) {
                const certificateChapters = this.certificate.chapters.filter(chapter => chapter.id);

                certificateChapters.forEach(chapter => {
                    const initialChapter = this.initialCertificate.chapters.find(initialChapter => +initialChapter.id === +chapter.id);

                    if(+chapter.selectedChapter?.id !== +initialChapter.selectedChapter?.id) {
                        canUpdateChapters = true;
                    }

                    if(chapter.text !== initialChapter.text) {
                        canUpdateChapters = true;
                    }

                    //certificate chapter images
                    const chapterImagesHasSameSize = (chapter.images.length === initialChapter.images.length)
                    const hasNewImages = chapter.images.find(image => !image.id);

                    if(hasNewImages || !chapterImagesHasSameSize) {
                        canUpdateChapters = true
                    }
                })
            }
            return canUpdateChapters;
        },
        canUpdateCertificate() {
            return (
                this.canUpdateCertificateName ||
                this.canUpdateCertificateParticipants ||
                this.canUpdateCertificateChapters ||
                this.canUpdateCertificateSampleRequired ||
                this.canUpdateCertificateStatus
            )
        },
        filteredGlobalChapters() {
            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;

            return this.globalChapters.filter(globalChapter => {
                return (this.certificate.chapters.filter(certificateChapter => {
                    return globalChapter.id === certificateChapter?.selectedChapter?.id
                }).length === 0)
            })
        }
    },
    watch: {
        certificateStatuses(status) {
        }
    },
    methods: {
        certificateValidation() {

            let valid = true;

            if(!((this.certificate.name.trim()).length > 0)) {
                valid = false;
            }

            if(!(this.certificate.participants?.length > 0)) {
                this.errors.add({
                    field: 'certificateParticipantsRequired',
                    msg: this.trans.participants_required
                })
                valid = false;
            } else {
                this.errors.remove('certificateParticipantsRequired')
            }

            if(!(this.certificate.chapters?.length > 0)) {
                this.errors.add({
                    field: 'certificateChaptersRequired',
                    msg: this.trans.chapters_required
                })
                valid = false;
            } else {
                this.errors.remove('certificateChaptersRequired')
            }

            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;

            this.certificate.chapters.forEach((chapter, chapterIndex) => {
                if(!chapter.selectedChapter) {
                    this.errors.add({
                        field: `certificateChapterName_${chapterIndex}`,
                        msg: 'azaza'
                    });
                    valid = false;
                } else {
                    this.errors.remove(`certificateChapterName_${chapterIndex}`)
                }

                if(!(chapter.text.length > 0)) {
                    this.errors.add({
                        field: `certificateChapterContent_${chapterIndex}`,
                        msg: 'azaza'
                    });
                    valid = false;
                } else {
                    this.errors.remove(`certificateChapterContent_${chapterIndex}`)
                }
            })

            return valid;
        },
        addCertificateChapter() {
            if(!this.canCreateChapter) return false

            const chapterData = {
                uid: this.getRandomInt(100000, 9999999),
                id: null,
                chapterId: null,
                selectedChapter: null,
                text: '',
                images: []
            }
            this.certificate.chapters.push(chapterData)
            this.errors.remove('certificateChaptersRequired')
            this.showErrors = false;
        },
        updateCertificateChapter(chapterData) {
            this.certificate.chapters.forEach((certificateChapter, index) => {
                if(certificateChapter.uid === chapterData?.uid) {
                    this.certificate.chapters[index] = chapterData;
                    this.certificate.chaptersUpdated = true;
                }
            })
        },
        updateCertificateParticipants(certificateParticipants) {
            this.certificate.participants = certificateParticipants;
        },
        deleteCertificateChapter(chapterToDelete) {
            this.certificate.chapters.forEach((certificateChapter, index) => {
                if(certificateChapter.uid === chapterToDelete?.uid) {
                    this.certificate.chapters.splice(index, 1)
                    this.certificate.chaptersUpdated = true;
                }
            })
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        },
        updateCertificate() {
            if(!this.canUpdateCertificate) return false;
            if(!this.certificateValidation()) {
                this.showErrors = true;
                this.scrollToError();
                return false;
            } else {
                this.showErrors = false;
            }

            let certificateForm = JSON.parse(JSON.stringify(this.certificate));

            certificateForm.sampleRequired = certificateForm.sampleRequired ? "1" : "0";
            certificateForm.status = this.selectedStatus.name;
            // return false;
            this.$emit('certificateUpdated', certificateForm);
            this.$emit('onClose');
        },
        scrollToError() {
            if(this.errors.has('certificateDescription')) {
                this.$refs['certificateDescription'].scrollIntoView({block: "center", behavior: "smooth"})
                return true;
            }

            if(this.errors.has('certificateParticipantsRequired')) {
                this.$refs['certificateParticipants'].scrollIntoView({block: "center", behavior: "smooth"})
                return true;
            }

            this.certificate.chapters.some((chapter, chapterIndex) => {
                if(this.errors.has(`certificateChapterName_${chapterIndex}`)) {
                    this.scrollToChapter = chapterIndex;
                    this.errors.remove(`certificateChapterName_${chapterIndex}`)
                    return true;
                }

                if(this.errors.has(`certificateChapterContent_${chapterIndex}`)) {
                    this.scrollToChapter = chapterIndex;
                    this.errors.remove(`certificateChapterContent_${chapterIndex}`)

                    return true;
                }
            })
        },
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    created() {
        this.initialCertificate = JSON.parse(JSON.stringify(this.certificate));

        this.certificateStatuses = Object.values(this.statuses).map((statusName, statusIndex) => {
            return {
                id: statusIndex,
                name: statusName
            }
        })

        this.selectedStatus = this.certificateStatuses.filter(status => status.name === this.certificate.status)[0]
        this.initialCertificate.selectedStatus = this.certificateStatuses.filter(status =>  status.name === this.certificate.status)[0]
    },
    mounted() {
        this.$validator.localize('msg');
    },
});

