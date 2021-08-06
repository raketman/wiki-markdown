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

        var result = [{
          header: true,
          title: wiki.name,
          hiddenOnCollapse: true
        }];

        for(var child in wiki.childs) {
          result.push(wikiHelper.buildMenu(wiki.childs[child]))
        }

        // чтобы при открытии / открывался нужный элемент
        if (result[1]) {
          result[1].alias = ['/'];
        }


        return result;
      }
  },
  data() {
    return {
      menu: [
      ]
    }
  }
}
</script>

<style scoped>
</style>
