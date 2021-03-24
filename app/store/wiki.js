const state = () => ({
  wiki: null,
  search: [],
  page: null,
  query: ""
})


const getters = {
  list(state) {
    return state.wiki
  },
  search(state) {
    return state.search
  },
  page(state) {
    return state.page
  }
}


const mutations = {
  list(state, wiki) {
    console.log(wiki)
    state.wiki = wiki
  },
  search(state, search) {
    state.search = search
  },
  page(state, page) {
    state.page = page
  },
  query(state, query) {
    state.query = query
  },
  select(state, item) {
    // Закроем все дочерние
    if (item.type === 'dir') {

    } else {
      //сделаем активным в state
    }
  },
}

const actions =  {
  async list ({ commit }) {
    const wiki = await this.$axios.$get('/list.json')
    commit('list', wiki)
  },
  async page ({ commit }, item) {
    const content = await this.$axios.$get('/page.json?page=' + item.path)
    commit('page', content)
    commit('select', item)
  },
  async search ({ commit }, query) {
    commit('query', query)
    if (!query || query.length === 0) {
      commit('search', [])
      return
    }
    const search = await this.$axios.$get('/search.json?query=' + query)
    commit('search', search)
  },
}

export default {
  state,
  actions,
  getters,
  mutations
}



