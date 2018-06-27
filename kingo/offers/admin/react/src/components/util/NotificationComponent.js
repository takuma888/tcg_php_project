import React from 'react'
import PropTypes from 'prop-types'
import classNames from 'classnames'

import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { removeNotification } from "../../redux/action";
// material ui component
import SuccessIcon from '@material-ui/icons/CheckCircle'
import ErrorIcon from '@material-ui/icons/Error'
import InfoIcon from '@material-ui/icons/Info'
import WarningIcon from '@material-ui/icons/Warning'
import CloseIcon from '@material-ui/icons/Close'
import green from '@material-ui/core/colors/green'
import amber from '@material-ui/core/colors/amber'
import IconButton from '@material-ui/core/IconButton'
import Snackbar from '@material-ui/core/Snackbar'
import SnackbarContent from '@material-ui/core/SnackbarContent'
import {withStyles} from "@material-ui/core/styles/index";

const variantIcon = {
  success: SuccessIcon,
  warning: WarningIcon,
  error: ErrorIcon,
  info: InfoIcon
}

const styles = theme => ({
  success: {
    backgroundColor: green[600]
  },
  error: {
    backgroundColor: theme.palette.error.dark
  },
  info: {
    backgroundColor: theme.palette.primary.dark
  },
  warning: {
    backgroundColor: amber[700]
  },
  icon: {
    fontSize: 20
  },
  iconVariant: {
    opacity: 0.9,
    marginRight: theme.spacing.unit
  },
  message: {
    display: 'flex',
    alignItems: 'center'
  }
})


function NotificationSnackBarContent(props) {
  const { title, classes, className, message, onClose, variant, ...other } = props
  const Icon = variantIcon[variant]

  return (
    <SnackbarContent
      className={classNames(classes[variant], className)}
      aria-describedby="client-snackbar"
      message={
        <div>
          <h2 style={{marginTop: '0', verticalAlign: 'text-top'}}><Icon className={classNames(classes.icon, classes.iconVariant)} style={{fontSize: '1.2em', verticalAlign: 'bottom'}} />{title}</h2>
          <span id="client-snackbar" className={classes.message}>
              {message}
          </span>
        </div>
      }
      action={[
        <IconButton
          key="close"
          aria-label="Close"
          color="inherit"
          className={classes.close}
          onClick={onClose}
        >
          <CloseIcon className={classes.icon} />
        </IconButton>,
      ]}
      {...other}
    />
  )
}

NotificationSnackBarContent.propTypes = {
  classes: PropTypes.object.isRequired,
  className: PropTypes.string,
  title: PropTypes.node,
  message: PropTypes.node,
  onClose: PropTypes.func,
  variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired
}


const NotificationSnackbarContentWrapper = withStyles(styles)(NotificationSnackBarContent)

class NotificationComponent extends React.Component {
  state = {
    open: true
  }

  handleClose = (event, reason) => {
    if (reason === 'clickaway') {
      return
    }

    this.setState({ open: false })
    this.props.removeNotification(this.props.id)
  }

  render () {
    const { title, message, onClose, variant, vertical, horizontal } = this.props

    return (
      <Snackbar
        anchorOrigin={{
          vertical: vertical || 'top',
          horizontal: horizontal || 'right',
        }}
        open={this.state.open}
        autoHideDuration={3000}
        onClose={onClose || this.handleClose}
      >
        <NotificationSnackbarContentWrapper
          onClose={onClose || this.handleClose}
          variant={variant}
          title={title}
          message={message}
        />
      </Snackbar>
    )
  }
}

NotificationComponent.propTypes = {
  id: PropTypes.node,
  title: PropTypes.node,
  message: PropTypes.node,
  onClose: PropTypes.func,
  variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired
}


const mapStateToProps = state => {
  return {}
}
const mapDispatchToProps = dispatch => ({
  removeNotification: bindActionCreators(removeNotification, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(NotificationComponent)