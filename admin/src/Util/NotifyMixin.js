export default {
    data() {
        return {};
    },
    methods: {
        notifySuccess(string = 'Item') {
            this.$toast.success(`${string}`, {
                timeout: 50000 // 5 seconds duration
            });
        },
        notifySuccessUpdate(string = 'Item') {
            this.$toast.success(`${string} updated successfully.`, {
                timeout: 50000 // 5 seconds duration
            });
        },
        notifyWarning(string = 'Item') {
            this.$toast.warning(`${string} updated successfully.`, {
                timeout: 50000 // 5 seconds duration
            });
        },
        notifyError(string = 'Item') {
            this.$toast.error(`Something went wrong.`, {
                timeout: 50000 // 5 seconds duration
            });
        },
        notifyErrormessage(string = 'Item') {
            this.$toast.error(`${string}`, {
                timeout: 50000 // 5 seconds duration
            });
        },

        notifyErrorMessage(string = 'Item') {
            this.$toast.error(`${string}`, {
                timeout: 50000 // 5 seconds duration
            });
        },

    }
};
