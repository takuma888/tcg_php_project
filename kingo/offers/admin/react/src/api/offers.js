import axios from '@/api/axios'

export default {
  // offer 列表
  list: (params) => {
    return axios.get('/offers', {
      params: params
    })
  }
}