import Loginview from '@/views/LoginView'
import NotFoundview from '@/views/NotFoundView'
import Homeview from '@/views/Homeview'
import UsersView from '@/views/home/UsersView'
import RolesView from '@/views/home/RolesView'
import UsersListComponent from '@/components/users/UsersListComponent'
import RolesListComponent from '@/components/roles/RolesListComponent'

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
        path: 'users',
        component: UsersView,
        children: [
          {
            path: '/',
            component: UsersListComponent
          }
        ]
      },
      {
        path: 'roles',
        component: RolesView,
        children: [
          {
            path: '/',
            component: RolesListComponent
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
