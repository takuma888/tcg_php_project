export default {
  state: {
    asideCollapsed: false,
    path: '/'
  },
  mutations: {
    collapseAside (state) {
      state.asideCollapsed = true
    },
    expandAside (state) {
      state.asideCollapsed = false
    },
    path (state, path) {
      state.path = path
    }
  }
}
