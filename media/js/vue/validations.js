Vue.use(VeeValidate, {
    dictionary: {
        msg: {
            messages: {
                "required": "<?=__('This field is required.')?>",
                "decimal": "<?=__('This field should be numeric.')?>"
            }
        }
    }
});