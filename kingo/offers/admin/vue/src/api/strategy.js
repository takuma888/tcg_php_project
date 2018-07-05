import axios from '@/api/axios'

export default {
  // 添加
  add: (name, description, priority) => {
    return axios.post('/strategy/add', {
      name, description, priority
    })
  },
  get: (id) => {
    return axios.get(`/strategy/${id}`)
  },
  update: (id, name, description, priority) => {
    return axios.post(`/strategy/edit/${id}`, {
      name, description, priority
    })
  },
  delete: (id) => {
    return axios.post(`/strategy/delete/${id}`)
  },
  deleteExt: (id) => {
    return axios.post(`/strategy/delete-ext/${id}`)
  },
  addExt: (strategyId, category, type, value1, value2) => {
    return axios.post('/strategy/add-ext', {
      strategy_id: strategyId,
      category,
      type,
      value1,
      value2
    })
  }
}
