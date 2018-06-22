import Vue from 'vue'
import Vuex from 'vuex'

import StaticStore from '@/vuex/static'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    static: StaticStore
  }
})
