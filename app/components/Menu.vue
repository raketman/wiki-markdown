<template>
  <div>
    <div v-html="this.$store.getters['wiki/list'].name"></div>
    <div v-for="item in this.$store.getters['wiki/list'].childs">
        <MenuSub :item="item"></MenuSub>
    </div>
    !!!
  </div>
</template>

<script>
export default {
  name: "Menu"
}
</script>

<style scoped>

</style>
