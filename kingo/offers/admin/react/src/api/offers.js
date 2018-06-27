import axios from './axios'
import { init } from './prepare'

export default {
  // offer 列表
  list: (params) => dispatch => {
    init(axios, dispatch)
    return axios.get('/offers', {
      params: params
    })
  }
}