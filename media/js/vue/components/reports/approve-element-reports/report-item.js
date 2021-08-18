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
                    <span class="approve-elv-top-item-value">{{ report.element_name }}</span>
                </div>
            </div>
            <div class="approve-elv-top-right flex-end">

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">{{ trans.created_by }}</span>
                    <span class="approve-elv-top-item-value">{{ report.creator_name }} ({{ convertTimestampToDate(report.created_at) }})</span>

                </div>

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">{{ trans.status }}</span>
                    <span class="approve-elv-top-item-value">{{ data.status }}</span>
                </div>

            </div>
        </div>
        <div class="approve-elv-filter flex-end">
            <div class="filter-item">
                <div class="filter-item-label">{{ trans.manager_status }}</div>
                <div class="multiselect-col">
                    <multiselect 
                        v-model="selectedStatus"
                        :option-height="104" 
                        placeholder="Waiting"
                        :disabled="(elStatuses.length < 1) || !checkReportsAllTasksEnabled()" 
                        :options="elStatuses" 
                        track-by="id" 
                        label="name"
                        :searchable="true" 
                        :allow-empty="false"
                        :show-labels="false"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            {{ props.option.name }}
                        </template>
                        <template slot="option" slot-scope="props">
                            <span>{{ props.option.name }}</span>
                        </template>
                        <template slot="option-selected" slot-scope="props">
                            <span>{{ props.option.name }}</span>
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
                <span class="approve-elv-property-value">{{ report.object_name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.floor_level }}</span>
                <span class="approve-elv-property-value">{{ report.floor_name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">{{ trans.place }}</span>
                <span class="approve-elv-property-value">{{ report.place_name }}</span>
            </div>

        </div>

        <div class="report-buttons">
            <div class="report-buttons-wraper ">
                <a class="report-button  " href=""><i class="q4bikon-print1"></i>{{ trans.print }}</a>
            </div>
        </div>

        <div class="approve-elv-reports">
            <div class="approve-elv-reports-top flex-between">
                <div class="approve-elv-reports-top-headline">{{ trans.speciality_list }}</div>
                <div class="approve-elv-reports-delete-all"><button class="delete-all">Delete All[*]</button>
                </div>
            </div>
            <div class="approve-elv-reports-wraper">
                <template v-for="(speciality, specialityIndex) in report.specialities">
                    <div :key="speciality.id" class="approve-elv-report-item">
                        <div class="approve-elv-report-top flex-start">
                            <span :class="['approve-elv-report-name', {'not-appropriate': speciality.appropriate === '0', 'appropriate': speciality.appropriate === '1' }]">{{ speciality.craft_name }}</span>
                            <span class="approve-elv-report-status flex-start">
                                <span class="approve-elv-report-status-title">{{ trans.status }}</span>
                                <span class="approve-elv-report-status-value">{{ +speciality.appropriate ? 'Appropriate[*]' : 'Not Appropriate[*]' }}</span>
                            </span>
                            <span class="approve-elv-report-view"><a href="">{{ trans.view_qc }}</a></span>
                        </div>
                        <div class="approve-elv-report-sign">
                                <template v-if="speciality.signatures.length">
                                    <div class="approve-elv-properties flex-start" v-for="signature in speciality.signatures">
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.updated_by }}</span>
                                            <span class="approve-elv-property-value">{{ signature.creator_name }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name ">{{ trans.date }}</span>
                                            <span class="approve-elv-property-value">{{ convertTimestampToDate(signature.created_at) }}</span>
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
                                            <img :src="signature.image">
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="approve-elv-properties flex-start">
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name">{{ trans.updated_by }}</span>
                                            <span class="approve-elv-property-value">{{ report.creator_name }}</span>
                                        </div>
                                        <div class=" approve-elv-property flex-start">
                                            <span class="approve-elv-properties-name">{{ trans.date }}</span>
                                            <span class="approve-elv-property-value">{{ convertTimestampToDate(report.created_at) }}</span>
                                        </div>
                                    </div>
                                </template>
                        </div>
                        <div class="ltest_info_certificate">
                            <div class="ltest_info_certificate_title">{{ trans.notes }}</div>
                            <div class="ltest_info_certificate_area">
                                <div class="labtest_edit_textarea">
                                    <textarea cols="30" rows="10" @keyup="specialityNoteChanged($event, speciality)" name="delivery_cert">{{ speciality.notice }}</textarea>
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
                                            @click="changeTaskStatus(task, speciality)"
                                        >
                                            <div class="report_task_title">{{ trans.task }} {{ task.task_id }}</div>
                                            <div class="report_task_desc_wrap">
                                                <div class="report_task_descripticon">
                                                        <div>{{ task.task_name }}</div>
                                                </div>
                                                <div class="report_task_status"></div>
                                            </div>
                                        </div>
                                    </template>  
                                </div>
                                
                            </div>
                        </div>
                        <div class="report-buttons-update flex-start">
                            <button v-if="checkSpecialityAllTasksEnabled(speciality.tasks)" @click="togglePopup(speciality,true)" class="report-button">Add signature</button>
                            <button
                                 :class="['report-button', {'labtest-disabled': !canUpdateSpeciality[specialityIndex]}]" 
                                 @click="updateReport(speciality)"
                             >              
                                 {{ trans.update }}
                             </button>
                        </div>
                        <span style="display: none">{{ report.updated }}</span>
                    </div>
                </template>
            </div>
        </div>
        <div class="modul-popup-wrap approve-elv-popup" v-show="openPopup">
            <div class="modul-popup">
                <div class="modul-popup-top">
                    <span class="modul-popup-headline">Please sign[*]</span>
                    <span class="modul-popup-close" @click="togglePopup(null, true, true)"><i class="q4bikon-close"></i></span>
                </div>
                <div class="modul-popup-main">
                    <div class="approve-elv-popup-inputs flex-between">
                        <div class="filter-item">
                            <div class="filter-item-label">{{ trans.signer_name }}</div>
                            <input type="text" name="signer_name" v-model="currentSignerName">
                        </div>
                        <div class="filter-item">
                            <div class="filter-item-label">{{ trans.position }}</div>
                            <input type="text" name="signer_position" v-model="currentSignerPosition">
                        </div>
                    </div>

                    <div class="approve-elv-popup-sign">
                        <canvas ref="signaturePad"></canvas>
                        <span class="clear-sign" @click="clearSignaturePad">Clear sign[*]</span>
                        <div class="approve-elv-popup-sign-line"></div>
                    </div>
                </div>

                <div class="modul-popup-btns">
                    <button class="modul-popup-Confirm" @click="pushSignatures">Sign[*]</button>
                    <button class="modul-popup-Cancel" @click="addSignature">{{ trans.additional_signature }}</button>
                </div>
            </div>
        </div>
    </section>
    `,
    props: {
        statuses: {required: true},
        project: {required: true},
        company: {required: true},
        data: {required: true},
        translations: {required: true},
        filters: {required: true},
        username: {required: true}
    },
    data() {
        return {
            showLoader: false,
            openPopup: false,
            trans: JSON.parse(this.translations),
            elStatuses: this.getStatuses(this.statuses),
            time: [],
            report: JSON.parse(JSON.stringify(this.data)),
            newSignatures: [],
            currentSpeciality: null,
            currentTask: null,
            currentSignerName: '',
            currentSignerPosition: '',
            keepOtherSignatures: false,
            canUpdateSpeciality: {},
            canChangeManagerStatus: false,
            signaturePad: null,
            selectedStatus: {}
        }
    },
    components: { Multiselect: window.VueMultiselect.default },
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
    },
    methods: {
        timeChanged() { },
        arrayToStringByValue(array, value) {
            let arrayOfValues = []
            array.forEach(item => {
                arrayOfValues.push(item[value]);
            })

            return arrayOfValues.join(',');
        },
        togglePopup(speciality, keepOthers = false, closingWithoutSign = false) {
            if(this.openPopup) {
                if(this.currentTask && keepOthers && closingWithoutSign) {
                    const specialityIndex = this.data.specialities.findIndex(spec => +spec.id === +this.currentSpeciality.id);
                    const taskIndex = this.data.specialities[specialityIndex].tasks.findIndex(task => +task.id === +this.currentTask.id);
                    this.report.specialities[specialityIndex].tasks[taskIndex].appropriate = "0";
                    this.report.specialities[specialityIndex].canUpdateTaskStatuses = !this.report.specialities[specialityIndex].canUpdateTaskStatuses;
                    this.report.updated = true;
                }
                this.openPopup = false;
                this.clearSignaturePad();
                this.currentSpeciality = null;
                this.currentTask = null;
            } else {
                this.keepOtherSignatures = keepOthers;
                this.currentSpeciality = speciality;
                this.currentSignerPosition = this.trans.userPosition;
                this.currentSignerName = this.username;
                this.openPopup = true;
            }
        },
        clearSignaturePad() {
            this.signaturePad.clear();
        },
        addSignature() {
            this.newSignatures.push({
                'el_app_id': this.report.id,
                'el_app_craft_id': this.currentSpeciality.id,
                'name': this.currentSignerName,
                'position': this.currentSignerPosition,
                'image': this.signaturePad.toDataURL(),
                'created_at': Date.now() / 1000,
                'creator_name': this.username
            })

            this.currentSignerName = this.username;
            this.currentSignerPosition = this.trans.userPosition;
            this.clearSignaturePad();
        },
        pushSignatures(keepOthers = false) {
            this.addSignature();

            this.report.specialities.forEach(speciality => {
                if(+speciality.id === +this.currentSpeciality.id) {
                    if(this.keepOtherSignatures) {
                        speciality.signatures = speciality.signatures.concat(this.newSignatures);
                    } else {
                        // speciality.deleted_signatures = speciality.signatures.map(signature => signature.id)
                        speciality.signatures = this.newSignatures;
                    }
                    this.report.updated = true;
                    speciality.canUpdateSignatures = true;
                }
            })
            this.newSignatures = [];
            this.openPopup = false;
        },
        changeTaskStatus(task, speciality) {
            switch (+task.appropriate) {
                case 1:
                    task.appropriate = "0";
                    speciality.deleted_signatures = speciality.signatures.map(signature => signature.id)
                    console.log('s-0', speciality)
                    speciality.signatures = [];
                    break;
                case 0:
                    task.appropriate = "1";
                    break;
            }

            speciality.canUpdateTaskStatuses = this.checkTaskStatusesUpdated(speciality);

            if(this.checkSpecialityAllTasksEnabled(speciality.tasks)) {
                this.currentTask = task;
                this.currentSpeciality = speciality;
                this.togglePopup(speciality, false);
            }
        },
        checkSpecialityAllTasksEnabled(specialityTasks) {
            const result =  specialityTasks.filter(task => {
                return task.appropriate === '0'
            })
            return result.length < 1
        },
        checkReportsAllTasksEnabled() {
            const result = this.report.specialities.filter(speciality => {
                return this.checkSpecialityAllTasksEnabled(speciality.tasks)
            })
            return (result.length === this.report.specialities.length)
        },
        checkTaskStatusesUpdated(speciality) {
            let updated = false;
            const specialityIndex = this.data.specialities.findIndex(spec => +spec.id === +speciality.id);

            speciality.tasks.forEach((task, taskIndex) => {
                if(+task.appropriate !== +this.data.specialities[specialityIndex].tasks[taskIndex].appropriate) {
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
            // this.showLoader = true;
            let url = `/el-approvals/${this.report.id}`;
            console.log("CRAFT TO SEND", speciality)
            qfetch(url, {method: 'PUT', headers: {}, body: speciality})
                .then(response => {
                    console.log('RESPONSE', response)
                    // this.showLoader = false;
                })
        },
        specialityNoteChanged(event, speciality) {
            const specialityIndex = this.report.specialities.findIndex(spec => +spec.id === +speciality.id);

            this.report.specialities[specialityIndex].canUpdateNote = event.target.value !== this.data.specialities[specialityIndex].notice;
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
    },
    mounted() {
        this.signaturePad = new SignaturePad(this.$refs['signaturePad'])
        this.selectedStatus = this.elStatuses.find(status => status.name === this.report.status)
    }
});

