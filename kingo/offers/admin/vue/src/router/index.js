import LoginComponent from '@/components/LoginComponent'
import NotFoundComponent from '@/components/NotFoundComponent'
import HomeComponent from '@/components/HomeComponent'
import OffersComponent from '@/components/offers/ListComponent'

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
    component: HomeComponent,
    children: [
      {
        path: 'offers',
        component: OffersComponent
      }
    ]
  },
  {
    path: '*',
    redirect: { path: '/404' }
  }
]
