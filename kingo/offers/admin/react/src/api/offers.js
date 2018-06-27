import axios from './axios'
import { init } from './prepare'

export default {
  // offer 列表
  list: (params) => dispatch => {
    const promise = axios.get('/offers', {
      params: params
    })
    return promise.then((response) => {
      init(response, dispatch)
    })
  }
}