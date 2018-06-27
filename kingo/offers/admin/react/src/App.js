import React from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { addAlert } from "./redux/action";
// custom ui components
import AlertComponent from './components/util/AlertComponent'
import NotificationComponent from './components/util/NotificationComponent'
import HomeLayout from './layouts/HomeLayout'
// api
import Api from './api'


class App extends React.Component {

  componentWillMount () {
    // sessionStorage.removeItem('user')
    const user = JSON.parse(sessionStorage.getItem('user'))
    if (!user && this.props.location.pathname !== '/login') {
      this.props.apiSession().then((data) => {
        if (!data.user) {
          addAlert({
            message: '请重新登录',
            type: 'warning'
          })
          this.props.history.push('/login')
        } else {
          sessionStorage.setItem('user', JSON.stringify(data.user))
        }
      })
    }
  }
  componentDidUpdate (prevProps, prevState, snapshot) {
  }
  componentDidMount () {
  }
  render () {
    const { alerts } = this.props.alertState
    const { notifications } = this.props.notificationState
    return (
      <div>
        <HomeLayout/>
        {alerts && alerts.map((val, key) => {
          return <AlertComponent variant={val.type} message={val.message} vertical={val.vertical} horizontal={val.horizontal} key={key} id={key}/>
        })}
        {notifications && notifications.map((val, key) => {
          return <NotificationComponent variant={val.type} title={val.title} message={val.message} key={key} id={key}/>
        })}
      </div>
    )
  }
}


const mapStateToProps = state => {
  const { getAlertState, getNotificationState } = state
  return {
    alertState: getAlertState,
    notificationState: getNotificationState
  }
}
const mapDispatchToProps = dispatch => ({
  apiSession: bindActionCreators(Api.auth.session, dispatch),
  addAlert: bindActionCreators(addAlert, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(App)

