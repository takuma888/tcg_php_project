import axios from 'axios'
import { init } from './prepare'

const instance = axios.create({
  baseURL: window['authRequestBaseUrl'],
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true
})

export default {
  // session
  session: () => dispatch => {
    init(instance, dispatch)
    return instance.get('/session')
  },
  // 登录
  login: (username, password) => dispatch => {
    init(instance, dispatch)
    return instance.post('/login', {
      username: username,
      password: password
    })
  },
  // 退出
  logout: () => dispatch => {
    init(instance, dispatch)
    return instance.get('/logout')
  }
}