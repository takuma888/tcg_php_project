import Loginview from '@/views/LoginView'
import NotFoundview from '@/views/NotFoundView'
import Homeview from '@/views/Homeview'
import TestView from '@/views/home/TestView'
import UsersView from '@/views/home/UsersView'
import UsersListComponent from '@/components/users/UsersListComponent'
// import UsersTestComponent from '@/components/users/UsersTestComponent'

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
          // {
          //   path: 'test',
          //   component: UsersTestComponent
          // }
        ]
      },
      {
        path: 'test',
        component: TestView
      }
    ]
  },
  {
    path: '*',
    redirect: { path: '/404' }
  }
]
