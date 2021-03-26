<template>
  <div>
    <ul>
      <li v-for="item in this.$store.getters['wiki/search']">
        <div style="zoom: 30%" v-on:click="select(item)">
          <Content :item="findWiki(item)" :content="item.content"></Content>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import wikiHelper from './../helpers/wiki'

export default {
  name: "List",
  computed: {
    isLoading() {
     return this.$store.getters['wiki/page'] === null
    },
    content() {
      return this.$store.getters['wiki/page'];
    }
  },
  methods: {
    findWiki(item) {
      return wikiHelper.findWikiBy(this.$store.getters['wiki/list'], 'id', item.id)
    },
    select(item) {
      var wiki = wikiHelper.findWikiBy(this.$store.getters['wiki/list'], 'id', item.id);

      this.$store.commit('wiki/query', "");
      this.$router.push(wiki.path)
    }
  }
}
</script>

<style scoped>

</style>
