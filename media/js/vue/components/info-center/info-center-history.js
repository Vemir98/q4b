Vue.component('info-center-history', {
    template: `
        <div class="info-center-history-content">
            <div class="info-center-history-filters">
                <div class="filter-item-label">
                    {{ trans.filter_by }}
                </div>
                <div class="filter-item">
                    <div class="multiselect-col">
                        <div class="filter-item-label" >{{ trans.projects }}</div>
                        <multiselect 
                            v-model="selectedProject"
                            :option-height="104" 
                            :placeholder="trans.select_project" 
                            :disabled="projects.length < 1" 
                            :options="projects" 
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
            <div class="info-center-history-filters-results" v-if="items.length && messagesHistoriesCount">
                <div 
                    class="info-center-history-message"
                    v-for="history in messagesHistories" 
                    :key="history.id"
                >
                    <div class="message-date">{{ convertTimestampToDate(history.createdAt) }}</div>
                    <div class="message-data">
                        <div class="message-data-creator">{{ history.creatorName }}</div>
                        <div 
                            v-if="(action === 'edit') && (+selectedHistory.id === +history.id)"
                            class="message-data-projects"
                        >
                            <div 
                                v-for="project in getSelectedHistoryProjects(selectedHistory)"
                                class="message-data-project-tag"
                            >{{ project.name }}</div>
                        </div>
                        <div class="message-data-date">{{ convertTimestampToTime(history.createdAt) }}</div>    
                    </div>
                    <div 
                        class="message-text history-selected"
                    >
                        <div v-if="((action === 'edit') && (+selectedHistory.id === +history.id))">
                            <textarea 
                                
                                cols="30" 
                                rows="4" 
                                v-model="currentMessage"
                                :ref="'history_' + history.id"
                                @keydown.esc="editHistoryCanceled(history)"
                                @keydown.ctrl.enter="historyEdited(history)"
                            >{{ history.text }}</textarea>
                        </div>
                        <div @dblclick="editHistory(history)"  v-else>
                            <textarea 
                               
                                cols="30" 
                                rows="4" 
                                disabled
                                :value="history.text"
                                :ref="'history_' + history.id"
                            ></textarea>
                        </div>
                        <div 
                            class="modal dropdown-cover"
                            v-if="(action === 'resend') && (+selectedHistory.id === +history.id)"  
                            @click="resendCanceled"></div>
                        <div 
                            class="info-center-history-edit-actions" 
                            v-if="(action === 'edit') && (+selectedHistory.id === +history.id)"
                        >
                            <div 
                                class="action-edit" 
                                :class="{'disabled': ((currentMessage.trim() === '') || (currentMessage.trim() === history.text))}" 
                                @click="historyEdited(history)"
                            ><span>{{ trans.edit }}</span></div>
                            <div class="action-cancel" @click="editHistoryCanceled(history)"><span>{{ trans.cancel }}</span></div>
                        </div>
                        <div 
                            class="info-center-history-actions"
                            style="position: relative"
                            v-else
                        >
                            <div 
                                class="info-center-icons project-tag-edit-icon" 
                                @click="editHistory(history)"
                            ></div>
                            <div class="info-center-resend-section">
                                <div 
                                    class="info-center-icons project-tag-resend-icon" 
                                    :class="{'disabled': !getSelectedHistoryMissingProjects(history).length}"
                                    @click="resendHistory(history)"
                                ></div>
                            </div>
                            <div 
                                class="info-center-icons project-tag-delete-icon" 
                                @click="deleteHistory(history)"
                            ></div>
                            <div 
                                class="info-center-icons message-text-history-icon" 
                                @click.stop="$emit('showMessageHistoryModal', history)"
                            ></div>
                            <resend-message-dropdown 
                                v-if="(action === 'resend') && (+selectedHistory.id === +history.id)"
                                :resendProjects="getSelectedHistoryMissingProjects(selectedHistory)"
                                :translations="translations"
                                @onResend="messageResented"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <confirm-popup
                v-if="messageDeletePopupDisplay"
                :translations="translations"
                @onClose="messageDeletePopupDisplay = false"
                @onConfirm="selectedHistoryActions('delete')"
            />
        </div>
    `,
    props: {
        projects: {required: true},
        items: {required: true},
        action: {required: true},
        translations: { required: true }
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    computed: {
        messagesHistories() {
            let histories = [];

            let currentProject = this.items.filter(project => {
                return +project.id === +this.selectedProject.id
            })[0];

            currentProject.messages.forEach(item => {
                item.history.forEach(history => {
                    histories.push(history)
                })
            })

            histories.sort(function(x, y){
                return y.createdAt - x.createdAt;
            })

            return histories;
        },
        messagesHistoriesCount() {
          return this.messagesHistories.length
        }
    },
    data() {
        return {
            selectedProject: null,
            selectedHistory: null,
            messageDeletePopupDisplay: false,
            currentMessage: '',
            trans: JSON.parse(this.translations),
        }
    },
    watch: {
        projects(projects) {
            this.selectedHistory = null;
            this.selectedProject = projects[0];
        },
        selectedProject(project) {
            this.selectedHistory = null;
            this.$emit('projectChanged', project)
        },
        action(action) {
            switch (action) {
                case 'edit':
                    break;
            }
        }
    },
    methods: {
        convertTimestampToDate(timestamp) {
            const thatDayDate = new Date(+timestamp*1000);
            const todayDate = new Date();
            const yesterdayDate = new Date(new Date().setDate(todayDate.getDate() - 1));

            const month = ((thatDayDate.getMonth()+1) > 9) ? (thatDayDate.getMonth()+1) : "0"+(thatDayDate.getMonth()+1);

            const today = todayDate.setHours(0,0,0,0);
            const yesterday = yesterdayDate.setHours(0,0,0,0);
            const thatDay = thatDayDate.setHours(0,0,0,0);

            switch (thatDay) {
                case today:
                    return this.trans.today;
                case yesterday:
                    return this.trans.yesterday;
                default:
                    return thatDayDate.getDate()+ '/' + month + '/' + thatDayDate.getFullYear();
            }
        },
        convertTimestampToTime(timestamp) {
            const date = new Date(+timestamp*1000);
            const minutes = date.getMinutes();
            const hour = date.getHours();
            return `${hour}:${minutes}`;
        },
        selectedHistoryActions(action, history) {
            if(history) {
                this.selectedHistory = history;
            }
            this.messageDeletePopupDisplay = false;
            this.$emit('actionSelected', {action: action, history: this.selectedHistory})
        },
        editHistory(history) {
            this.selectedHistory = history;
            this.currentMessage = history.text;
            setTimeout(() => {
                const textArea = this.$refs['history_' + history.id][0];
                textArea.focus()
                textArea.setSelectionRange(textArea.value.length,textArea.value.length);
            }, 0)
            this.selectedHistoryActions('edit')
        },
        editHistoryCanceled() {
            this.selectedHistory = null;
            this.currentMessage = '';
            this.$emit('actionSelected', {action: 'create', history: null})
        },
        resendCanceled() {
            this.$emit('actionSelected', {action: 'create', history: null})
        },
        deleteHistory(history) {
            this.selectedHistory = history;
            this.messageDeletePopupDisplay = true;
        },
        historyTextEdited(event) {
            if(this.selectedHistory) {
                this.currentMessage = event.target.value
            }
        },
        historyEdited(history) {
            if(this.currentMessage === '' || this.currentMessage === history.text) return false;

            this.$emit('messageEdited', {
                id: history.id,
                text: this.currentMessage
            })
        },
        messageResented(selectedProjects) {
            this.$emit('messageResented', {
                id: this.selectedHistory.pmId,
                selectedProjects
            })
        },
        resendHistory(history) {
            this.selectedHistory = history;
            if(!this.getSelectedHistoryMissingProjects(this.selectedHistory).length) {
                return false;
            }

            this.selectedHistoryActions('resend')
        },
        getSelectedHistoryMissingProjects(history) {
            const messageProjectsIds = this.getSelectedHistoryProjects(history).map(messageProject => +messageProject.id);

            return this.projects.filter(project => {
                return !(messageProjectsIds.includes(+project.id))
            })
        },
        getSelectedHistoryProjects(history) {

            if(history) {
                const messageId = history.pmId;

                let projectIds = this.items
                    .filter(project => {

                        let messages = project.messages
                            .filter(message => {
                                return +message.id === +messageId
                            })

                        return messages.length > 0
                    })
                    .map(project => +project.id)

                return this.projects.filter(project => projectIds.includes(+project.id));
            }
        },
        test() {
            alert('yey')
        }
    },
    mounted() {
        this.selectedProject = this.projects[0];
    }
});

