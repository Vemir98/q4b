Vue.component('sendDeliveryReports', {
    template: `
        <div class="new-styles">
            
              <div class="two-flex">
                <div class="space-betwen-column">
                    <div>
                        <label for="" class="multiselect-label">
                            {{sendToTxt}}        
                        </label>
                        <multiselect 
                          v-model="value" 
                          tag-placeholder="" :placeholder="typeEmailTxt" 
                          :options="opts" :taggable="true" 
                          @tag="addTag" tag-position="bottom"
                          @select="selectedEmail"
                          >                     
                        </multiselect>
                        <div class="multiselect-selected-items overflow-styled">
                            <span v-for="tag in selectedTags" class="tagged-email">
                            <span class="tagged-email-text">{{tag}}</span>
                            <span class="tagged-email-remove-btn" @click="remove(tag)"><img src="/media/img/close.svg" alt="close-modal"></span>
                            </span>
                        </div>   
                    </div>                                         
                    <div>
                        <p><label class="multiselect-label margin-0">{{messageTxt}}</label></p>
                        <p><textarea name="message" class="modal-details-textarea" v-model="message" :placeholder="yourTextTxt"></textarea></p>  
                    </div>
              </div>
            <div class="modal-table-delivery">
<!--                <p class="title-delivery-table">{{chooseTxt}}</p>-->
                <p class="title-delivery-table">{{selectExistingTxt}}</p>
                <div class="mobile-view">
                    <div class="table-delivery-mobile">
                        <div class="mob-tr" v-for="user in projectUsers">
                            <div class="mob-th">
                                <div class="input inp_ch">
                                    <input type="checkbox" v-model="selectedTags" :value="user.email" :id="user.email">
                                    <label :for="user.email" ></label>
                                </div>
                                <label :for="user.email" class="mob-th-title">Select</label>
                            </div>
                            <div class="mob-td">
                                <div class="mob-td-titile">{{nameTxt}}</div>
                                <div class="mob-td-desc">{{user.name}}</div>
                            </div>
                            <div class="mob-td">
                                <div class="mob-td-titile">{{professionTxt}}</div>
                                <div class="mob-td-desc">{{user.profession}}</div>
                            </div>
                            <div class="mob-td">
                                <div class="mob-td-titile">{{emailTxt}}</div>
                                <div class="mob-td-desc">{{user.email}}</div>
                            </div>
                        </div>              
                    </div>
                </div>

                <div class="table-overflow overflow-styled web-view">
                    <table class="project-users">
                    <thead>
                        <th></th>
                        <th>{{nameTxt}}</th>
                        <th>{{professionTxt}}</th>
                        <th>{{emailTxt}}</th>
                    </thead>
                    <tbody>
                        <tr v-for="user in projectUsers">
                            <td>
                            <div class="input inp_ch">
                                <input type="checkbox" v-model="selectedTags" :value="user.email" :id="user.email">
                                <label :for="user.email"></label>
                            </div>
                            </td>
                            <td>{{user.name}}</td>
                            <td>{{user.profession}}</td>
                            <td>{{user.email}}</td>
                        </tr>
                    </tbody>
                   
                </table>
                </div>
            </div>
        </div>
                
        </div>
    `,
    props: {
        confirmed: {default: false},
        siteUrl: {required: true},
        chooseTxt: {required: true},
        typeEmailTxt: {required: true},
        nameTxt: {required: true},
        professionTxt: {required: true},
        emailTxt: {required: true},
        messageTxt: {required: true},
        sendToTxt: {required: true},
        selectExistingTxt: {required: true},
        mailingUrl: {required: true},
        yourTextTxt: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },
    data() {
        return {
            projectId: null,
            value: [],
            selectedTags: [],
            options: [],
            chooseUsersActive: false,
            projectUsers: [],
            reports: [],
            message: ""
        };
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
        window.eventBus.$on('deliveryProjectSelected', projectId => {
            this.projectId = projectId;
            this.selectedTags = this.reports = [];
            this.message = "";
            axios.get(this.siteUrl + 'entities/email_users_list/' + projectId)
                .then(response => {
                    this.options = response.data.autocomplete;
                    this.projectUsers = response.data.projectUsers;
                });
        });
        window.eventBus.$on('deliveryReportChecked', reports => {
            this.reports = reports;
        })
    },
    methods: {
        isValidEmail: function(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        addTag: function (newTag) {
            if( ! this.isValidEmail(newTag)) return;
            this.selectedTags.push(newTag)
            setTimeout(() => {
                this.value = [];
            },50)
        },
        selectedEmail: function (option) {
            this.selectedTags.push(option)
            setTimeout(() => {
                this.value = [];
            },50)

        },
        remove: function (tag) {
            this.selectedTags = this.selectedTags.filter(x => x !== tag)
        }
    },
    computed: {
        opts: function () {
            return this.options.filter(x => !this.selectedTags.includes(x))
        },
        usersStyle: function () {
            return this.chooseUsersActive ? 'left: 0' : '';
        }
    },
    mounted() {
    },
    watch: {
        confirmed: function (val) {
            if(!val || this.selectedTags.length < 1 || this.reports.length < 1) {
                return;
            }
            axios.post(this.mailingUrl,JSON.stringify({'reports' : this.reports, 'emails': this.selectedTags, 'message': this.message, 'project': this.projectId}))
                .then(response => {

                });
        }
    }
});
