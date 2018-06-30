import * as type from './type'

export const addAlert = (data) => ({
  type: type.ALERT_ADD,
  data
})

export const addNotification = (data) => ({
  type: type.NOTIFICATION_ADD,
  data
})

export const removeAlert = (data) => ({
  type: type.ALERT_REMOVE,
  data
})

export const removeNotification = (data) => ({
  type: type.NOTIFICATION_REMOVE,
  data
})


export const showAddStrategyDialog = () => ({
  type: type.SHOW_STRATEGY_ADD_DIALOG,
  open: true
})

export const hideAddStrategyDialog = () => ({
  type: type.HIDE_STRATEGY_ADD_DIALOG,
  open: false
})

export const refreshStrategiesTable = () => ({
  type: type.REFRESH_STRATEGIES_TABLE
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
