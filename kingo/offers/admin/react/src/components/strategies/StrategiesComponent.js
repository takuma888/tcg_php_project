import React from 'react'
import classNames from 'classnames'
import PropTypes from 'prop-types'
import { withStyles} from '@material-ui/core/styles'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'

// material ui component
import Table from '@material-ui/core/Table'
import TableHead from '@material-ui/core/TableHead'
import TableBody from '@material-ui/core/TableBody'
import TableRow from '@material-ui/core/TableRow'
import TableCell from '@material-ui/core/TableCell'
import TablePagination from '@material-ui/core/TablePagination'

import Toolbar from '@material-ui/core/Toolbar'
import Typography from '@material-ui/core/Typography'
import Paper from '@material-ui/core/Paper'
import Checkbox from '@material-ui/core/Checkbox'
import IconButton from '@material-ui/core/IconButton'
import Tooltip from '@material-ui/core/Tooltip'
import DeleteIcon from '@material-ui/icons/Delete'
import { lighten} from '@material-ui/core/styles/colorManipulator'

import Button from '@material-ui/core/Button'
import Add from '@material-ui/icons/Add'


// self component
import AddStrategyComponent  from './AddStrategyComponent'

// api
import Api from '../../api'

// redux
import { showAddStrategyDialog } from "../../redux/action"


// table head
class EnhancedTableHead extends React.Component
{
  render () {
    const { onSelectAllClick, numSelected, rowCount } = this.props

    return (
      <TableHead>
        <TableRow>
          <TableCell padding="checkbox">
            <Checkbox
              indeterminate={numSelected > 0 && numSelected < rowCount}
              checked={numSelected === rowCount && rowCount !== 0}
              onChange={onSelectAllClick}
            />
          </TableCell>
          <TableCell>ID</TableCell>
          <TableCell>名称</TableCell>
          <TableCell>描述</TableCell>
          <TableCell>创建</TableCell>
          <TableCell>更新</TableCell>
          <TableCell>操作</TableCell>
        </TableRow>
      </TableHead>
    )
  }
}


EnhancedTableHead.propTypes = {
  numSelected: PropTypes.number.isRequired,
  onRequestSort: PropTypes.func.isRequired,
  onSelectAllClick: PropTypes.func.isRequired,
  // order: PropTypes.string.isRequired,
  // orderBy: PropTypes.string.isRequired,
  rowCount: PropTypes.number.isRequired,
}

// table
const styles = theme => ({
  root: {
    width: '100%',
    marginTop: theme.spacing.unit * 3,
  },
  table: {
    minWidth: 1020,
  },
  tableWrapper: {
    overflowX: 'auto',
  },
  toolbar: {
    paddingRight: theme.spacing.unit,
  },
  highlight:
    theme.palette.type === 'light'
      ? {
        color: theme.palette.secondary.main,
        backgroundColor: lighten(theme.palette.secondary.light, 0.85),
      }
      : {
        color: theme.palette.text.primary,
        backgroundColor: theme.palette.secondary.dark,
      },
  spacer: {
    flex: '1 1 100%',
  },
  actions: {
    color: theme.palette.text.secondary,
  },
  title: {
    flex: '0 0 auto',
  }
})

class EnhancedTable extends React.Component
{

  state = {
    selected: [],
    data: [],
    page: 0,
    pageSize: 25,
    lastRefresh: 1,
    tableState: {},
  }



  componentWillMount () {
    this.fetchData(this.state)
  }

  fetchData = (state) => {
    if (!state) {
      state = this.state.tableState
    } else {
      this.setState({
        tableState: state
      })
    }

    this.props.apiListStrategies({
      page: state.page,
      size: state.pageSize,
      sort: state.sorted,
      filter: state.filtered
    }).then((data) => {
      if (data) {
        this.setState({
          data: data.data
        })
      }
    })
  }

  showAddStrategyDialog = () => {
    this.props.showAddStrategyDialog()
  }

  showRemoveStrategyDialog = () => {

  }

  handleRequestSort = (event, property) => {
    const orderBy = property;
    let order = 'desc';

    if (this.state.orderBy === property && this.state.order === 'desc') {
      order = 'asc';
    }

    this.setState({ order, orderBy });
  }

  handleSelectAllClick = (event, checked) => {
    if (checked) {
      this.setState(state => ({ selected: state.data.map(n => n.base.id) }));
      return;
    }
    this.setState({ selected: [] });
  }

