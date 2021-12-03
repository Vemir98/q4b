Vue.component('message-history-modal', {
    template: `
        <div class="modal modal-cover" @click="$emit('onClose')">
            <div class="message-history-modal" @click.stop>
                <div class="message-history-modal-header">
                    <h1>{{ trans.message_history }}</h1>
                </div>
                <div class="modal-separator"></div>
                <div class="message-history-modal-body">
                    <div class="info-center-history-filters-results" style="border: none">
                        <div 
                            class="info-center-history-message"
                            v-for="history in messageHistories" 
                            :key="history.id"
                            :ref="(history.id === selectedHistory?.id) ? 'history-selected' : ''"
                        >
                            <div class="message-date">{{ convertTimestampToDate(history.createdAt) }}</div>
                            <div class="message-data">
                                <div class="message-data-creator">{{ history.creatorName }}</div>    
                                <div class="message-data-date">{{ convertTimestampToTime(history.createdAt) }}</div>    
                            </div>
                            <div 
                                class="message-text"
                                :class="{'history-selected': (history.id === selectedHistory?.id)}"
                            >
                                {{ history.text }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-separator"></div>
                <div class="message-history-modal-footer">
                    <button 
                        class="message-history-modal-close-btn" 
                        @click="$emit('onClose')"
                    >{{ trans.close }}</button>
                </div>
            </div>
        </div>
    `,
    props: {
        messageHistories: {required: true},
        selectedHistory: {required: true},
        translations: { required: true }
    },
    computed: {

    },
    data() {
        return {
            trans: JSON.parse(this.translations)
        }
    },
    watch: {

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
    },
    mounted() {
        this.$refs['history-selected'][0].scrollIntoView({block: "center", behavior: "smooth"})
    }
});

