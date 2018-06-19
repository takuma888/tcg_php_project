import axios from '@/api/axios'

export default {
  // 用户数据
  get: (id) => {
    return axios.get(`/user/${id}`)
  },
  // 添加用户
  addUsername: (username, password, confirmPassword, extra) => {
    return axios.post('/user/add/username', {
      username: username,
      password: password,
      confirm_password: confirmPassword,
      extra: extra || {}
    })
  },
  addEmail: (email, password, confirmPassword, extra) => {
    return axios.post('/user/add/email', {
      email: email,
      password: password,
      confirm_password: confirmPassword,
      extra: extra || {}
    })
  },
  addMobile: (mobile, password, confirmPassword, extra) => {
    return axios.post('/user/add/mobile', {
      mobile: mobile,
      password: password,
      confirm_password: confirmPassword,
      extra: extra || {}
    })
  },
  // 更新用户
  edit: (id, params) => {
    return axios.post(`/user/edit/${id}`, params)
  },
  // 删除用户
  delete: (id) => {
    return axios.post(`/user/delete/${id}`)
  },
  // 验证用户名合法性
  validateUsernameUnique: (username) => {
    return axios.post(`/user/validate-username/${username}/unique`)
  },
  // 验证邮箱合法性
  validateEmailUnique: (email) => {
    return axios.post(`/user/validate-email/${email}/unique`)
  },
  // 验证手机号码合法性
  validateMobileUnique: (mobile) => {
    return axios.post(`/user/validate-mobile/${mobile}/unique`)
  }
}
