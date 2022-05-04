Vue.component('print-pdf', {
    template: `
        <div ref="pdf-container">
            <slot></slot>
        </div>
    `,
    props: {
        translations: { required: true },
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

    },
    mounted() {
        Promise.all(Array.from(document.images).map(img => {
            if (img.complete)
                return Promise.resolve(img.naturalHeight !== 0);
            return new Promise(resolve => {
                img.addEventListener('load', () => resolve(true));
                img.addEventListener('error', () => resolve(false));
            });
        })).then(results => {
            const content = this.$slots.default[0].elm;

            let oldContent = document.getElementsByClassName('print-container');

            if(oldContent.length) {
                Array.from(oldContent).forEach(node => {
                    document.body.removeChild(node)
                })
            }

            let pdfContainer = document.createElement('div');
            pdfContainer.classList.add('print-container');
            // pdfContainer.style.display = 'none';
            pdfContainer.appendChild(content.cloneNode(true))

            document.body.appendChild(pdfContainer);
            window.print();
            this.$emit('onClose')
        });
    }
});

