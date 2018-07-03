import Vue from 'vue'
import Vuex from 'vuex'

import UiStore from '@/vuex/ui'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    ui: UiStore
  }
})
