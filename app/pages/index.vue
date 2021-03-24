<template>
  <div class="container">
    <div>
      <Search></Search>
      <Menu></Menu>
      <Page></Page>
    </div>
  </div>
</template>

<script>
function findFile(childs) {
  for (var child in childs) {
    var item = childs[child];

    if (item.type === 'file') {
      return item;
    }

    item = findFile(item.childs);
    if (item) {
      return item;
    }
  }

  return null;
}

export default {
  async asyncData ({ app, route, params, error, store }) {
    try {
      await store.dispatch('wiki/list')

      // Выберем первый
      const item = findFile(store.getters['wiki/list'].childs);
      if (item) {
          store.dispatch('wiki/page', item)
      }

    } catch (err) {
      console.log(err)
      return error({
        statusCode: 503,
        message: 'Сервер временно недоступен'
      })
    }
  },
  methods: {
    created() {
      console.log('hello');
    }
  }
}
</script>

<style>
.container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.title {
  font-family:
    'Quicksand',
    'Source Sans Pro',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    'Helvetica Neue',
    Arial,
    sans-serif;
  display: block;
  font-weight: 300;
  font-size: 100px;
  color: #35495e;
  letter-spacing: 1px;
}

.subtitle {
  font-weight: 300;
  font-size: 42px;
  color: #526488;
  word-spacing: 5px;
  padding-bottom: 15px;
}

.links {
  padding-top: 15px;
}
</style>
