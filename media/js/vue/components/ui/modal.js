Vue.component('modal', {
    template: `
    <div class="new-modal" :style="styles" @click.stop="close">
      <div class="new-modal-content" @click.stop="clickContent">
        <div class="new-modal-header">
          <span class="close" @click.stop="close"><img src="/media/img/close.svg" alt="close-modal"></span>          
          <slot name="header"></slot>
        </div>
        <div class="new-modal-body">
          <slot name="body" :confirmed="confirmed"></slot>
        </div>
        <div class="new-modal-footer">
          <slot name="footer" :confirm="confirm">
          </slot>
        </div>
      </div>
    </div>
    `,
    props: {
        modalId: {required: true}
    },
    data() {
        return {
            confirmed: false,
            hidden: true,
        };
    },
    created() {
        window.eventBus.$on('showModal', id => {
            if(this.modalId == id){
                this.hidden = false;
            }
        })
    },
    methods: {
        confirm: function () {
            this.$emit('confirm')
            this.confirmed = true;
            this.close();
            setTimeout(() => {
                this.confirmed = false;
            },500);
        },
        close: function(){
            this.hidden = true;
        },
        clickContent: function () {

        }
    },
    computed: {
        styles: function () {
            if( ! this.hidden){
                return "display: block;";
            }
        }
    },
    mounted() {
    }
});
