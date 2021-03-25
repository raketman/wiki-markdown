<template>
  <div>
    <b-button v-b-toggle.sidebar-1>Toggle Sidebar</b-button>
    <input v-model="query" v-on:keyup="search"/>

  </div>
</template>

<script>
export default {
  name: "Search",
  data()  {
    return {
      query: ""
    }
  },
  methods: {
    search() {
      this.$store.dispatch("wiki/search", this.query)
    }
  }
}
</script>

<style scoped>

</style>
