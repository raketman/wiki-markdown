<template>
  <div>
    <div v-html="item.name" v-on:click="select"></div>
    <div v-for="child in item.childs">
      <MenuSub :item="child"></MenuSub>
    </div>
  </div>
</template>

<script>
export default {
  name: "MenuSub",
  props: {
    item: Object
  },
  methods: {
    select() {
      if (this.item.type === 'dir') {
        this.$store.commit('wiki/select', this.item)
        return
      }

      this.$store.dispatch('wiki/page', this.item)
    }
  }
}
</script>

<style scoped>

</style>
