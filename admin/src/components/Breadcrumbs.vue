<template>
  <nav aria-label="breadcrumb m-2">
    <ul class="breadcrumb">
      <li v-for="(crumb, index) in breadcrumbs" :key="index">
        <a href="#" v-if="crumb.path && isCheck" @click.prevent="handleClick(crumb)">{{ crumb.name }}</a>
        <router-link v-else-if="crumb.path" :to="getPath(crumb.path)">{{ crumb.name }}</router-link>
        <span v-else>{{ crumb.name }}</span>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    isCheck: {
      type: Boolean,
      default: false,
    }
  },
  computed: {
    breadcrumbs() {
      return this.$route.meta.breadcrumbs || [];
    }
  },
  methods: {
    handleClick(crumb) {
      this.$emit("navigate", crumb); // Emit navigation event
    },
    getPath(path) {
      //   if (path.includes(":id") && this.$route.params.id) {
      //     return path.replace(":id", this.$route.params.id);
      //   }
      return path;
    }
  }
};
</script>

<style scoped>
.breadcrumb {
  display: flex;
  list-style: none;
  padding: 10px;
  background: #fff;
}

.breadcrumb li {
  margin-right: 10px;
}

.breadcrumb li:not(:last-child)::after {
  content: " >> ";
  margin-left: 10px;
}
</style>