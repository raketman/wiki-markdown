<template>
  <div>
    <div v-if="isLoading">Идет загрузка</div>
    <div v-if="!isLoading" v-html="content"></div>
  </div>
</template>

<script>
export default {
  name: "Page",
  computed: {
    isLoading() {
     return this.$store.getters['wiki/page'] === null
    },
    content() {
      return this.$store.getters['wiki/page'].content;
    }
  }
}
</script>

<style scoped>

</style>
