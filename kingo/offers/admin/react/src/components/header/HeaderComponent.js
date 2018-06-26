import React from 'react'
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles'
import {withRouter} from 'react-router-dom'

import AppBar from '@material-ui/core/AppBar'
import Toolbar from '@material-ui/core/Toolbar'
import Button from '@material-ui/core/Button'
import Typography from '@material-ui/core/Typography'
import Tabs from '@material-ui/core/Tabs'
import Tab from '@material-ui/core/Tab'

import IconButton from '@material-ui/core/IconButton'
// import MenuIcon from '@material-ui/icons/Menu'
import AccountCircle from '@material-ui/icons/AccountCircle'


const styles = theme => ({
  toolbar: {
    minHeight: "auto"
  },
  title: {
    marginRight: "50px"
  },
  tabs: {
    flex: 1
  }
})


class HeaderComponent extends React.Component {

  state = {
    topNav: '/home'
  }

  handleTopNavChange = (event, value) => {
    const currentTopNav = this.state.topNav
    if (currentTopNav !== value) {
      this.setState({ topNav: value })
      this.props.history.push(value)
    }
  }

  componentDidMount () {
    let urlTopNav = '/home'
    if (this.props.location && this.props.location.pathname) {
      urlTopNav = this.props.location.pathname
    }
    if (urlTopNav !== '/home') {
      this.setState({ topNav: urlTopNav })
      // this.props.history.push(urlTopNav)
    }
  }

  topNavToHome = () => {
    const currentTopNav = this.state.topNav
    if (currentTopNav !== '/home') {
      this.setState({ topNav: '/home' })
      this.props.history.push('/home')
    }
  }

  render () {
    const { classes } = this.props
    const { topNav } = this.state
    return (
      <div>
        <AppBar position="fixed">
          <Toolbar className={classes.toolbar}>
            <Button className={classes.title} color="inherit" onClick={this.topNavToHome}>
              <Typography variant="headline" color="inherit">
                OFFER 总库
              </Typography>
            </Button>
            <Tabs value={topNav} className={classes.tabs} onChange={this.handleTopNavChange}>
              <Tab value="/home" style={{display: 'none'}}/>
              <Tab value="/home/offers" label="OFFER 库" />
              <Tab value="/home/statistics" label="统计" />
              <Tab value="/home/strategy" label="策略" />
            </Tabs>
            <div>
              <IconButton
                // aria-owns={open ? 'menu-appbar' : null}
                // aria-haspopup="true"
                // onClick={this.handleMenu}
                color="inherit"
              >
                <AccountCircle />
              </IconButton>
              {/*<Menu*/}
                {/*id="menu-appbar"*/}
                {/*anchorEl={anchorEl}*/}
                {/*anchorOrigin={{*/}
                  {/*vertical: 'top',*/}
                  {/*horizontal: 'right',*/}
                {/*}}*/}
                {/*transformOrigin={{*/}
                  {/*vertical: 'top',*/}
                  {/*horizontal: 'right',*/}
                {/*}}*/}
                {/*open={open}*/}
                {/*onClose={this.handleClose}*/}
              {/*>*/}
                {/*<MenuItem onClick={this.handleClose}>Profile</MenuItem>*/}
                {/*<MenuItem onClick={this.handleClose}>My account</MenuItem>*/}
              {/*</Menu>*/}
            </div>
          </Toolbar>
        </AppBar>
      </div>
    )
  }
}


HeaderComponent.propTypes = {
  classes: PropTypes.object.isRequired
}

export default withRouter(withStyles(styles)(HeaderComponent))