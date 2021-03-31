<template>
  <div>
    <sidebar-menu :menu="items" @toggle-collapse="onToggleCollapse" @item-click="onItemClick"/>
  </div>
</template>

<script>
import { SidebarMenu } from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import wikiHelper from '../helpers/wiki'

export default {
  name: "Menu",
  components: {
    SidebarMenu
  },
  props: {
    onToggleCollapse: Function
  },
  methods: {
    onItemClick(event, item, node) {
      event.preventDefault();
      return false;
    }
  },
  computed: {
      items () {
        var wiki = this.$store.getters['wiki/list'];

        var selected = this.$store.getters['wiki/selected'];

        var result = [{
          header: true,
          title: wiki.name,
          hiddenOnCollapse: true
        }];

        for(var child in wiki.childs) {
          result.push(wikiHelper.buildMenu(wiki.childs[child]))
        }

        return result;
      }
  },
  data() {
    return {
      menu: [
        {
          header: true,
          title: 'Main Navigation',
          hiddenOnCollapse: true
        },
        {
          href: '/',
          title: 'Dashboard',
          icon: 'fa fa-user'
        },
        {
          title: 'Charts',
          icon: 'fa fa-chart-area',
          child: [
            {
              href: '/equeue/api.markdown',
              title: 'Sub Link'
            }
          ]
        }
      ]
    }
  }
}
</script>

<style scoped>
</style>
