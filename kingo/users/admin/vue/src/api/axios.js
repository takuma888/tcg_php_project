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
  let { msg, code, data, flash } = res
  if (flash.error && flash.error.length > 0) {
    flash.error.forEach((val) => {
      ElementUI.Notification({
        title: '错误',
        message: val,
        type: 'error'
      })
    })
  }
  if (flash.warning && flash.warning.length > 0) {
    flash.warning.forEach((val) => {
      ElementUI.Notification({
        title: '警告',
        message: val,
        type: 'warning'
      })
    })
  }
  if (flash.info && flash.info.length > 0) {
    flash.info.forEach((val) => {
      ElementUI.Notification({
        title: '消息',
        message: val,
        type: 'info'
      })
    })
  }
  if (flash.success && flash.success.length > 0) {
    flash.success.forEach((val) => {
      ElementUI.Notification({
        title: '成功',
        message: val,
        type: 'success'
      })
    })
  }
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
