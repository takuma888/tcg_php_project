import axios from '@/api/axios'

export default {
  // 角色列表
  list: (params) => {
    return axios.get('/roles', {
      params: params
    })
  },
  // 批量删除角色
  delete: (ids) => {
    return axios.post('/roles/delete', {
      ids: ids
    })
  },
  // 获取角色权限
  permissions: (ids) => {
    return axios.post('/roles/permissions', {
      ids: ids
    })
  }
}
