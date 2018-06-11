import axios from '@/api/axios'

export default {
  // 用户列表
  list: (params) => {
    return axios.get('/users', {
      params: params
    })
  },
  // 批量删除用户
  delete: (ids) => {
    return axios.post('/users/delete', {
      ids: ids
    })
  }
}
