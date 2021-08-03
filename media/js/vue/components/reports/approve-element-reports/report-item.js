Vue.component('report-item', {
    template: `
    <section class='q4b-approve-el approve-el-view'>
        <div class="approve-elv-top-wrap flex-between">
            <div class="approve-elv-top-left flex-start">
                <div class="approve-elv-top-item">
                    <a class="back-to-filter">
                        <i @click="$emit('toReportsList')" class="q4bikon-arrow_back2"></i>
                    </a>
                    <span class="approve-elv-top-item-name">Check #</span>
                    <span class="approve-elv-top-item-value">{{ data.id }}</span>

                </div>
                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">Element</span>
                    <span class="approve-elv-top-item-value">{{ data.element_name }}</span>

                </div>

            </div>
            <div class="approve-elv-top-right flex-end">

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">Created by</span>
                    <span class="approve-elv-top-item-value">{{ data.creator }} ({{ data.check_date }})</span>

                </div>

                <div class="approve-elv-top-item">
                    <span class="approve-elv-top-item-name">Status</span>
                    <span class="approve-elv-top-item-value">{{ data.status }}</span>
                </div>

            </div>
        </div>
        <div class="approve-elv-filter flex-end">
            <div class="filter-item">
                <div class="filter-item-label">Manager Status</div>
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
                <span class="approve-elv-properties-name ">Company</span>
                <span class="approve-elv-property-value">{{ filters.company.name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">Project</span>
                <span class="approve-elv-property-value">{{ filters.project.name }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">Structure</span>
                <span class="approve-elv-property-value">{{ arrayToStringByValue(filters.structures,'name') }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">Floor/Level</span>
                <span class="approve-elv-property-value">{{ arrayToStringByValue(filters.floors,'number') }}</span>
            </div>
            <div class=" approve-elv-property flex-start">
                <span class="approve-elv-properties-name ">Place</span>
                <span class="approve-elv-property-value">{{ arrayToStringByValue(filters.places,'name') }}</span>
            </div>

        </div>

        <div class="report-buttons">
            <div class="report-buttons-wraper ">
                <a class="report-button  " href=""><i class="q4bikon-print1"></i>Print</a>
            </div>
        </div>

        <div class="approve-elv-reports">
            <div class="approve-elv-reports-top flex-between">
                <div class="approve-elv-reports-top-headline">Speciality List</div>
                <div class="approve-elv-reports-delete-all"><button class="delete-all">Delete</button>
                </div>
            </div>
            <div class="approve-elv-reports-wraper">
                <!--report-item-->
                <div class="approve-elv-report-item">
                    <div class="approve-elv-report-top flex-start">
                        <span class="approve-elv-report-name not-appropriate">בטון יצוק באתר </span>
                        <span class="approve-elv-report-status flex-start">
                            <span class="approve-elv-report-status-title">Status</span>
                            <span class="approve-elv-report-status-value">Not appropriate</span>
                        </span>
                        <span class="approve-elv-report-view"><a href=""> View a QC</a> </span>
                    </div>
                    <div class="approve-elv-report-sign">
                        <div class="approve-elv-properties flex-start ">
                            <div class=" approve-elv-property flex-start">
                                <span class="approve-elv-properties-name ">Updated by</span>
                                <span class="approve-elv-property-value"> יורם</span>
                            </div>
                            <div class=" approve-elv-property flex-start">
                                <span class="approve-elv-properties-name ">Date</span>
                                <span class="approve-elv-property-value"> 10/05/2021 </span>
                            </div>


                        </div>
                    </div>
                    <div class="ltest_info_certificate ">
                        <div class="ltest_info_certificate_title">Notes</div>
                        <div class="ltest_info_certificate_area">
                            <div class="labtest_edit_textarea">
                                <textarea cols="30" rows="10" name="delivery_cert">
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="approve-elv-report-tasks">
                        <div class="report_tasks">
                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks
                                <span>*</span>
                            </h4>
                            <div class="report_tasks_wraper">
                                <div class="report_tasks_item not-appropriate">
                                    <div class="report_task_title">Task 9518</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית
                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item not-appropriate ">
                                    <div class="report_task_title">Task 9524</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9536</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי
                                                דיירים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item not-appropriate">
                                    <div class="report_task_title">Task 9542</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9565</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון
                                            </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item not-appropriate">
                                    <div class="report_task_title">Task 9566</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9567</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /
                                                תקן </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <!--report-item-->
                <div class="approve-elv-report-item">

                    <div class="approve-elv-report-top flex-start">
                        <span class="approve-elv-report-name appropriate">עבודות אלומיניום , קירות מסך
                            וחיפויים - אלוקובונד</span>
                        <span class="approve-elv-report-status flex-start">
                            <span class="approve-elv-report-status-title">Status</span>
                            <span class="approve-elv-report-status-value"> Appropriate</span>
                        </span>
                        <span class="approve-elv-report-view"><a href=""> View a QC</a> </span>
                    </div>
                    <div class="approve-elv-report-sign-wraper">
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="ltest_info_certificate ">
                        <div class="ltest_info_certificate_title">Notes</div>
                        <div class="ltest_info_certificate_area">
                            <div class="labtest_edit_textarea">
                                <textarea cols="30" rows="10" name="delivery_cert">
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="approve-elv-report-tasks">
                        <div class="report_tasks">
                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks
                                <span>*</span>
                            </h4>
                            <div class="report_tasks_wraper">
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9518</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית
                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate ">
                                    <div class="report_task_title">Task 9524</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9536</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי
                                                דיירים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9542</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9565</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון
                                            </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9566</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9567</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /
                                                תקן </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <!--report-item-->
                <div class="approve-elv-report-item">
                    <div class="approve-elv-report-top flex-start">
                        <span class="approve-elv-report-name appropriate">עבודות אלומיניום </span>
                        <span class="approve-elv-report-status flex-start">
                            <span class="approve-elv-report-status-title">Status</span>
                            <span class="approve-elv-report-status-value"> Appropriate</span>
                        </span>
                        <span class="approve-elv-report-view"><a href=""> View a QC</a> </span>
                    </div>
                    <div class="approve-elv-report-sign-wraper">
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                        <div class="approve-elv-report-sign">
                            <div class="approve-elv-properties flex-start ">
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Updated by</span>
                                    <span class="approve-elv-property-value"> יורם</span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Date</span>
                                    <span class="approve-elv-property-value">10/05/2021 </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Approved by</span>
                                    <span class="approve-elv-property-value">דניאל </span>
                                </div>
                                <div class=" approve-elv-property flex-start">
                                    <span class="approve-elv-properties-name ">Position</span>
                                    <span class="approve-elv-property-value"> מנהל צוות </span>
                                </div>
                                <div class=" approve-elv-property flex-start sign-image">
                                    <img src="./img/signature.png">
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="ltest_info_certificate ">
                        <div class="ltest_info_certificate_title">Notes</div>
                        <div class="ltest_info_certificate_area">
                            <div class="labtest_edit_textarea">
                                <textarea cols="30" rows="10" name="delivery_cert">
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
Lorem Ipsum has been the industry's standard 
dummy text ever since the 1500s, when an unknown 
printer took a galley of type and scrambled it to
make a type specimen book.
                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="approve-elv-report-tasks">
                        <div class="report_tasks">
                            <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);">Tasks
                                <span>*</span>
                            </h4>
                            <div class="report_tasks_wraper">
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9518</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מיקום נק' מים בחדרים רטובים/ מטבח אל מול תכנית
                                                יועץ/ שינוי דיירים/ חברת מטבחים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate ">
                                    <div class="report_task_title">Task 9524</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> התאמת שלביות ביצוע אל מול מפרט/ פתיחת מלאכה </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9536</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת מידות דירה/ חללים אל מול תוכנית אדריכלות/שינוי
                                                דיירים </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9542</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת הארקות לפי תכנית יועץ חשמל </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9565</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת סימון תוואי דיפון + מרכז קידוח+ אבטחת סימון
                                            </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9566</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת אנכיות קידוח כלונס/ קירות סלרי </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                                <div class="report_tasks_item appropriate">
                                    <div class="report_task_title">Task 9567</div>
                                    <div class="report_task_desc_wrap">
                                        <div class="report_task_descripticon">
                                            <div> בדיקת קיבוע הקונסטרוקציה ע"פ תכנית ומפרט מאושר יועץ /
                                                תקן </div>
                                        </div>
                                        <div class="report_task_status  "></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="report-buttons-update flex-center ">
            <button class="report-button" @click="openPopup=true" href="">Update</button>
        </div>
        <div class="modul-popup-wrap approve-elv-popup" v-if="openPopup">
            <div class="modul-popup">
                <div class="modul-popup-top">
                    <span class="modul-popup-headline">Please sign</span>
                    <span class="modul-popup-close" @click="openPopup=false"><i class="q4bikon-close"></i></span>
                </div>
                <div class="modul-popup-main">
                    <div class="approve-elv-popup-inputs flex-between">
                        <div class="filter-item">
                            <div class="filter-item-label">Signer Name</div>
                            <input type="text" name="signer_name" id="" value="Gabriel Belmont">
                        </div>
                        <div class="filter-item">
                            <div class="filter-item-label">Position</div>
                            <input type="text" name="signer_name" id="" value="Director">
                        </div>
                    </div>

                    <div class="approve-elv-popup-sign">
                        <span class="clear-sign">Clear sign</span>
                        <div class="approve-elv-popup-sign-line"></div>
                    </div>
                </div>

                <div class="modul-popup-btns">
                    <button class="modul-popup-Confirm">Sign</button>
                    <button class="modul-popup-Cancel">Additional Signature</button>
                </div>

            </div>
        </div>
    </section>
    `,
    props: {
        data: {required: true},
        translations: {required: true},
        filters: {required: true}
    },
    data() {
        return {
            openPopup: false,
            trans: JSON.parse(this.translations),
            value: [],
            time: [],
            options: [
                {
                    "name": "Waiting",
                    "city_ascii": "San Martin",
                    "lat": -33.06998533,
                    "lng": -68.49001612,
                    "pop": 99974,
                    "country": "Argentina",
                    "iso2": "AR",
                    "iso3": "ARG",
                    "province": "Mendoza",
                    "timezone": "America/Argentina/Mendoza"
                },
                {
                    "name": "Normal",
                    "city_ascii": "San Nicolas",
                    "lat": -33.33002114,
                    "lng": -60.24000289,
                    "pop": 117123.5,
                    "country": "Argentina",
                    "iso2": "AR",
                    "iso3": "ARG",
                    "province": "Ciudad de Buenos Aires",
                    "timezone": "America/Argentina/Buenos_Aires"
                },
                {
                    "name": "Invalid",
                    "city_ascii": "San Francisco",
                    "lat": -31.43003375,
                    "lng": -62.08996749,
                    "pop": 43231,
                    "country": "Argentina",
                    "iso2": "AR",
                    "iso3": "ARG",
                    "province": "Córdoba",
                    "timezone": "America/Argentina/Cordoba"
                }
            ]
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
        }
    }
});

