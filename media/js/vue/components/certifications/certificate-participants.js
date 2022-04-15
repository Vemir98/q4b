Vue.component('certificate-participants', {
    template: `
        <div class="certificate-participants">
            <div class="certificate-participants-header">
                <div class="certificate-participants-form">
                    <div class="q4b-input-text">
                        <div class="q4b-input-label">{{ trans.participant_name }}</div>
                        <input 
                            type="text" 
                            v-model="currentParticipant.name" 
                            autocomplete="off" 
                            :placeholder="trans.enter_participant_name"
                            :disabled="!canChange"
                        >
                    </div>
                    <div class="q4b-input-text">
                        <div class="q4b-input-label">{{ trans.participant_position }}</div>
                        <input 
                            type="text"
                            v-model="currentParticipant.position"
                            autocomplete="off"
                            :placeholder="trans.enter_participant_position"
                            :disabled="!canChange"
                        >
                    </div>
                </div>
                <div 
                    :class="['q4b-orange-text', {'q4b-disabled': (!canCreateParticipant)}]"
                    @click="createParticipant"
                >
                    {{ trans.add }}
                </div>
            </div>
            <div class="certificate-participants-body">
                <div class="certificate-participants-list">
                    <div 
                        class="certificate-participants-item" 
                        v-for="participant in participants" 
                        :key="participant.uid"
                    >
                        <div class="certificate-participant-data">
                            <div class="certificate-participant-data-name">{{ participant.name }}</div>
                            <div class="certificate-participant-data-position">{{ participant.position }}</div>
                        </div>
                        <div 
                            class="certificate-participant-delete-icon"
                            :class="{'q4b-disabled': !canChange}"
                            @click="removeParticipant(participant)"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        data: {required: true},
        canChange: {required: true}
    },
    components: {
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            currentParticipant: {
                name: '',
                position: ''
            },
            participants: [],
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        canCreateParticipant() {
            return (
                ((this.currentParticipant.name.trim()).length > 0) &&
                ((this.currentParticipant.position.trim()).length > 0)
            )
        }
    },
    created() {
    },
    mounted() {
        this.participants = JSON.parse(JSON.stringify(this.data))
    },
    watch: {
        participants(participants) {
            this.$emit('participantsUpdated', participants)
        }
    },
    methods: {
        createParticipant() {
            this.participants.push({
                uid: this.getRandomInt(100000, 9999999),
                name: this.currentParticipant.name,
                position: this.currentParticipant.position
            })
            this.currentParticipant.name = '';
            this.currentParticipant.position = '';
        },
        removeParticipant(participant) {
            if(!this.canChange) return false;
            const participantIndex = this.participants.findIndex(item => +item.uid === +participant.uid);

            this.participants.splice(participantIndex,1);
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        }
    },
});

