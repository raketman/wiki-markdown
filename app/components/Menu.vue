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

<style>

  .v-sidebar-menu .vsm--link.vsm--link_active {

  }
  .v-sidebar-menu .vsm--link.vsm--link_exact-active {
    color: darkgray !important;
  }
  .v-sidebar-menu.vsm_expanded .vsm--item_open .vsm--link_level-1
  {
    background: none !important;
  }
  .v-sidebar-menu .vsm--toggle-btn {
    display: none !important;
  }

  .v-sidebar-menu .vsm--arrow:after {
    content: '';
    position: absolute;
    width: 15px;
    height: 5px;
    margin-top: -5px;
    background-color: lightgray;
    transform: rotate(45deg);
  }

  .v-sidebar-menu .vsm--arrow:before {
    content: '';
    position: absolute;
    width: 15px;
    height: 5px;
    margin-top: 4px;
    background-color: lightgray;
    box-shadow: 0 3px 5px rgba(0, 0, 0, .2);
    transform: rotate(-45deg);
  }

  .v-sidebar-menu .vsm--arrow:hover {
    animation: arrow-1 1s linear infinite;
  }
</style>
