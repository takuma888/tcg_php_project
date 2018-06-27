import * as type from './type'

export const addAlert = (data) => ({
  type: type.ALERT_ADD,
  data
})

export const addNotification = (data) => ({
  type: type.NOTIFICATION_ADD,
  data
})

// export const setAppState = (data) => ({
//   type: type.SET_APP_STATE,
//   data
// })
//
// export const addAppState = (data) => ({
//   type: type.ADD_APP_STATE,
//   data
// })
//
// export const setTestState = (data) => ({
//   type: type.SET_TEST_STATE,
//   data
// })
