import axios from '@/api/axios'

export default {
  // 获取角色权限
  list: (ids) => {
    return axios.post('/permissions', {
      ids: ids
    })
  },
  // 编辑角色权限
  edit: (rolesPermissions) => {
    return axios.post('/permissions/edit', {
      roles: rolesPermissions
    })
  }
}
