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
    const promise = instance.get('/session')
    return promise.then((response) => {
      return init(response, dispatch)
    })
  },
  // 登录
  login: (username, password) => dispatch => {
    const promise = instance.post('/login', {
      username: username,
      password: password
    })
    return promise.then((response) => {
      return init(response, dispatch)
    })
  },
  // 退出
  logout: () => dispatch => {
    const promise = instance.get('/logout')
    return promise.then((response) => {
      return init(response, dispatch)
    })
  }
}