<template>
  <div>
    <navigation-bar />
    <router-view />
  </div>
</template>

<script>
import NavigationBar from "@/components/NavigationBar.vue";

export default {
  components: {
    NavigationBar,
  },
};
</script>