import axios from '@/api/axios'

export default {
  // 角色数据
  get: (id) => {
    return axios.get(`/role/${id}`)
  },
  // 添加角色
  add: (id, name, priority, desc) => {
    return axios.post(`/role/add`, {
      id: id,
      name: name,
      priority: priority,
      desc: desc
    })
  },
  // 修改角色
  edit: (id, params) => {
    return axios.post(`/role/edit/${id}`, params)
  },
  // 删除
  delete: (id) => {
    return axios.post(`/role/delete/${id}`)
  },
  // 验证
  validateIdUnique: (newId, oldId) => {
    return axios.post(`/role/validate-id/unique`, {
      new_id: newId,
      old_id: oldId
    })
  }
}
