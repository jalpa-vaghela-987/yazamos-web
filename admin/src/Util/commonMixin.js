import { getAuthUser, setStorage } from "@/Util/auth";

export default {
    data() {
        return {};
    },
    mounted() {
    },
    methods: {
        async redirectingUserPage(data) {
            await setStorage("auth", JSON.stringify(data));
            await this.$store.dispatch("user", data.user);
            // await this.$store.dispatch("setPermissions", data.user.permissions);
            await this.$store.dispatch("setUnAuthorized", false);
            if(this.$global.hasRole('super admin') || this.$global.hasRole('admin')) {
                await this.$router.push({ name: "AdminDashboard" });
            } else {
                await this.$router.push({ name: "Dashboard" });
            }

        }
    }
};
