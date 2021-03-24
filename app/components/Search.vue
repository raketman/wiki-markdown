<template>
  <div>
    SEARCH
    <input v-model="query" v-on:keyup="search"/>
    <ul>
      <li>smallblock1</li>
      <li>smallblock2</li>
    </ul>
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
