import axios from '@/api/axios'
import UsersApi from '@/api/users'
import UserApi from '@/api/user'

import RolesApi from '@/api/roles'
import RoleApi from '@/api/role'

import PermissionsApi from '@/api/permissions'

export default {
  // session
  session: () => {
    return axios.get('/session')
  },
  // 登录
  login: (username, password) => {
    return axios.post('/login', {
      username: username,
      password: password
    })
  },
  // 退出
  logout: () => {
    return axios.get('/logout')
  },
  // 测试
  test: () => {
    return axios.get('/test')
  },
  // users相关API
  users: UsersApi,
  // user 相关API
  user: UserApi,
  // roles 相关API
  roles: RolesApi,
  // role 相关API
  role: RoleApi,
  // permissions 相关API
  permissions: PermissionsApi
}
