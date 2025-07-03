<template>
    <div class="dashboard">
        <h1>Hello, <b> {{user.name}} !!!</b></h1>
    </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
export default {
    name: "Dashboard",
    computed: {
        ...mapGetters(['user']),
    }
};
</script>

<style>
.dashboard {
    padding: 20px;
}
</style>

