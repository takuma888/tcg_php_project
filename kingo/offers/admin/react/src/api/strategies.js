import axios from './axios'
import { init } from './prepare'

export default {
  // 策略列表
  list: (params) => dispatch => {
    const promise = axios.get('/strategies', {
      params: params
    })
    return promise.then((response) => {
      return init(response, dispatch)
    })
  }
}