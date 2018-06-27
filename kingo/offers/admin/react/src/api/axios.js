import axios from 'axios'

const instance = axios.create({
  baseURL: window['appRequestBaseUrl'],
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true
})

export default instance