import axios from 'axios'
import ElementUI from 'element-ui'

let instance = axios.create({
  baseURL: 'http://tcg.php.localhost.com/kingo/users/admin',
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
})

instance.interceptors.response.use((response) => {
  let res = response.data
  let { msg, code, data } = res
  if (code !== 0) {
    ElementUI.Message({
      message: msg,
      type: 'error'
    })
    return Promise.reject(new Error(msg))
  }
  return data
})

export default instance
