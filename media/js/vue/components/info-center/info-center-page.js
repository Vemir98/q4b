Vue.component('info-center-page', {
    template: `
        <div id="info-center-content" class="new-styles">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="page-title-sec flex-start">
                <div class="page-title"> {{ trans.info_center }} </div>
            </div>
            <div class="info-center-content">
                <div class="info-center-filters">
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.companies }}</div>
                            <multiselect 
                                v-model="selectedCompany"
                                :option-height="104" 
                                :placeholder="trans.select_company" 
                                :disabled="(companies.length < 1)" 
                                :options="companies" 
                                track-by="id" 
                                label="name" 
                                :searchable="true" 
                                :allow-empty="false" 
                                :show-labels="false"
                            >
                                <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
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
                <div class="info-center-filters-results">
                    <div id="info-center-send-content" v-if="projects.length">
                        <info-center-message
                            :projects="projects"
                            :message="message"
                            :translations="translations"
                            @onMessageChanged="message = $event"
                            @onProjectSelected="selectedProjects = $event"
                            @sendMessage="sendMessage"
                        />
                        <div class="info-center-send-buttons">
                            <button 
                                class="info-center-send-button" 
                                @click="sendMessage"
                                :class="{'labtest-disabled': (!canCreateMessage)}"
                            >
                                {{ trans.send }}
                            </button>
                        </div>
                    </div>
                    <div id="info-center-history-content" v-if="projects.length">
                        <info-center-history
                            :projects="projects"
                            :items="messages"
                            :action="action"
                            :translations="translations"
                            @projectChanged="filteredProjectChanged"
                            @actionSelected="actionSelected"
                            @showMessageHistoryModal="showMessageHistoryModal"
                            @messageEdited="editMessage"
                            @messageResented="resendMessage"
                        />
                        <message-history-modal
                            v-if="messageHistoryModalDisplay"
                            :translations="translations"
                            :messageHistories="getMessageHistoriesByMessage"
                            :selectedHistory="messageHistoryModalSelectedHistory"
                            @onClose="messageHistoryModalDisplay = false"
                         />
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        siteUrl: {required: true},
        translations: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    computed: {
        canCreateMessage() {
            return (this.selectedCompany && this.selectedProjects.length && this.message.trim().length)
        },
        getMessageHistoriesByMessage() {
            if(this.messageHistoryModalSelectedHistory) {
                const messageId = this.messageHistoryModalSelectedHistory.pmId;

                return this.messages
                    .filter(project => {
                        return +project.id === +this.filteredProject.id
                    })[0]['messages']
                    .filter(message => +message.id === +messageId)[0].history;
            }
        }
    },
    data() {
        return {
            showLoader: false,
            companies: [],
            projects: [],
            messages: [],
            filteredMessages: [],
            filteredProject: null,
            message: '',
            trans: JSON.parse(this.translations),
            selectedHistory: null,
            selectedHistoryText: '',
            selectedProjects: [],



            messagesProjects: [],
            selectedCompany: null,
            messageHistoryModalDisplay: false,
            messageDeletePopupDisplay: false,
            messageHistoryModalSelectedHistory: null,
            action: 'create'
        }
    },
    watch: {
        selectedCompany(company) {
            this.projects = [];
            this.selectedProjects = [];
            this.messages = [];
            this.message = '';

            if(company.projects) {
                this.projects = this.projects.concat(Object.values(company.projects))
                this.filteredProject = this.projects[0];
                this.getProjectsMessagesAPI();
            }
        },
    },
    methods: {
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        getCompaniesAPI(){
            this.showLoader = true;
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                    this.showLoader = false;
                })
        },
        toggleSelectAll(selected, list) {
            if (list.length) {
                if (this[selected].length < this[list].length) {
                    this[selected] = this[list].map((i) => {
                        i.checked = true;
                        return i
                    });
                } else {
                    this[selected] = this[list].map((i) => {
                        i.checked = false;
                        return i
                    });
                    this[selected] = []
                }
            }
        },
        onSelect(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = false;
        },
        actionSelected(data) {
            switch (data.action) {
                case 'create':
                    this.action = 'create';
                    this.selectedHistory = null;
                    break;
                case 'edit':
                    this.action = 'edit';
                    this.selectedHistory = data.history;
                    break;
                case 'resend':
                    this.action = 'resend';
                    this.selectedHistory = data.history;
                    // this.resendMessageAPI();
                    break;
                case 'delete':
                    this.action = 'delete';
                    this.selectedHistory = data.history;
                    this.deleteMessageHistoryAPI();
                    break;
            }
        },
        sendMessage() {
            switch (this.action) {
                case 'create':
                    if(!this.canCreateMessage) return false;
                    this.createMessageAPI();
                    break;
                case 'edit':
                    this.editMessageAPI();
                    break;
            }
        },
        editMessage(history) {
            this.editMessageAPI(history);
        },
        resendMessage(data) {
            this.resendMessageAPI(data)
        },
        createMessageAPI() {
            this.showLoader = true;
            let url = '/projects/messages/create';

            const data = {
                message: this.message,
                projectIds: this.selectedProjects.map(project => +project.id)
            }

            qfetch(url, {method: 'POST', headers: {},  body: data})
                .then(response => {
                    this.getProjectsMessagesAPI()
                    this.message = '';
                    this.showLoader = false;

                })
        },
        editMessageAPI(message) {
            this.showLoader = true;
            let url = `/projects/messages/histories/${message.id}/edit`;

            const data = {
                message: message.text
            }

            qfetch(url, {method: 'POST', headers: {},  body: data})
                .then(response => {
                    this.action = 'create';
                    this.getProjectsMessagesAPI()
                    this.showLoader = false;

                })
        },
        resendMessageAPI(data) {
            this.showLoader = true;
            let url = `/projects/messages/${data.id}/resend`;

            qfetch(url, {method: 'POST', headers: {},  body: {projectIds: data.selectedProjects.map(project => +project.id)}})
                .then(response => {
                    this.action = 'create';
                    this.getProjectsMessagesAPI()
                    this.showLoader = false;
                })
        },
        getProjectMessagesAPI(projectId, from) {
            this.showLoader = true;
            let url = `/projects/${projectId}/messages`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.messages = response.items;

                    switch (from) {
                        case 'info-center-page':
                            // this.messagesProjects = null;
                            // this.messagesProjects = JSON.parse(JSON.stringify(this.selectedProjects));
                            break;
                        case 'info-center-history':
                            break;
                    }
                    this.showLoader = false;
                })
        },
        getProjectsMessagesAPI() {
            this.showLoader = true;
            let url = `/projects/messages`;

            let data = {
                projectIds: this.projects.map(project => +project.id)
            }

            qfetch(url, {method: 'POST', headers: {}, body: data})
                .then(response => {
                    this.messages = response.items;
                    this.filteredMessages = response.items.filter(project => {
                        return +project.id === +this.filteredProject.id;
                    })[0]['messages']
                    this.showLoader = false;
                })
        },
        deleteMessageHistoryAPI() {
            if(this.action !== 'delete' || !this.selectedHistory) return false;

            this.showLoader = true;
            let url = `/projects/messages/histories/${this.selectedHistory.id}/delete`;

            qfetch(url, {method: 'DELETE', headers: {}})
                .then(response => {
                    this.deleteMessageHistory(this.selectedHistory);
                    this.showLoader = false;
                })
        },
        deleteMessageHistory(history) {

            this.messages.forEach((project, projectIndex) => {
                project.messages.forEach((message, messageIndex) => {
                    if(+message.id === +history.pmId) {
                        message.history.forEach((messageHistory, historyIndex) => {
                            if(+messageHistory.id === +history.id) {
                                this.messages[projectIndex].messages[messageIndex].history.splice(historyIndex, 1)

                                if(this.messages[projectIndex].messages[messageIndex].history.length === 0) {
                                    this.messages[projectIndex].messages.splice(messageIndex, 1);
                                }
                            }
                        })
                    }
                })

            })
            this.action = 'create';
            this.selectedHistory = null;
        },
        showMessageHistoryModal(history) {
            console.log('HISTORY', history)

            this.messageHistoryModalSelectedHistory = history;

            console.log('this.messageHistoryModalSelectedHistory', this.messageHistoryModalSelectedHistory)
            console.log('this.getMessageHistoriesByMessage', this.getMessageHistoriesByMessage)

            this.messageHistoryModalDisplay = true;
        },
        filteredProjectChanged(project) {
            this.action = 'create'
            this.filteredProject = project
        }
    },
    created() {
        this.getCompaniesAPI();
    },
});

