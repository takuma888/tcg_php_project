import React from 'react'
// import PropTypes from "prop-types"
import { HashRouter, Switch, Route, Redirect } from 'react-router-dom'

// import withStyles from "@material-ui/core/styles/withStyles";

// core components
import HeaderComponent from '../components/header/HeaderComponent'
import HomeComponent from '../components/home/HomeComponent'
import OffersComponent from '../components/offers/OffersComponent'
import StatisticsComponent from '../components/statistics/StatisticsComponent'
import StrategyComponent from '../components/strategy/StrategyComponent'


class HomeLayout extends React.Component
{
  render () {
    return (
      <div>
        <HeaderComponent/>
        <div style={{marginTop: '50px'}}>
          <HashRouter>
            <Switch>
              <Route exact path="/home" component={HomeComponent}/>
              <Route path="/home/offers" component={OffersComponent}/>
              <Route path="/home/statistics" component={StatisticsComponent} />
              <Route path="/home/strategy" component={StrategyComponent} />
              <Redirect to="/404" />
            </Switch>
          </HashRouter>
        </div>
      </div>
    )
  }
}

// HomeLayout.propTypes = {
//   classes: PropTypes.object.isRequired
// }

// export default withStyles()(HomeLayout)
export default HomeLayout