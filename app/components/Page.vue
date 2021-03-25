<template>
  <div>
    <div v-if="isLoading">Идет загрузка</div>
    <div v-if="!isLoading">
      <vue-markdown  :source="content"></vue-markdown>
    </div>
  </div>
</template>

<script>
import VueMarkdown from '@adapttive/vue-markdown'

export default {
  name: "Page",
  components: {
    VueMarkdown
  },
  computed: {
    isLoading() {
     return this.$store.getters['wiki/page'] === null
    },
    content() {
      return this.$store.getters['wiki/page'];
    }
  }
}
</script>

<style scoped>

</style>
