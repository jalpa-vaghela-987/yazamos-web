<template>
    <div id="app">
        <Localization />
    </div>
</template>

<script>
import Localization from "@/Localization";
import { request } from "@/Util/Request";

export default {
    name: 'App',
    components: {
        Localization
    },
    mounted() {
        // this.permissionInterval = setInterval(() => {
        //     this.fetchPermissions();
        // }, 30000); // update every 30 seconds
    },
    methods: {
    },
    // beforeDestroy() {
    //     clearInterval(this.permissionInterval);
    // }
};
</script>
