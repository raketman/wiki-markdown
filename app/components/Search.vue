<template>
  <div>
    <div class="fias-container fias-container-multi">
      <ul class="fias-choices">
        <li class="search-field" v-bind:class="{ 'fias-width100': isInputWidth100 }">
          <input v-model="searchField"  v-bind:class="{ 'fias-width100': isInputWidth100 }" type="text" placeholder="Search"  autocomplete="off" v-on:keyup="search" >
        </li>
      </ul>
      <div class="fias-stop" v-show="isSearchField" v-on:click="reset"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Search",
  computed: {
    isInputWidth100() {
      return true
    },
    isSearchField() {
      return this.$store.getters['wiki/query']
    },
    searchField: {
      get () {
        return this.$store.getters['wiki/query']
      },
      set (value) {
        this.$store.commit('wiki/query', value)
      }
    },
  },
  methods: {
    reset() {
      this.$store.commit('wiki/query', "");
    },
    search(e) {
      if ((e.keyCode && e.keyCode === 27) || (e.code && e.code === 'Escape')) {
        this.reset();
        return;
      }

      this.$store.dispatch("wiki/search", this.searchField)
    }
  }
}
</script>

<style scoped>
@import './../assets/css/field.css';
</style>
