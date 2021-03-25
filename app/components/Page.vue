<template>
  <div>
    <div v-if="isLoading">Идет загрузка</div>
    <div v-if="!isLoading">
      <vue-markdown v-if="isMarkdown" :source="content"></vue-markdown>
      <div v-if="!isMarkdown"  v-html="content"></div>
    </div>
  </div>
</template>

<script>
import VueMarkdown from '@adapttive/vue-markdown'
import wikiHelper from '../helpers/wiki'

export default {
  name: "Page",
  components: {
    VueMarkdown
  },
  computed: {
    isMarkdown() {
      return wikiHelper.isMarkdown(this.$store.getters['wiki/selected'])
    },
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
