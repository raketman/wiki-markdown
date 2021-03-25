<template>
  <div>
    <ul>
      <li>smallblock1</li>
      <li>smallblock2</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "List",
  computed: {
    isLoading() {
     return this.$store.getters['wiki/page'] === null
    },
    content() {
      return this.$store.getters['wiki/page'];
    }
  }
}
</script>

<style scoped>

</style>
