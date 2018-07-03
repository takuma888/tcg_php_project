import AuthApi from '@/api/auth'
import OffersApi from '@/api/offers'
import StrategiesApi from '@/api/strategies'
import StrategyApoi from '@/api/strategy'

export default {
  session: () => {
    return AuthApi.get('/session')
  },
  login: (username, password) => {
    return AuthApi.post('/login', {
      username: username,
      password: password
    })
  },
  logout: () => {
    return AuthApi.get('/logout')
  },
  offers: OffersApi,
  strategies: StrategiesApi,
  strategy: StrategyApoi
}
