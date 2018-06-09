import axios from 'axios'
import UsersApi from '@/api/users'
import UserApi from '@/api/user'

let base = 'http://tcg.php.localhost.com/kingo/users/admin'

let config = {
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
}

export default {
  // session
  session: () => {
    return axios.get(`${base}/session`, config)
  },
  // 登录
  login: (username, password) => {
    return axios.post(`${base}/login`, {
      username: username,
      password: password
    }, config)
  },
  // 退出
  logout: () => {
    return axios.get(`${base}/logout`, config)
  },
  // users相关API
  users: UsersApi,
  // user 相关API
  user: UserApi
}
