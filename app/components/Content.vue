<template>
  <div>
      <vue-markdown v-if="isMarkdown" :source="source"></vue-markdown>
      <div v-if="!isMarkdown"  v-html="source"></div>
  </div>
</template>

<script>
import VueMarkdown from '@adapttive/vue-markdown'
import wikiHelper from '../helpers/wiki'
import Prism from 'prismjs'
import 'prismjs/themes/prism.css'

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
  },
  mounted() {
    Prism.highlightAll();
  }
}
</script>

<style scoped>
@import 'https://unpkg.com/katex@0.13.18/dist/katex.min.css';
@import 'https://fonts.googleapis.com/css?family=Questrial';
@import 'https://fonts.googleapis.com/css?family=Arvo';
</style>
