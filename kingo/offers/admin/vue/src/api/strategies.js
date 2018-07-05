import axios from '@/api/axios'

export default {
  list: (params) => {
    return axios.get('/strategies', {
      params: params
    })
  }
}
