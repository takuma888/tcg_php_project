import React from 'react'
import ReactDOM from 'react-dom'

import { Provider } from 'react-redux'
import thunk from 'redux-thunk'
import { createStore, applyMiddleware } from 'redux'
import reducer from './redux/reducer'

import 'normalize.css'
import { HashRouter, Route, Switch, Redirect } from 'react-router-dom'


import App from './App'

import LoginLayout from './layouts/LoginLayout'
import NotFoundLayout from './layouts/NotFoundLayout'


import registerServiceWorker from './registerServiceWorker';
// import {addAlert} from "./redux/action";


const middleware = [thunk]
const store = createStore(reducer, applyMiddleware(...middleware))

ReactDOM.render((
    <Provider store={store}>
      <HashRouter>
        <Switch>
          <Route exact path="/" render={() => {
            const json = sessionStorage.getItem('user')
            if (json === 'undefined' || !json) {
              return <Redirect to="/login" />
            } else {
              console.log(json)
              const user = JSON.parse(json)
              return !user ? <Redirect to="/login" /> : <Redirect to="/home" />
            }
          }} />
          <Route path="/home" component={() => {
            const json = sessionStorage.getItem('user')
            if (json === 'undefined' || !json) {
              return <Redirect to="/login" />
            } else {
              const user = JSON.parse(json)
              return !user ? <Redirect to="/login" /> : <App />
            }
          }} />
          <Route path="/login" component={LoginLayout} />
          <Route path="/404" component={NotFoundLayout} />
          <Redirect to="/404" />
        </Switch>
      </HashRouter>
    </Provider>
  ), document.getElementById('root')
)


registerServiceWorker();
