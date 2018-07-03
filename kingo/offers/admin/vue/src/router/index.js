import LoginComponent from '@/components/LoginComponent'
import NotFoundComponent from '@/components/NotFoundComponent'
import HomeComponent from '@/components/HomeComponent'

export default [
  {
    path: '/login',
    component: LoginComponent
  },
  {
    path: '/404',
    component: NotFoundComponent
  },
  {
    path: '/',
    component: HomeComponent
  },
  {
    path: '*',
    redirect: { path: '/404' }
  }
]
