<template>
  <div>
      <vue-markdown v-if="isMarkdown" :source="source"></vue-markdown>
      <div v-if="!isMarkdown"  v-html="source"></div>
  </div>
</template>

<script>
import VueMarkdown from '@adapttive/vue-markdown'
import wikiHelper from '../helpers/wiki'

export default {
  name: "Content",
  components: {
    VueMarkdown
  },
  props: {
    item: Object,
    content: String
  },
  computed: {
    isMarkdown() {
      return wikiHelper.isMarkdown(this.item)
    },
    source() {
      return this.content;
    }
  }
}
</script>

<style scoped>

</style>
