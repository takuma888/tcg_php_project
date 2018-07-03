import axios from 'axios'
import { init } from '@/api/prepare'

const instance = axios.create({
  baseURL: window['authBaseUrl'],
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true
})

init(instance)

export default instance
