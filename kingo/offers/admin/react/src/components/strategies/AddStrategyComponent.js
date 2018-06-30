import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { hideAddStrategyDialog } from "../../redux/action"

// material ui
import { withStyles } from '@material-ui/core/styles'
import Button from '@material-ui/core/Button'
import Dialog from '@material-ui/core/Dialog'
import DialogActions from '@material-ui/core/DialogActions'
import DialogContent from '@material-ui/core/DialogContent'
import DialogTitle from '@material-ui/core/DialogTitle'

import FormGroup from '@material-ui/core/FormGroup'
import Input from '@material-ui/core/Input'
import InputLabel from '@material-ui/core/InputLabel'
import FormHelperText from '@material-ui/core/FormHelperText'
import FormControl from '@material-ui/core/FormControl'

// api
import Api from '../../api'

// redux
import { refreshStrategiesTable } from '../../redux/action'

const styles = theme => ({
  formControl: {
    width: '100%'
  }
})

class AddStrategyComponent extends React.Component
{
  
  state = {
    name: '',
    nameText: '',
    nameError: false,
    description: '',
    descriptionText: '',
    priority: 0,
    priorityText: ''
  }
  
  handleClose = () => {
    this.props.hideAddStrategyDialog()
  }
  
  handleSubmit = () => {
    const { name, description, priority } = this.state
    this.props.apiStrategyAdd(name, description, priority).then(() => {
      // 清空表单
      this.setState({
        name: '',
        nameText: '',
        nameError: false,
        description: '',
        descriptionText: '',
        priority: 0,
        priorityText: ''
      })
      this.props.hideAddStrategyDialog()
      // 刷新列表
      this.props.refreshStrategiesTable()
    }).catch(() => {})
  }
  
  handleNameChange = (event) => {
    this.setState({
      name: event.target.value
    })
  }
  
  handleDescriptionChange = (event) => {
    this.setState({
      description: event.target.value
    })
  }
  
  handlePriorityChange = (event) => {
    this.setState({
      priority: event.target.value
    })
  }
  
  render () {
    const { classes } = this.props
    return (
      <Dialog
        open={this.props.dialogState.open}
        onClose={this.handleClose}
        aria-labelledby="responsive-dialog-title"
        fullWidth={true}
        maxWidth="md"
      >
        <DialogTitle id="responsive-dialog-title">添加策略</DialogTitle>
        <DialogContent>
          <FormGroup row>
            <FormControl className={classes.formControl} error={this.state.nameError} aria-describedby="name-text">
              <InputLabel htmlFor="name">名称</InputLabel>
              <Input id="name" name="name" autoComplete="off" value={this.state.name} onChange={this.handleNameChange} />
              <FormHelperText id="name-text">{this.state.nameText}</FormHelperText>
            </FormControl>
          </FormGroup>
          <FormGroup row>
            <FormControl className={classes.formControl} aria-describedby="description-text">
              <InputLabel htmlFor="description">描述</InputLabel>
              <Input id="description" name="description" multiline rows={3} autoComplete="off" value={this.state.description} onChange={this.handleDescriptionChange} />
              <FormHelperText id="description-text">{this.state.descriptionText}</FormHelperText>
            </FormControl>
          </FormGroup>
          <FormGroup row>
            <FormControl className={classes.formControl} aria-describedby="priority-text">
              <InputLabel htmlFor="priority">优先级权重</InputLabel>
              <Input id="priority" name="priority" autoComplete="off" value={this.state.priority} onChange={this.handlePriorityChange} />
              <FormHelperText id="priority-text">{this.state.priorityText}</FormHelperText>
            </FormControl>
          </FormGroup>
        </DialogContent>
        <DialogActions>
          <Button onClick={this.handleClose}>取消</Button>
          <Button onClick={this.handleSubmit} color="primary" variant="contained" autoFocus>提交</Button>
        </DialogActions>
      </Dialog>
    )
  }
}

const mapStateToProps = state => {
  const { getStrategyAddDialogState } = state
  return {
    dialogState: getStrategyAddDialogState
  }
}
const mapDispatchToProps = dispatch => ({
  hideAddStrategyDialog: bindActionCreators(hideAddStrategyDialog, dispatch),
  refreshStrategiesTable: bindActionCreators(refreshStrategiesTable, dispatch),
  apiStrategyAdd: bindActionCreators(Api.strategy.add, dispatch)
})


export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(AddStrategyComponent))