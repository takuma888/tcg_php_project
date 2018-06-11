// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Vuex from 'vuex'
import ElementUI from 'element-ui'
import VueRouter from 'vue-router'
import 'normalize.css/normalize.css'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App'
import routes from './router'
import 'font-awesome/css/font-awesome.min.css'
import Api from './api'
import store from './vuex'

Vue.use(ElementUI)
Vue.use(VueRouter)
Vue.use(Vuex)

const router = new VueRouter({
  // mode: 'history',
  routes: routes
})

router.beforeEach((to, from, next) => {
  let user = JSON.parse(sessionStorage.getItem('user'))
  if (!user && to.path !== '/login') {
    Api.session().then((data) => {
      if (!data.user) {
        next('/login')
        ElementUI.Message({
          message: '请重新登录',
          type: 'warning'
        })
      } else {
        sessionStorage.setItem('user', JSON.stringify(data.user))
        next()
      }
    })
  } else {
    next()
  }
})

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
