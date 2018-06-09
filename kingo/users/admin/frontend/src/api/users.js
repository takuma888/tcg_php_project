import axios from 'axios'

let base = 'http://tcg.php.localhost.com/kingo/users/admin'

let config = {
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
}

export default {
  // 用户列表
  list: (page, size, extra) => {
    return axios.get(`${base}/users`, {
      params: {
        page: page,
        size: size,
        extra: extra || {}
      },
      headers: config.headers
    })
  },
  // 批量删除用户
  delete: (ids) => {
    return axios.post(`${base}/users/delete`, {
      ids: ids
    }, config)
  }
}
