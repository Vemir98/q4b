Vue.component('plans-section', {
    template: `
        <div class="plans-section" v-if="plans.length">
            <div class="labtest-plans-title">{{ trans.plans }}</div>
            <div class="labtest-plans-table">
                <table>
                    <thead>
                    <tr>
                        <th>{{ trans.name_type }}</th>
                        <th>{{ trans.profession }}</th>
                        <th>{{ trans.place_name_number }}</th>
                        <th>{{ trans.date }}</th>
                        <th>{{ trans.image }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="plan in plans" :key="plan.id">
                        <td>{{ plan.name }}</td>
                        <td>{{ plan.professionName }}</td>
                        <td>{{ plan.placeName ? plan.placeName : plan.placeNumber }}</td>
                        <td>{{ convertTimestampToDate(plan.createdAt) }}</td>
                        <td><div class="ltest_attachment_icon" v-if="plan.fileOriginalName"><i class="icon q4bikon-file" @click="openImage(plan.fileOriginalName)"></i></div></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        `,
    props: {
        projectId: {required: false},
        translations: {required: true},
        plans: {required: true},
        siteUrl: {required: true}
        // floors: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            image: null
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        }
    },
    created() {
    },
    mounted() {

    },
    watch: {

    },
    methods: {
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1) > 9) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        async openImage(url) {
            this.$emit('showLoader')
            let allowedFormats = ['jpg','png','jpeg'];
            let format = url.split('.')[url.split('.').length - 1];
            if(!allowedFormats.includes(format)) {
                this.$emit('hideLoader')

                return false;
            }

            if(!url.includes('https')) {
                let a = this.siteUrl.split('/');
                if(a[a.length -1] === 'he' || a[a.length -1] === 'ru') {
                    a.splice((a.length - 1), 1)
                }
                let baseUrlWithoutLanguage = a.join('/');
                url = baseUrlWithoutLanguage + '/' + url
            }
            console.log('URL', url)
            this.showLoader = true;
            let response = await fetch(url);
            let data = await response.blob();
            let metadata = {
                type: `image/${format}`
            };

            let file = new File([data], "test.jpg", metadata);
            let ext = file.type.split('/')[1];
            this.image = {
                id: null,
                fileName: Q4U.timestamp() + '.' + ext,
                fileOriginalName: Q4U.timestamp() + '.' + ext,
                filePath: '',
                ext: ext === 'jpg' ? 'jpeg' : ext,
                type: 'plan'
            }

            this.getBase64FromBlob(data)
                .then(base64 => {
                    this.image.src = base64
                    this.$emit('openEditor', this.image)
                    this.$emit('hideLoader')
                })
        },
        async getBase64FromBlob(blob) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = () => {
                    const base64data = reader.result;
                    resolve(base64data);
                }
            });
        }
    },
});

