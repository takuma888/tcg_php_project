import Loginview from '@/views/LoginView'
import NotFoundview from '@/views/NotFoundView'
import Homeview from '@/views/Homeview'
import UsersListview from '@/views/users/UsersListview'

export default [
  {
    path: '/login',
    component: Loginview
  },
  {
    path: '/404',
    component: NotFoundview
  },
  {
    path: '/',
    component: Homeview,
    children: [
      {
        path: '/users',
        component: UsersListview,
        children: [
          {
            path: '/users',
            component: UsersListview
          }
        ]
      }
    ]
  },
  {
    path: '*',
    redirect: { path: '/404' }
  }
]
