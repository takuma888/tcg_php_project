import axios from '@/api/axios'

export default {
  // 用户数据
  get: (id) => {
    return axios.get(`/user/${id}`)
  },
  // 添加用户
  add: (auth, extra) => {
    return axios.post('/user/add', {
      auth: auth,
      extra: extra || {}
    })
  },
  // 更新用户
  edit: (id, params) => {
    return axios.post(`/user/edit/${id}`, params)
  },
  // 删除用户
  delete: (id) => {
    return axios.post(`/user/delete/${id}`)
  }
}
