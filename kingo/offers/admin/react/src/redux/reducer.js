import { combineReducers } from 'redux'
import * as type from './type'

const getAlertState = (state = {alerts: []}, action) => {
  switch (action.type) {
    case type.ALERT_ADD:
      state.alerts.push(action.data)
      return { ...state }
    case type.ALERT_REMOVE:
      delete state.alerts[action.data]
      return { ...state }
    default:
      return { ...state }
  }
}


const getNotificationState = (state = { notifications: [] }, action) => {
  switch (action.type) {
    case type.NOTIFICATION_ADD:
      state.notifications.push(action.data)
      return { ...state }
    case type.NOTIFICATION_REMOVE:
      delete state.notifications[action.data]
      return {...state}
    default:
      return { ...state }
  }
}


// const getAppState = (state = {}, action) => {
//   switch (action.type) {
//     case type.SET_APP_STATE:
//       return {...state, ...action.data}
//     case type.ADD_APP_STATE:
//       return {...state, ...action.data}
//     default:
//       return {...state}
//   }
// }
//
// const getTestState = (state = {a: []}, action) => {
//   switch (action.type) {
//     case type.SET_TEST_STATE:
//       state.a.push(action.data.a)
//       return {...state}
//     default:
//       return {...state}
//   }
// }

export default combineReducers({
  getAlertState,
  getNotificationState
  // getAppState,
  // getTestState
})