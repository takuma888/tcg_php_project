import axios from '@/api/axios'

export default {
  // 添加
  add: (name, description, priority, client, geo2, geo3, startAt, endAt) => {
    return axios.post('/strategy/add', {
      name,
      description,
      priority,
      client,
      geo2,
      geo3,
      start_at: startAt,
      end_at: endAt
    })
  },
  get: (id) => {
    return axios.get(`/strategy/${id}`)
  },
  update: (id, name, description, priority, client, geo2, geo3, startAt, endAt) => {
    return axios.post(`/strategy/edit/${id}`, {
      name,
      description,
      priority,
      client,
      geo2,
      geo3,
      start_at: startAt,
      end_at: endAt
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
