<template>
  <div>
    <div v-if="isLoading">Идет загрузка</div>
    <div v-if="!isLoading">
      <Content :item="this.$store.getters['wiki/selected']" :content="this.$store.getters['wiki/page']"></Content>
    </div>
  </div>
</template>

<script>
export default {
  name: "Page",
  computed: {
    isLoading() {
     return this.$store.getters['wiki/page'] === null
    }
  }
}
</script>

<style scoped>

</style>
