import axios from '@/api/axios'

export default {
  list: (params) => {
    return axios.get('/offers', {
      params: params
    })
  }
}
