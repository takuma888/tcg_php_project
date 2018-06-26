import React from 'react'
import PropTypes from 'prop-types';
import {withRouter} from 'react-router-dom'
import { withStyles } from '@material-ui/core/styles'
import Card from '@material-ui/core/Card'
import CardHeader from '@material-ui/core/CardHeader'
import CardActions from '@material-ui/core/CardActions'
import CardContent from '@material-ui/core/CardContent'
import FormGroup from '@material-ui/core/FormGroup'
import Input from '@material-ui/core/Input'
import InputLabel from '@material-ui/core/InputLabel'
import FormHelperText from '@material-ui/core/FormHelperText'
import FormControl from '@material-ui/core/FormControl'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import Checkbox from '@material-ui/core/Checkbox'
import Button from '@material-ui/core/Button'

const styles = theme => ({
  root: {
    width: '400px',
    margin: "100px auto"
  },
  formControl: {
    width: '100%'
  },
  actions: {
    justifyContent: "space-around"
  }
})

class LoginLayout extends React.Component {

  state = {
    username: '',
    usernameText: '',
    usernameError: false,
    password: '',
    passwordText: '',
    passwordError: false,
    remember: false
  }

  handleUsernameChange = event => {
    this.setState({ username: event.target.value })
  }

  handlePasswordChange = event => {
    this.setState({ password: event.target.value })
  }

  handleRememberChange = event => {
    this.setState({ remember: !this.state.remember })
  }

  handleSubmit = event => {
    this.props.history.push('/home')
  }

  render () {
    const { classes } = this.props
    return (
      <form className={classes.root}>
        <Card>
          <CardHeader title="管理员登录" />
          <CardContent>
            <FormGroup row>
              <FormControl className={classes.formControl} error={this.state.usernameError} aria-describedby="username-text">
                <InputLabel htmlFor="username">用户名</InputLabel>
                <Input id="username" name="username" autoComplete="off" value={this.state.username} onChange={this.handleUsernameChange} />
                <FormHelperText id="username-text">{this.state.usernameText}</FormHelperText>
              </FormControl>
            </FormGroup>

            <FormGroup row>
              <FormControl className={classes.formControl} error={this.state.passwordError} aria-describedby="password-text">
                <InputLabel htmlFor="password">密码</InputLabel>
                <Input id="password" type="password" name="password" value={this.state.password} onChange={this.handlePasswordChange} />
                <FormHelperText id="password-text">{this.state.passwordText}</FormHelperText>
              </FormControl>
            </FormGroup>
          </CardContent>
          <CardActions className={classes.actions}>
            <FormGroup>
              <FormControlLabel
                control={
                  <Checkbox checked={this.state.remember} onChange={this.handleRememberChange} color="primary" />
                }
                label="自动登录"
              />
            </FormGroup>
            <FormGroup style={{float: "right"}}>
              <Button variant="contained" color="primary" onClick={this.handleSubmit}>登录</Button>
            </FormGroup>
          </CardActions>
        </Card>
      </form>
    )
  }
}

LoginLayout.propTypes = {
  classes: PropTypes.object.isRequired,
}

export default withRouter(withStyles(styles)(LoginLayout))