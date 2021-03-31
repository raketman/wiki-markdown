<template>
  <div>
    <div>
      <nuxt-link  v-for="item in this.$store.getters['wiki/search']" v-bind:to="getUrl(item)" no-prefetch>
        <div class="list-item">
            <Content :item="findWiki(item)" :content="item.content"></Content>
        </div>
      </nuxt-link>
    </div>
  </div>
</template>

<script>
import wikiHelper from './../helpers/wiki'

export default {
  name: "List",
  methods: {
    findWiki(item) {
      return wikiHelper.findWikiBy(this.$store.getters['wiki/list'], 'id', item.id)
    },
    getUrl(item) {
      var wiki = wikiHelper.findWikiBy(this.$store.getters['wiki/list'], 'id', item.id);

      return '/page' +  wiki.path;
    }
  }
}
</script>

<style scoped>
.list-item {
  width: auto;
  float: left;
  border: 1px solid lightblue;
  border-radius: 4px;
  zoom: 60%;
  padding: 15px;
  margin: 15px;
  cursor: pointer;
  max-height: 300px;
  max-width: 500px;
  overflow: auto;
}
</style>
