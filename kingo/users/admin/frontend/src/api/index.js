import axios from 'axios'

let base = 'http://tcg.php.localhost.com/kingo/users/admin'

let config = {
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
}

export default {
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
  }
}
