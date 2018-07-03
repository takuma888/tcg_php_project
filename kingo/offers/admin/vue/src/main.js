// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Api from '@/api'
import store from '@/vuex'
import App from '@/App'
import routes from '@/router'
import MuseUI from 'muse-ui'
import ElementUI from 'element-ui'
import 'normalize.css/normalize.css'
import 'font-awesome/css/font-awesome.min.css'
import 'muse-ui/dist/muse-ui.css'
import 'element-ui/lib/theme-chalk/index.css'
import 'material-design-icons/iconfont/material-icons.css'

Vue.config.productionTip = false
Vue.prototype.$api = Api

Vue.use(Vuex)
Vue.use(MuseUI)
Vue.use(ElementUI)
Vue.use(VueRouter)

const router = new VueRouter({
  // mode: 'history',
  routes: routes
})

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
