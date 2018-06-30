import axios from './axios'
import { init } from './prepare'

export default {
  // 策略列表
  add: (name, description, priority) => dispatch => {
    const promise = axios.post('/strategy/add', {
      name: name,
      description: description,
      priority: priority
    })
    return promise.then((response) => {
      return init(response, dispatch)
    })
  },
  // 获取策略数据
  get: (id) => dispatch => {
    const promise = axios.get(`/strategy/${id}`)
    return promise.then((response) => {
      return init(response, dispatch)
    })
  }
}