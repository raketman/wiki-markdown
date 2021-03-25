<template>
  <div id="wrapper" v-bind:class="{ full: !menuCollapsed, collapsed: menuCollapsed }">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
            <Search></Search>
        </div>
      </div>
      <div class="row">
        <div class="col-sm">
          <Breadcrumb></Breadcrumb>
        </div>
      </div>

      <div class="row"  v-if="isPage">
        <div class="col-sm">
          <Page></Page>
        </div>
      </div>
      <div class="row" v-if="isList">
        <div class="col-sm">
          <List></List>
        </div>
      </div>
    </div>
    <Menu :onToggleCollapse="onToggleCollapse"></Menu>
  </div>
</template>

<script>
import wikiHelper from '../helpers/wiki'

export default {
  async asyncData ({ app, route, params, error, store }) {
    try {
      if (!store.getters['wiki/list']) {
        await store.dispatch('wiki/list')
      }

      // Выберем первый
      var item = wikiHelper.findWikiBy(store.getters['wiki/list'], 'path', route.path);
      console.log(item);
      if (!item) {
          item = wikiHelper.findFirstFile(store.getters['wiki/list'].childs);
      }

      store.dispatch('wiki/page', item)

    } catch (err) {
      console.log(err)
      return error({
        statusCode: 503,
        message: 'Сервер временно недоступен'
      })
    }
  },
  data() {
    return {
      menuCollapsed: false
    }
  },
  methods: {
    onToggleCollapse(collapsed) {
      this.menuCollapsed = collapsed
    }
  },
  computed: {
    isList() {
      return this.isSearch || (this.isSelected && !this.isFile)
    },
    isPage() {
      return !this.isSearch && this.isSelected && this.isFile
    },
    isSearch() {
      return this.$store.getters['wiki/query']
    },
    isFile() {
      return wikiHelper.isFile(this.$store.getters['wiki/selected'])
    },
    isSelected() {
      return this.$store.getters['wiki/selected']
    }
  }
}
</script>

<style>
  #wrapper.full {
    padding-left: 350px;
  }

  #wrapper.collapsed {
    padding-left: 50px;
  }
</style>
