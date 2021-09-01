Vue.component('ticket-item', {
    template: `
        <div class="labtest_ticket_info">
                <div class="labtest_ticket_info_top">
                    <div class="labtest_ticket_info_title">{{ trans.ticket }}</div>
                    <div class="labtest_ticket_info_add">{{ trans.add_ticket }}</div>
                </div>

                <div class=" ltest_info_wraper ">

                    <div class="ltest_info_standard">
                        <div class="ltest_info_standard_title">{{ trans.description }}</div>
                        <div class="ltest_info_standard_area">
                            <textarea cols="30" rows="10" v-model="item.description"></textarea>
                        </div>
                    </div>
                    <div class="ltest_info_select">
                        <input type="text" v-model="item.number" :placeholder="trans.lab_certificate">
                    </div>
                    <div class="ltest_info_select">
                        <input type="text" v-model="item.freshStrength" :placeholder="trans.fresh_concrete_strength">
                    </div>
                    <div class="ltest_info_select">
                        <input type="text" v-model="item.rollStrength" :placeholder="trans.roll_strength">
                    </div>
                </div>
                <div class=" ltest_info_wraper notes">
                    <div class="ltest_info_certificate">
                        <div class="ltest_info_certificate_title">{{ trans.notes }}</div>
                        <div class="ltest_info_certificate_area">
                            <textarea cols="30" rows="10" v-model="item.notes"></textarea>
                        </div>
                    </div>
                    <div class="ltest_info_plan">
                        <div class="ltest_info_plan_top">
                            <div class="ltest_info_plan_select">
                                <div class="ltest_info_plan_select_title">{{ trans.lab_cert }}</div>
                            </div>
                        </div>
                        <div class="ltest_info_plan_main">
                            <div class="ltest_info_plan_properties_wrap">                                
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.ticket_upload_date }}</div>
                                </div>
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.status }}</div>
                                </div>
                                <div class="ltest_info_plan_property">
                                    <div class="ltest_info_plan_property_name">{{ trans.description }}</div>
                                </div>
                                                             
                            </div>
                            <template v-for="ticket in tickets">
                                <div class="ltest_info_plan_properties_wrap cursor-pointer">
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ getDate(ticket.createdAt) }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ ticket.status }}</div>
                                    </div>
                                    <div class="ltest_info_plan_property">
                                        <div class="ltest_info_plan_property_value">{{ ticket.description }}</div>
                                    </div>
                                </div>
                            </template>  
                        </div>                        
                    </div>
                </div>
                <div class="labtest_edit_bottom">
                    <div class="labtest_attachment">
                        <div class="ltest_attachment_title">{{ trans.attached_files }}</div>
                        <div class="ltest_attachment_wraper">
                            <div class="ltest_attachment_item">
                                <div class="ltest_attachment_icon"><i class="icon q4bikon-file"></i></div>
                                <div class="ltest_attachment_right">
                                    <div class="ltest_attachment_name">001.jpg </div>
                                    <div class="ltest_attachment_line"></div>
                                    <div class="ltest_attachment_Uploaded">{{ trans.uploaded }}</div>
                                </div>
                            </div>                          
                        </div>
                    </div>
                    <div class="labtest_editor">
                        <div class="labtest_editor_item">
                            <div class="labtest_editor_by">{{ trans.updated_by }}</div>
                            <div class="labtest_editor_name_sec">
                                <span> Yoram Cohen</span>
                                at
                                <span class="labtest_editor_name_sec">01/10/2020</span>
                            </div>
                        </div>
                        <div class="labtest_editor_item">
                            <div class="labtest_editor_by">{{ trans.created_by }}</div>
                            <div class="labtest_editor_name_sec">
                                <span> David Aramian</span>
                                at
                                <span class="labtest_editor_name_sec">01/10/2020</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
`,
    props: {
        projectId: {required: true},
        trans: {required: false},
        item: {required: true},
        tickets: {required: true},
    },

    data() {
        return {
            project: null,
            items: [],
            showLoader:false,
            search: '',
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        enabledSave() {
            let valid = true;
            this.items.forEach(item => {
                this.items.forEach(i => {
                    if (!i.name) valid = false
                })
            })
            return valid;
        },
        showSave() {
            let show = false;
            this.items.forEach(item => {
                this.items.forEach(i => {
                    if (!i.id) show = true
                })
            })
            return show;
        }
    },
    mounted() {
        if (this.projectId) {
            this.getProject(this.projectId);
        }
    },
    watch: {
        projectId(val) {
            if(val) {
                this.getProject(val);
                this.getElements();
            }
        },
        project(val) {
            if(val) {
                this.getElements();
            }
        }
    },
    methods: {
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        save() {
            let newElements = [];
            this.items.forEach(i => {
                if (!i.id) {
                    newElements.push(i)
                }
            })
            if (newElements.length) {
                this.sendSaveRequest(newElements);
            }
            console.log(newElements);
        },
        addItem() {
            let newItem = { name: '' };
            this.items.unshift(newItem)
        },
        toggleMore(data) {
            let {item, index} = data;
            let obj = {...item};
            obj.more = !obj.more;
            item = {...obj};
            if (obj.more) {
                var items = this.items.map((item) => {
                    item.more = false
                    return item;
                });
            } else {
                var items = [...this.items];
            }
            items[index] = obj;
            this.items = [...items];
        },
        deleteItem(data) {
            let { index } = data;
            let items = [... this.items];
            let obj = items[index];
            if (obj.id) {
                this.sendDeleteRequest(obj.id);
            }
            items.splice(index,1);
            this.items = [...items];
        },
        sendSaveRequest(newElements) {
            let url = `/projects/${this.project.id}/labtests/elements`;

            qfetch(url, {method: 'POST', headers: {}, body: newElements})
                .then(response => {
                    this.search = '';
                    this.items = response.items;
                })
        },
        sendDeleteRequest(id) {
            let url = `/projects/${this.project.id}/labtests/elements/${id}`;
            qfetch(url, {method: 'DELETE', headers: {}});
        },
        getProject(id) {
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                })
        },
        getElements(){
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/elements`;
            let param = encodeURIComponent('?search='+ this.search)
            url +=  param;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let items = response.items;
                    items.forEach(i => i.more = false)
                    this.items = items;
                    this.showLoader = false;
                })
        },
    },

});

