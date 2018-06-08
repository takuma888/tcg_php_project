import Loginview from '@/views/LoginView'
import NotFoundview from '@/views/NotFoundView'
import Homeview from '@/views/Homeview'
import UsersListview from '@/views/users/UsersListView'
import UsersAsideView from '@/views/users/UsersAsideView'

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
        components: {
          default: UsersListview,
          aside: UsersAsideView
        }
      }
    ]
  },
  {
    path: '*',
    redirect: { path: '/404' }
  }
]
