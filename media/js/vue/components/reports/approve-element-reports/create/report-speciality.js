Vue.component('report-speciality', {
    template: `
        <div :key="speciality.id" class="approve-elv-report-item">
            <div class="approve-elv-report-top flex-start">
                <span :class="['approve-elv-report-name', {'not-appropriate': speciality.appropriate === '0', 'appropriate': speciality.appropriate === '1' }]">{{ speciality.name }}</span>
                <span class="approve-elv-report-status flex-start">
<!--                    <span class="approve-elv-report-status-title">{{ trans.status }}</span>-->
<!--                    <span class="approve-elv-report-status-value">{{ +speciality.appropriate ? trans.appropriate : trans.not_appropriate }}</span>-->
                    <div class="filter-item-checkbox">
                        <span class="check-task">
                            <input 
                                type="checkbox" 
                                class="el-app-checkbox" 
                                :class="{'disabled': false}" 
                                :checked="+speciality.primarySupervision" 
                                @change="changePrimarySupervision(speciality)" 
                                :disabled="false"
                            >
                            <span class="checkboxImg" :class="{'disabled': false}"></span>
                        </span>
                        <div class="approve-elv-report-status-title flex-between">
                            {{ trans.primary_supervision }}
                        </div>
                    </div>
                </span>
                <span 
                    class="approve-elv-report-view" 
                    v-if="speciality.qualityControl"
                >
                    <a>
                        {{ trans.view_qc }}
                    </a>
                </span>
            </div>
            <div class="approve-elv-report-sign">
                    <template v-if="speciality?.signatures?.length">
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
<!--                        <div class="approve-elv-properties flex-start">-->
<!--                            <div class="approve-elv-property flex-start">-->
<!--                                <span class="approve-elv-properties-name">{{ trans.updated_by }}</span>-->
<!--                                <span class="approve-elv-property-value">{{ speciality.updatorName }}</span>-->
<!--                            </div>-->
<!--                            <div class="approve-elv-property flex-start">-->
<!--                                <span class="approve-elv-properties-name">{{ trans.date }}</span>-->
<!--                                <span class="approve-elv-property-value">{{ convertTimestampToDate(speciality.updatedAt) }}</span>-->
<!--                            </div>-->
<!--                        </div>-->
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
                            :disabled="false"
                        >{{ speciality.notice }}</textarea>
                    </div>
                </div>
            </div>
            <div class="approve-elv-report-tasks" v-if="speciality?.tasks?.length">
                <div class="report_tasks">
                    <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">
                        {{ trans.tasks }}
                        <span>*</span>
                    </h4>
                    <div class="report_tasks_wraper" >
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
                     :class="['report-button', { 'labtest-disabled': false }]" 
                     @click="updateReport(speciality)"
                 >              
                     {{ trans.update }}
                 </button>
                 <button 
                    v-if="false" 
                    @click="togglePopup(speciality,true, false, 'button')" 
                    :class="['report-button', {'labtest-disabled': false}]"
                 >
                    {{ trans.add_signature }}
                 </button>
            </div>
        </div>
    `,
    props: {
        userRole: {required: true},
        siteUrl: {required: true},
        username: {required: true},
        taskStatuses: {required: true},
        translations: {required: true},
        userProfession: {required: true},
        projectId: {required: true},
        initialSpeciality: {required: true}
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            speciality: JSON.parse(JSON.stringify(this.initialSpeciality))
        }
    },
    methods: {
        specialityNoteChanged(event, speciality) {

        },
        changeTaskStatus(task, speciality) {

        },
        updateReport(speciality) {

        },
        togglePopup() {

        }
    },
    created() {
        console.log('this.initialSpeciality',this.initialSpeciality)
    },
    mounted() {
        this.speciality.appropriate = '0';
        this.speciality.notice = '';

    }
});

