Vue.component('resend-message-dropdown', {
    template: `
            <div class="resend-message-dropdown" @click.stop>
                <div class="resend-projects-list">
                    <div class="resend-project-item" v-for="project in resendProjects">
                        <input 
                            type="checkbox" 
                            class="info-center-checkbox" 
                            @click="toggleProject(project)"
                        >
                        <div class="resend-project-name"><span>{{ project.name }}</span></div>
                    </div>
                </div>
                <div class="resend-message-actions">
                    <button class="confirm-dropdown-btn" @click="$emit('onResend', selectedProjects)">{{ trans.resend }}</button>
                </div>
            </div>
    `,
    props: {
        resendProjects: {required: true},
        translations: { required: true }
    },
    computed: {

    },
    data() {
        return {
            selectedProjects: [],
            trans: JSON.parse(this.translations),
        }
    },
    watch: {

    },
    methods: {
        toggleProject(project) {
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
    }
});

