import axios from 'axios'

let base = 'http://tcg.php.localhost.com/kingo/users/admin'

let config = {
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
}

export default {
  // 用户数据
  get: (id) => {
    return axios.get(`${base}/user/${id}`, config)
  },
  // 添加用户
  add: (auth, extra) => {
    return axios.post(`${base}/user/add`, {
      auth: auth,
      extra: extra || {}
    }, config)
  },
  // 更新用户
  edit: (id, params) => {
    return axios.post(`${base}/user/edit/${id}`, params, config)
  },
  // 删除用户
  delete: (id) => {
    return axios.post(`${base}/user/delete/${id}`, {}, config)
  }
}