  handleClick = (event, id) => {
    const { selected } = this.state;
    const selectedIndex = selected.indexOf(id);
    let newSelected = [];

    if (selectedIndex === -1) {
      newSelected = newSelected.concat(selected, id);
    } else if (selectedIndex === 0) {
      newSelected = newSelected.concat(selected.slice(1));
    } else if (selectedIndex === selected.length - 1) {
      newSelected = newSelected.concat(selected.slice(0, -1));
    } else if (selectedIndex > 0) {
      newSelected = newSelected.concat(
        selected.slice(0, selectedIndex),
        selected.slice(selectedIndex + 1),
      );
    }

    this.setState({ selected: newSelected });
  }

  handleChangePage = (event, page) => {
    this.setState({ page });
  }

  handleChangeRowsPerPage = event => {
    this.setState({ pageSize: event.target.value });
  }

  isSelected = id => this.state.selected.indexOf(id) !== -1

  render () {
    const { classes } = this.props
    const { data, selected, pageSize, page } = this.state

    return (
      <Paper className={classes.root}>
        <Toolbar
          className={classNames(classes.toolbar, {
            [classes.highlight]: selected.length > 0,
          })}
        >
          <div className={classes.title}>
            {selected.length > 0 ? (
              <Typography color="inherit" variant="subheading">
                选择了 {selected.length} 行
              </Typography>
            ) : (
              <Button variant="contained" color="primary" onClick={this.showAddStrategyDialog}>
                <Add />
                添加策略
              </Button>
            )}
          </div>
          <div className={classes.spacer} />
          <div className={classes.actions}>
            {selected.length > 0 ? (
              <Tooltip title="删除">
                <IconButton aria-label="删除">
                  <DeleteIcon />
                </IconButton>
              </Tooltip>
            ) : ''}
          </div>
        </Toolbar>
        <div className={classes.tableWrapper}>
          <Table className={classes.table} aria-labelledby="tableTitle">
            <TableHead>
              <TableRow>
                <TableCell padding="checkbox">
                  <Checkbox
                    indeterminate={selected.length > 0 && selected.length < data.length}
                    checked={selected.length === data.length && data.length !== 0}
                    onChange={this.handleSelectAllClick}
                  />
                </TableCell>
                <TableCell>ID</TableCell>
                <TableCell>名称</TableCell>
                <TableCell>描述</TableCell>
                <TableCell>创建</TableCell>
                <TableCell>更新</TableCell>
                <TableCell>操作</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {data.map(n => {
                const isSelected = this.isSelected(n.base.id)
                return (
                  <TableRow
                    hover
                    onClick={event => this.handleClick(event, n.base.id)}
                    role="checkbox"
                    aria-checked={isSelected}
                    tabIndex={-1}
                    key={n.base.id}
                    selected={isSelected}>
                    <TableCell padding="checkbox">
                      <Checkbox checked={isSelected} />
                    </TableCell>
                    <TableCell component="th">{n.base.id}</TableCell>
                    <TableCell>{n.base.name}</TableCell>
                    <TableCell>{n.base.description}</TableCell>
                    <TableCell>{n.base.create_at}</TableCell>
                    <TableCell>{n.base.update_at}</TableCell>
                    <TableCell>{''}</TableCell>
                  </TableRow>
                )
              })}
              {data.length === 0 && (
                <TableRow style={{ padding: '10px' }}>
                  <TableCell colSpan={7} style={{textAlign: 'center'}}>没有数据</TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>
        <TablePagination
          component="div"
          count={data.length}
          rowsPerPage={pageSize}
          page={page}
          backIconButtonProps={{
            'aria-label': 'Previous Page',
          }}
          nextIconButtonProps={{
            'aria-label': 'Next Page',
          }}
          onChangePage={this.handleChangePage}
          onChangeRowsPerPage={this.handleChangeRowsPerPage}
        />
        <AddStrategyComponent/>
      </Paper>
    )
  }
}

EnhancedTable.propTypes = {
  classes: PropTypes.object.isRequired,
}


const mapStateToProps = state => {
  const { getAlertState, getNotificationState, getStrategyAddDialogState, getStrategiesTableState } = state
  return {
    alertState: getAlertState,
    notificationState: getNotificationState,
    addStrategyDialogState : getStrategyAddDialogState,
    tableState: getStrategiesTableState
  }
}
const mapDispatchToProps = dispatch => ({
  apiListStrategies: bindActionCreators(Api.strategies.list, dispatch),
  showAddStrategyDialog: bindActionCreators(showAddStrategyDialog, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(EnhancedTable))