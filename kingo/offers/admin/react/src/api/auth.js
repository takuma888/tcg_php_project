import axios from 'axios'

const instance = axios.create({
  baseURL: window['authRequestBaseUrl'],
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true
})


export default {
  // session
  session: () => {
    return instance.get('/session')
  },
  // 登录
  login: (username, password) => {
    return instance.post('/login', {
      username: username,
      password: password
    })
  },
  // 退出
  logout: () => {
    return instance.get('/logout')
  }
}