Vue.component('report-item', {
    template: `
    <section class='q4b-approve-el approve-el-view'>
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
                        :option-height="104" 
                        placeholder="Waiting"
                        :disabled="options.length < 1" 
                        :options="options" 
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
                <template v-for="speciality in report.specialities">
                    <div class="approve-elv-report-item">
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
                                    <textarea cols="30" rows="10" name="delivery_cert">{{ speciality.notice }}</textarea>
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
                                <div class="report-buttons-update flex-start">
                                    <button v-if="checkAllTasksEnabled(speciality.tasks)" @click="togglePopup(speciality,true)" class="report-button">Add signature</button>
                                </div>
                                <div class="report-buttons-update flex-start">
                                    <button class="report-button" @click="updateReport(speciality)">{{ trans.update }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <!--report-item-->
<!--                <div class="approve-elv-report-item">-->
<!--                    <div class="approve-elv-report-top flex-start">-->
<!--                        <span class="approve-elv-report-name not-appropriate">בטון יצוק באתר </span>-->
<!--                        <span class="approve-elv-report-status flex-start">-->
<!--                            <span class="approve-elv-report-status-title">Status</span>-->
<!--                            <span class="approve-elv-report-status-value">Not appropriate</span>-->
<!--                        </span>-->
<!--                        <span class="approve-elv-report-view"><a href=""> View a QC</a> </span>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-sign">-->
<!--                        <div class="approve-elv-properties flex-start ">-->
<!--                            <div class=" approve-elv-property flex-start">-->
<!--                                <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                <span class="approve-elv-property-value"> יורם</span>-->
<!--                            </div>-->
<!--                            <div class=" approve-elv-property flex-start">-->
<!--                                <span class="approve-elv-properties-name ">Date</span>-->
<!--                                <span class="approve-elv-property-value"> 10/05/2021 </span>-->
<!--                            </div>-->


<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="ltest_info_certificate ">-->
<!--                        <div class="ltest_info_certificate_title">Notes</div>-->
<!--                        <div class="ltest_info_certificate_area">-->
<!--                            <div class="labtest_edit_textarea">-->
<!--                                <textarea cols="30" rows="10" name="delivery_cert">-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--                            </textarea>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-tasks">-->
<!--                        <div class="report_tasks">-->
<!--                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks-->
<!--                                <span>*</span>-->
<!--                            </h4>-->
<!--                            <div class="report_tasks_wraper">-->
<!--                                <div class="report_tasks_item not-appropriate">-->
<!--                                    <div class="report_task_title">Task 9518</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית-->
<!--                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item not-appropriate ">-->
<!--                                    <div class="report_task_title">Task 9524</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9536</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי-->
<!--                                                דיירים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item not-appropriate">-->
<!--                                    <div class="report_task_title">Task 9542</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9565</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item not-appropriate">-->
<!--                                    <div class="report_task_title">Task 9566</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9567</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /-->
<!--                                                תקן </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                </div>-->
                <!--report-item-->
<!--                <div class="approve-elv-report-item">-->
<!--                    <div class="approve-elv-report-top flex-start">-->
<!--                        <span class="approve-elv-report-name appropriate">עבודות אלומיניום , קירות מסך-->
<!--                            וחיפויים - אלוקובונד</span>-->
<!--                        <span class="approve-elv-report-status flex-start">-->
<!--                            <span class="approve-elv-report-status-title">Status</span>-->
<!--                            <span class="approve-elv-report-status-value"> Appropriate</span>-->
<!--                        </span>-->
<!--                        <span class="approve-elv-report-view"><a href=""> View a QC</a> </span>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-sign-wraper">-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->


<!--                    <div class="ltest_info_certificate ">-->
<!--                        <div class="ltest_info_certificate_title">Notes</div>-->
<!--                        <div class="ltest_info_certificate_area">-->
<!--                            <div class="labtest_edit_textarea">-->
<!--                                <textarea cols="30" rows="10" name="delivery_cert">-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--                            </textarea>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-tasks">-->
<!--                        <div class="report_tasks">-->
<!--                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks-->
<!--                                <span>*</span>-->
<!--                            </h4>-->
<!--                            <div class="report_tasks_wraper">-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9518</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית-->
<!--                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate ">-->
<!--                                    <div class="report_task_title">Task 9524</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9536</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי-->
<!--                                                דיירים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9542</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9565</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9566</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9567</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /-->
<!--                                                תקן </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                </div>-->
                <!--report-item-->
<!--                <div class="approve-elv-report-item">-->
<!--                    <div class="approve-elv-report-top flex-start">-->
<!--                        <span class="approve-elv-report-name appropriate">עבודות אלומיניום </span>-->
<!--                        <span class="approve-elv-report-status flex-start">-->
<!--                            <span class="approve-elv-report-status-title">Status</span>-->
<!--                            <span class="approve-elv-report-status-value"> Appropriate</span>-->
<!--                        </span>-->
<!--                        <span class="approve-elv-report-view"><a href="">{{ trans.view_qc }}</a></span>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-sign-wraper">-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="approve-elv-report-sign">-->
<!--                            <div class="approve-elv-properties flex-start ">-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Updated by</span>-->
<!--                                    <span class="approve-elv-property-value"> יורם</span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Date</span>-->
<!--                                    <span class="approve-elv-property-value">10/05/2021 </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Approved by</span>-->
<!--                                    <span class="approve-elv-property-value">דניאל </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start">-->
<!--                                    <span class="approve-elv-properties-name ">Position</span>-->
<!--                                    <span class="approve-elv-property-value"> מנהל צוות </span>-->
<!--                                </div>-->
<!--                                <div class=" approve-elv-property flex-start sign-image">-->
<!--                                    <img src="./img/signature.png">-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->


<!--                    <div class="ltest_info_certificate ">-->
<!--                        <div class="ltest_info_certificate_title">Notes</div>-->
<!--                        <div class="ltest_info_certificate_area">-->
<!--                            <div class="labtest_edit_textarea">-->
<!--                                <textarea cols="30" rows="10" name="delivery_cert">-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--Lorem Ipsum has been the industry's standard -->
<!--dummy text ever since the 1500s, when an unknown -->
<!--printer took a galley of type and scrambled it to-->
<!--make a type specimen book.-->
<!--                            </textarea>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="approve-elv-report-tasks">-->
<!--                        <div class="report_tasks">-->
<!--                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks-->
<!--                                <span>*</span>-->
<!--                            </h4>-->
<!--                            <div class="report_tasks_wraper">-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9518</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית-->
<!--                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate ">-->
<!--                                    <div class="report_task_title">Task 9524</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9536</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי-->
<!--                                                דיירים </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9542</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9565</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9566</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="report_tasks_item appropriate">-->
<!--                                    <div class="report_task_title">Task 9567</div>-->
<!--                                    <div class="report_task_desc_wrap">-->
<!--                                        <div class="report_task_descripticon">-->
<!--                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /-->
<!--                                                תקן </div>-->
<!--                                        </div>-->
<!--                                        <div class="report_task_status  "></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
        <div class="modul-popup-wrap approve-elv-popup" v-show="openPopup">
            <div class="modul-popup">
                <div class="modul-popup-top">
                    <span class="modul-popup-headline">Please sign[*]</span>
                    <span class="modul-popup-close" @click="togglePopup"><i class="q4bikon-close"></i></span>
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
        project: {required: true},
        company: {required: true},
        data: {required: true},
        translations: {required: true},
        filters: {required: true},
        username: {required: true}
    },
    data() {
        return {
            openPopup: false,
            trans: JSON.parse(this.translations),
            time: [],
            report: this.data,
            newSignatures: [],
            currentSpeciality: null,
            currentSignerName: '',
            currentSignerPosition: '',
            keepOtherSignatures: false,
            options: [
                {"name": "waiting"},
                {"name": "approved"}
            ],
            signaturePad: null

        }
    },
    components: { Multiselect: window.VueMultiselect.default },
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
        togglePopup(speciality, keepOthers = false) {
            if(this.openPopup) {
                this.openPopup = false;
                this.clearSignaturePad();
                this.currentSpeciality = null;
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
                        speciality.signatures = this.newSignatures;
                    }
                }
            })
            this.newSignatures = [];
            this.openPopup = false;
        },
        changeTaskStatus(task, speciality) {
            switch (+task.appropriate) {
                case 1:
                    task.appropriate = "0";
                    break;
                case 0:
                    task.appropriate = "1";
                    break;
            }
            if(this.checkAllTasksEnabled(speciality.tasks)) {
                this.togglePopup(speciality, false);
            }
        },
        checkAllTasksEnabled(specialityTasks) {
            const result =  specialityTasks.filter(task => {
                return task.appropriate === '0'
            })
            return result.length < 1
        },
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1).length > 1) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        updateReport(speciality) {
            console.log('SPECIALITY TO UPDATE', speciality)
        }
    },
    mounted() {
        this.signaturePad = new SignaturePad(this.$refs['signaturePad'])
    }
});

