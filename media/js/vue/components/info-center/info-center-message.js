Vue.component('info-center-message', {
    template: `
        <div class="info-center-send-content">
            <div class="info-center-send-projects">
                <div 
                    v-for="project in projects"
                    class="info-center-project-tag"
                    :class="{'project-selected': isProjectSelected(project)}"
                    @click="projectSelected(project)"
                >
                    <div class="project-tag-name">
                        <span>{{ project.name }}</span>
                    </div>
                </div>
            </div>
            <div class="info-center-send-message">
                <textarea 
                    ref="message-textarea"
                    cols="30" 
                    rows="10" 
                    v-model="currentMessage"
                    :placeholder="trans.your_message"
                    @input="messageChanged"
                    @keydown.ctrl.enter="$emit('sendMessage')"
                >{{ currentMessage }}</textarea>
            </div>
        </div>
    `,
    props: {
        projects: {required: true},
        message: {required: true},
        translations: { required: true }
    },
    components: {

    },
    computed: {

    },
    data() {
        return {
            currentMessage: '',
            selectedProjects: [],
            trans: JSON.parse(this.translations),
        }
    },
    watch: {
        message(value) {
            this.currentMessage = value;
        },
        selectedProjects(projects) {
            this.$emit('onProjectSelected', projects)
        },
        projects(projects) {
            this.selectedProjects = JSON.parse(JSON.stringify(projects));
        }
    },
    methods: {
        messageChanged(event) {
            this.$emit('onMessageChanged', event.target.value)
        },
        projectSelected(project) {
            if(this.isProjectSelected(project)) {
                this.removeProjectFromSelected(project)
            } else {
                this.selectedProjects.push(project)
            }
        },
        isProjectSelected(project) {
            return (this.selectedProjects.filter(item => +item.id === +project.id).length > 0)
        },
        removeProjectFromSelected(project) {
            this.selectedProjects.forEach((item, itemIndex) => {
                if(+item.id === +project.id) {
                  this.selectedProjects.splice(itemIndex,1);
                }
            }, this)
        },
    },
    mounted() {
        this.currentMessage = this.message;
        this.selectedProjects = JSON.parse(JSON.stringify(this.projects))
    }
});

