<template>
  <div>
    <sidebar-menu :menu="items" @toggle-collapse="onToggleCollapse" @item-click="onItemClick"/>
    <!--div v-html="this.$store.getters['wiki/list'].name"></div>
    <div v-for="item in this.$store.getters['wiki/list'].childs">
        <MenuSub :item="item"></MenuSub>
    </div-->
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
      console.log(item);
      event.preventDefault();
      return false;
    }
  },
  computed: {
      items () {
        var wiki = this.$store.getters['wiki/list'];

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
