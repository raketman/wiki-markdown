<template>
  <div id="wrapper" v-bind:class="{ full: !menuCollapsed, collapsed: menuCollapsed }">
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-sm">
            <Search></Search>
        </div>
      </div>

      <div class="row"  v-if="isPage">
        <div class="col-sm  mt-3">
          <Page></Page>
        </div>
      </div>
      <div class="row" v-if="isList">
        <div class="col-sm mt-3">
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
    var prefix = app.$config.app.prefix;
    try {
      if (!store.getters['wiki/list']) {
        await store.dispatch('wiki/list')
      }

      var routePath = decodeURI(route.path);
      if (prefix) {
        routePath = routePath.replace(prefix, '');
        if (routePath === '') {
          routePath = '/'
        }
      }

      var item = null;
      if (routePath) {
          item = wikiHelper.findWikiBy(store.getters['wiki/list'], 'id', routePath);
      }

      if (!item) {  // Выберем первый
          item = wikiHelper.findFirstFile(store.getters['wiki/list'].childs);
      }

      store.dispatch('wiki/page', item)
        .then(() => store.commit('wiki/query', ""))
        .then( () => {
          if (!route.hash) {
            return;
          }

          setTimeout(() => {

            var doc = window.document.querySelector(route.hash);
            if (!doc) {
              return;
            }


            window.scrollTo({
              top: doc.offsetTop
            })
          }, 100)

        })

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
