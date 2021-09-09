Vue.component('report-item', {
    template: `
    <section class='q4b-approve-el approve-el-view'>
        <div v-if="showLoader" class="loader_backdrop_vue">
            <div class="loader"></div>
        </div>
        <div class="approve-elv-top-wrap flex-between">
            <div class="approve-elv-top-left flex-start">
                <div class="approve-elv-top-item">
                    <a class="back-to-filter">
                        <i @click="$emit('toReportsList')" class="q4bikon-arrow_back2"></i>
                    </a>
                    <span class="approve-elv-top-item-name">{{ trans.check_number }}</span>
                    <span class="approve-elv-top-item-value">{{ report.id }}</span>

                </div>
                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">{{ trans.element }}</span>
                    <span class="approve-elv-top-item-value">{{ report.elementName }}</span>
                </div>
            </div>
            <div class="approve-elv-top-right flex-end">

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">{{ trans.createdBy }}</span>
                    <span class="approve-elv-top-item-value">{{ report.creatorName }} ({{ convertTimestampToDate(report.createdAt) }})</span>
                </div>

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">{{ trans.status }}</span>
                    <span class="approve-elv-top-item-value">{{ +report.appropriate === 1 ? trans.appropriate : trans.not_appropriate }}</span>
                </div>

            </div>
        </div>
        <div class="approve-elv-filter flex-end">
            <div v-if="report.managerSignature" class="sign-section">
                <div class="filter-item-label">{{ trans.manager_signature }}</div>
                <div class="sign-content">
                    <img 
                        :src="report.managerSignature ? imageUrl+report.managerSignature.image : ''"
                    > 
               </div>
            </div>
            <div class="filter-item">
                <div class="filter-item-label">{{ trans.manager_status }}</div>
                <div class="multiselect-col">
                    <multiselect 
                        v-model="selectedStatus"
                        :option-height="104" 
                        :placeholder="trans.select_status"
                        :disabled="(elStatuses.length < 1) || !checkInitialReportAllTasksEnabled() || this.nothingToUpdate() || (!canUpdate && (userRole !== 'super_admin') )" 
                        :options="elStatuses" 
                        track-by="id" 
                        label="name"
                        @select="ReportStatusChanged($event)"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            {{ trans[props.option.name] }}
                        </template>
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

        <div class="approve-elv-properties flex-start disabled">
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.company }}</span>
                <span class="approve-elv-property-value">{{ company?.name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.project }}</span>
                <span class="approve-elv-property-value">{{ project?.name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.structure }}</span>
                <span class="approve-elv-property-value">{{ report.objectName }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.floor_level }}</span>
                <span class="approve-elv-property-value">{{ report.floorName ? report.floorName : report.floorNumber  }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.place }}</span>
                <span class="approve-elv-property-value">{{ report.placeName }}</span>
            </div>

        </div>

        <div class="report-buttons">
            <div class="report-buttons-wraper" style="opacity: .5">
                <a class="report-button" style="cursor: auto"><i class="q4bikon-print1"></i>{{ trans.print }}</a>
            </div>
        </div>

        <div class="approve-elv-reports">
            <div class="approve-elv-reports-top flex-between">
                <div class="approve-elv-reports-top-headline">{{ trans.speciality_list }}</div>
                <div class="approve-elv-reports-delete-all" >
                     <button
                         @click="openConfirmDeletePopup = true"
                         class="delete-all"
                         v-if="canUpdate"
                     > 
                       {{ trans.delete_all }}
                       </button>
                </div>
            </div>
            <div class="approve-elv-reports-wraper">
                <template v-for="(speciality, specialityIndex) in report.specialities">
                    <div :key="speciality.id" class="approve-elv-report-item">
                        <div class="approve-elv-report-top flex-start">
                            <span :class="['approve-elv-report-name', {'not-appropriate': speciality.appropriate === '0', 'appropriate': speciality.appropriate === '1' }]">{{ speciality.craftName }}</span>
                            <span class="approve-elv-report-status flex-start">
                                <span class="approve-elv-report-status-title">{{ trans.status }}</span>
                                <span class="approve-elv-report-status-value">{{ +speciality.appropriate ? trans.appropriate : trans.not_appropriate }}</span>
                            </span>
                            <span 
                                class="approve-elv-report-view" 
                                v-if="speciality.qualityControl"
                            >
                                <a @click="getGenerateQcHref(speciality)">
                                    {{ trans.view_qc }}
                                </a>
                            </span>
                        </div>
                        <div class="approve-elv-report-sign">
                                <template v-if="speciality.signatures.length">
                                    <div class="approve-elv-properties flex-start" v-for="signature in speciality.signatures">
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.updated_by }}</span>
                                            <span class="approve-elv-property-value">{{ signature.creatorName }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.date }}</span>
                                            <span class="approve-elv-property-value">{{ convertTimestampToDate(signature.createdAt) }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.approved_by }}</span>
                                            <span class="approve-elv-property-value">{{ signature.name }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.position }}</span>
                                            <span class="approve-elv-property-value">{{ signature.position }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start sign-image">
                                            <img :src="signature.id ? imageUrl+signature.image : signature.image">
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="approve-elv-properties flex-start">
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name">{{ trans.updated_by }}</span>
                                            <span class="approve-elv-property-value">{{ speciality.updatorName }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name">{{ trans.date }}</span>
                                            <span class="approve-elv-property-value">{{ convertTimestampToDate(speciality.updatedAt) }}</span>
                                        </div>
                                    </div>
                                </template>
                        </div>
                        <div class="ltest_info_certificate">
                            <div class="ltest_info_certificate_title">{{ trans.notes }}</div>
                            <div class="ltest_info_certificate_area">
                                <div class="labtest_edit_textarea">
                                    <textarea 
                                        cols="30" 
                                        rows="10" 
                                        @input="specialityNoteChanged($event, speciality)" 
                                        name="delivery_cert"
                                        :disabled="!canUpdate"
                                    >{{ speciality.notice }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="approve-elv-report-tasks">
                            <div class="report_tasks">
                                <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">
                                    {{ trans.tasks }}
                                    <span>*</span>
                                </h4>
                                <div class="report_tasks_wraper">
                                    <template v-for="task in speciality.tasks">
                                        <div 
                                            :class="[ 'report_tasks_item', {'not-appropriate': task.appropriate === '0', 'appropriate': task.appropriate === '1' }]"
                                        >
                                            <div class="approve-elv-task-status" @click="changeTaskStatus(task, speciality)"></div>
                                            <div class="report_task_title">{{ trans.task }} {{ task.taskId }}</div>
                                            <div class="report_task_desc_wrap">
                                                <div class="report_task_descripticon">
                                                        <div>{{ task.taskName }}</div>
                                                </div>
                                                <div class="report_task_status"></div>
                                            </div>
                                        </div>
                                    </template>  
                                </div>
                                
                            </div>
                        </div>
                        <div class="report-buttons-update flex-start">
                            <button
                                 :class="['report-button', { 'labtest-disabled': test1(specialityIndex) }]" 
                                 @click="updateReport(speciality)"
                             >              
                                 {{ trans.update }}
                             </button>
                             <button 
                                v-if="checkSpecialityAllTasksEnabled(speciality.tasks)" 
                                @click="togglePopup(speciality,true, false, 'button')" 
                                :class="['report-button', {'labtest-disabled': !canUpdate}]"
                             >
                                {{ trans.add_signature }}
                             </button>
                        </div>
                        <span style="display: none">{{ report.updated }}</span>
                    </div>
                </template>
            </div>
        </div>
        <div class="modul-popup-wrap approve-elv-popup" v-show="openSignaturePopup">
            <div class="modul-popup">
                <div class="modul-popup-top">
                    <span class="modul-popup-headline">{{ trans.please_sign }}</span>
                    <span class="modul-popup-close" @click="togglePopup(null, true, true)"><i class="q4bikon-close"></i></span>
                </div>
                <div class="modul-popup-main">
                    <div class="approve-elv-popup-inputs flex-between">
                        <div class="filter-item">
                            <div class="filter-item-label">{{ trans.signer_name }} *</div>
                            <input type="text" name="signer_name" v-model="currentSignerName">
                        </div>
                        <div class="filter-item">
                            <div class="filter-item-label">{{ trans.position }} *</div>
                            <input type="text" name="signer_position" v-model="currentSignerPosition">
                        </div>
                    </div>

                    <div class="approve-elv-popup-sign">
                        <canvas ref="signaturePad" width="500" height="250"></canvas>
                        <span class="clear-sign" @click="clearSignaturePad">{{ trans.clear_sign }}</span>
                        <div class="approve-elv-popup-sign-line"></div>
                    </div>
                </div>

                <div class="modul-popup-btns">
                    <button
                        :class="['modul-popup-Confirm', {'labtest-disabled': (canSign && canUpdate) ? false : true}]"
                        @click="pushSignatures"
                    >
                      {{ trans.sign }}
                    </button>
                    <button
                        v-if="popupWasOpenFrom !== 'managerSignature'" 
                        :class="['modul-popup-Cancel', {'labtest-disabled': (canSign && canUpdate) ? false : true}]" 
                        @click="addSignature"
                    >
                        {{ trans.additional_signature }}
                    </button>
                </div>
            </div>
        </div>
        <confirm-modal 
            v-if="openConfirmDeletePopup"
            :msg="trans.are_you_sure_to_delete" 
            :trans="trans" 
            :deletable="trans.approve_element"
            :deletable-id="report.id"
            :modal-data="{}"
            @closeConfirm="openConfirmDeletePopup = false"
            @deleteConfirmed="deleteReport"
        />
    </section>
    `,
    props: {
        siteUrl: {required: true},
        imageUrl: {required: true},
        statuses: {required: true},
        project: {required: true},
        company: {required: true},
        data: {required: true},
        translations: {required: true},
        filters: {required: true},
        username: {required: true},
        userProfession: {required: true},
        userRole: {required: true}
    },
    data() {
        return {
            showLoader: false,
            openSignaturePopup: false,
            openConfirmDeletePopup: false,
            popupWasOpenFrom: '',
            trans: JSON.parse(this.translations),
            elStatuses: this.getStatuses(this.statuses),
            time: [],
            report: JSON.parse(JSON.stringify(this.data)),
            userProf: JSON.parse(JSON.stringify(this.userProfession)),
            initialReport: null,
            newSignatures: [],
            managerSignature: null,
            currentSpeciality: null,
            currentTask: null,
            currentSignerName: '',
            currentSignerPosition: '',
            keepOtherSignatures: false,
            canUpdateSpeciality: {},
            canChangeManagerStatus: false,
            signatureDrawn: false,
            signaturePad: null,
            selectedStatus: {},
        }
    },
    components: { Multiselect: window.VueMultiselect.default },
    computed: {
      canSign() {
         return (this.signatureDrawn && this.currentSignerName && this.currentSignerPosition)
      },
      canUpdate() {
          return (this.report.status === 'waiting')
      },
    },
    watch: {
      report: {
          handler() {
              this.report.specialities.forEach((speciality, index) => {
                  this.canUpdateSpeciality[index] = (speciality.canUpdateNote || speciality.canUpdateSignatures || speciality.canUpdateTaskStatuses)
                  this.report.updated = false;
              })
          },
          deep: true
      }
    },
    created() {
        var date = new Date();
        date.setDate(1);
        date.setMonth(date.getMonth() - 6);
        let end = new Date();
        end.setDate(end.getDate() + 1);
        this.time = [date, end];

        this.getQcs();
        this.initialReport = JSON.parse(JSON.stringify(this.report));
    },
    methods: {
        togglePopup(speciality, keepOthers = false, closingWithoutSign = false, from = '') {
            if(this.openSignaturePopup) {
                switch(this.popupWasOpenFrom) {
                    case 'managerSignature':
                        if(closingWithoutSign) {
                            this.selectedStatus = this.elStatuses[0]
                        }
                        break;
                    case 'button':
                        break;
                    case 'ticket':
                        if(this.popupWasOpenFrom === 'ticket') {
                            if(this.currentTask && keepOthers  &&  closingWithoutSign) {
                                const specialityIndex = this.initialReport.specialities.findIndex(spec => +spec.id === +this.currentSpeciality.id);
                                const taskIndex = this.initialReport.specialities[specialityIndex].tasks.findIndex(task => +task.id === +this.currentTask.id);
                                this.report.specialities[specialityIndex].tasks[taskIndex].appropriate = "0";
                                this.report.specialities[specialityIndex].appropriate = "0";
                                this.report.specialities[specialityIndex].canUpdateTaskStatuses = !this.report.specialities[specialityIndex].canUpdateTaskStatuses;
                                this.report.updated = true;
                            }
                        }
                        break;
                    default:
                        break;
                }

                this.openSignaturePopup = false;
                this.clearSignaturePad();
                this.currentSpeciality = null;
                this.currentTask = null;
                this.managerSignature = null;
                this.newSignatures = [];

            } else {
                this.keepOtherSignatures = keepOthers;
                this.currentSpeciality = speciality;
                this.currentSignerPosition = this.userProf;
                this.currentSignerName = this.username;
                this.openSignaturePopup = true;
            }
            if(from !== '') this.popupWasOpenFrom = from;
        },
        clearSignaturePad() {
            this.signaturePad.clear();
            this.signatureDrawn = false;
        },
        addSignature() {
            switch (this.popupWasOpenFrom) {
                case "managerSignature":
                    this.managerSignature = {
                        'name': this.currentSignerName,
                        'position': this.currentSignerPosition,
                        'image': this.signaturePad.toDataURL(),
                    }
                break;

                default:
                    this.newSignatures.push({
                        'elAppId': this.report.id,
                        'elAppCraftId': this.currentSpeciality.id,
                        'name': this.currentSignerName,
                        'position': this.currentSignerPosition,
                        'image': this.signaturePad.toDataURL(),
                        'createdAt': Date.now() / 1000,
                        'creatorName': this.username
                    })
                break;
            }

            this.currentSignerName = this.username;
            this.currentSignerPosition = this.userProf;
            this.clearSignaturePad();
        },
        pushSignatures() {
            this.addSignature();

            switch(this.popupWasOpenFrom) {
                case "managerSignature":
                    this.updateManagerSignature()
                break;
                default:
                    this.report.specialities.forEach(speciality => {
                        if(+speciality.id === +this.currentSpeciality.id) {
                            if(this.keepOtherSignatures) {
                                speciality.signatures = speciality.signatures.concat(this.newSignatures);
                            } else {
                                speciality.signatures = this.newSignatures;
                            }
                            this.report.updated = true;
                            speciality.canUpdateSignatures = true;
                        }
                    })
                    this.newSignatures = [];
                break;
            }
            this.openSignaturePopup = false;
        },
        changeTaskStatus(task, speciality) {
            if(!this.canUpdate) return false;
            switch (+task.appropriate) {
                case 1:
                    task.appropriate = "0";
                    speciality.deletedSignatures = speciality.signatures.map(signature => signature.id)
                    speciality.signatures = [];
                    speciality.appropriate = "0";
                    this.report.updated = true;
                    break;
                case 0:
                    task.appropriate = "1";
                    this.report.updated = true;
                    break;
            }

            speciality.canUpdateTaskStatuses = this.checkTaskStatusesUpdated(speciality);

            if(this.checkSpecialityAllTasksEnabled(speciality.tasks)) {
                speciality.appropriate = "1";
                this.currentTask = task;
                this.currentSpeciality = speciality;
                this.togglePopup(speciality, false, false, 'ticket');
            }
        },
        checkSpecialityAllTasksEnabled(specialityTasks) {
            const result =  specialityTasks.filter(task => {
                return task.appropriate === '0'
            })
            return result.length < 1
        },
        checkInitialReportAllTasksEnabled() {
            const result = this.initialReport.specialities.filter(speciality => {
                return this.checkSpecialityAllTasksEnabled(speciality.tasks)
            })
            return (result.length === this.initialReport.specialities.length)
        },
        checkReportAllTasksEnabled() {
            const result = this.report.specialities.filter(speciality => {
                return this.checkSpecialityAllTasksEnabled(speciality.tasks)
            })
            return (result.length === this.report.specialities.length)
        },
        checkTaskStatusesUpdated(speciality) {
            let updated = false;
            const specialityIndex = this.initialReport.specialities.findIndex(spec => +spec.id === +speciality.id);
            speciality.tasks.forEach((task, taskIndex) => {
                if(+task.appropriate !== +this.initialReport.specialities[specialityIndex].tasks[taskIndex].appropriate) {
                    updated = true;
                    return false;
                }
            })
            return updated;
        },
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1).length > 1) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        updateReport(speciality) {
            this.showLoader = true;
            let url = `/el-approvals/${this.report.id}`;
            speciality.projectId = this.project.id;
            qfetch(url, {method: 'PUT', headers: {}, body: speciality})
                .then(response => {
                    this.getSpeciality(speciality.id);
                    this.showLoader = false;
                })
        },
        specialityNoteChanged(event, speciality) {
            const specialityIndex = this.report.specialities.findIndex(spec => +spec.id === +speciality.id);

            this.report.specialities[specialityIndex].canUpdateNote = event.target.value !== this.initialReport.specialities[specialityIndex].notice;
            this.report.specialities[specialityIndex].notice = event.target.value;
            this.report.updated = true;
        },
        getStatuses(statusesArr) {
            let statuses = [];
            statusesArr.forEach((item, ind) => {
                statuses.push({ id: ind, name: item })
            })
            return statuses
        },
        getSpeciality(specialityId) {
            this.showLoader = true;
            let url = `/el-approvals/${this.report.id}/specialities/${specialityId}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    const specialityIndex = this.report.specialities.findIndex(spec => +spec.id === +specialityId);

                    this.report.specialities[specialityIndex] = response.item;
                    this.initialReport.specialities[specialityIndex] = JSON.parse(JSON.stringify(response.item));
                    this.report.specialities[specialityIndex].canUpdateSignatures = false;
                    this.report.specialities[specialityIndex].canUpdateNote = false;
                    this.report.specialities[specialityIndex].canUpdateTaskStatuses = false;

                    if(this.checkInitialReportAllTasksEnabled()) {
                        this.initialReport.appropriate = "1";
                        this.report.appropriate = "1";
                    } else {
                        this.initialReport.appropriate = "0";
                        this.report.appropriate = "0";
                    }

                    this.report.updated = true;
                    this.showLoader = false;
                })
        },
        ReportStatusChanged(status) {
          switch (status.name) {
              case "approved":
                  this.togglePopup(null, true, false, 'managerSignature')
                  break;
              case "waiting":
                  this.updateManagerSignature();
                  break;
          }
        },
        updateManagerSignature() {
            this.showLoader = true;
            let url = `/el-approvals/${this.report.id}/add_signature`;
            qfetch(url, {method: 'POST', headers: {}, body:this.managerSignature})
                .then(response => {
                    console.log('RESPONSE',response)
                    this.report.managerSignature = response.item;
                    this.initialReport.managerSignature = response.item;
                    this.changeReportStatus(this.selectedStatus)
                    this.showLoader = false;
                })
                .catch(error => {
                    this.selectedStatus = this.elStatuses.filter(elStatus => {
                        return elStatus.name !== status.name;
                    })[0];
                    this.showLoader = false;
                })
        },
        changeReportStatus(status) {
            this.showLoader = true;
            let url = `/el-approvals/${this.report.id}/status`;
            qfetch(url, {method: 'PUT', headers: {}, body:{status: status.name}})
                .then(response => {
                    this.initialReport.status = status.name;
                    this.report.status = status.name;
                    if(this.report.status === 'waiting') {
                        this.report.managerSignature = null;
                    }
                    this.report.updated = true;
                    this.showLoader = false;
                })
                .catch(error => {
                    this.selectedStatus = this.elStatuses.filter(elStatus => {
                        return elStatus.name !== status.name;
                    })[0];
                    this.showLoader = false;
                })
        },
        deleteReport() {
            this.showLoader = true;
            let url = `/el-approvals/${this.report.id}/delete`;
            qfetch(url, {method: 'DELETE', headers: {}})
                .then(response => {
                    this.$emit('reportDeleted')
                })
        },
        getGenerateQcHref(speciality) {
            let url = `${this.siteUrl}/reports/generate`;
            let date = this.convertTimestampToDate(speciality.qcCreatedAt);
            let queryParams = `?from=${date}&to=${date}&crafts[]=${speciality.craftId}&company=${this.company.id}&project=${this.project.id}&el_app_id=${this.report.id}#tab_qc_controls`;
            url += queryParams;
            window.open(url);
        },
        test1(specialityIndex) {
            if(this.canUpdateSpeciality[specialityIndex]) {
                return !this.canUpdate;
            } else {
                return true;
            }
        },
        nothingToUpdate() {
           let array = this.report.specialities.filter((speciality,index ) => {
               return !this.test1(index)
           })

            return (array.length === this.report.specialities.length)
        },
        getQcs() {
            this.report.specialities.forEach(speciality => {

                if(speciality.qualityControl) {
                    this.showLoader = true;

                    let url = '/quality-controls/get/'+speciality.qualityControl+'?fields=createdAt';

                    qfetch(url, {method: 'GET', headers: {}})
                        .then(response => {
                            console.log('RESPONSE', response)
                            speciality.qcCreatedAt = response.item.createdAt
                            this.showLoader = false;
                        })
                }
            })
        }
    },
    mounted() {
        this.signaturePad = new SignaturePad(this.$refs['signaturePad'], {
            penColor: "rgb(18,4,134)",
            onEnd: () => {
                this.signatureDrawn = true;
            }
        })
        this.selectedStatus = this.elStatuses.find(status => status.name === this.report.status)
    },
});

