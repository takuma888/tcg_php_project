import {addAlert, addNotification} from "../redux/action";

export const init = (axios, dispatch) => {
  axios.interceptors.response.use((response) => {
    let res = response.data
    let { msg, code, data, flash } = res
    if (flash.error && flash.error.length > 0) {
      flash.error.forEach((val) => {
        dispatch(addNotification({
          title: '错误',
          message: val,
          type: 'error'
        }))
      })
    }
    if (flash.warning && flash.warning.length > 0) {
      flash.warning.forEach((val) => {
        dispatch(addNotification({
          title: '警告',
          message: val,
          type: 'warning'
        }))
      })
    }
    if (flash.info && flash.info.length > 0) {
      flash.info.forEach((val) => {
        dispatch(addNotification({
          title: '消息',
          message: val,
          type: 'info'
        }))
      })
    }
    if (flash.success && flash.success.length > 0) {
      flash.success.forEach((val) => {
        dispatch(addNotification({
          title: '成功',
          message: val,
          type: 'success'
        }))
      })
    }
    if (code !== 0) {
      dispatch(addAlert({
        message: msg,
        type: 'error'
      }))
      return Promise.reject(new Error(msg))
    }
    return data
  })
}