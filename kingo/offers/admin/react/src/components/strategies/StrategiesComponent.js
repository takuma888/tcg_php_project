import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles'

// ui component
import ReactTable from 'react-table'
import "react-table/react-table.css";
import checkboxHOC from "react-table/lib/hoc/selectTable"


// meterial ui component
import Toolbar from '@material-ui/core/Toolbar'
import Button from '@material-ui/core/Button'
import Add from '@material-ui/icons/Add'
import Clear from '@material-ui/icons/Clear'
import ExpandLess from '@material-ui/icons/ExpandLess'
import ExpandMore from '@material-ui/icons/ExpandMore'

// self component
import AddStrategyComponent  from './AddStrategyComponent'

// api
import Api from '../../api'

// redux
import { showAddStrategyDialog } from "../../redux/action"

const CheckboxTable = checkboxHOC(ReactTable);

const styles = theme => ({
  toolbar: {
    minHeight: "auto",
    marginBottom: "20px",
    padding: "0"
  },
})


function getData(originalData) {
  return originalData.map(item => {
    return {
      _id: item.id,
      ...item
    };
  });
}


class StrategiesComponent extends React.Component
{
  
  state = {
    data: [],
    pages: null,
    loading: false,
    lastRefresh: 1,
    tableState: {},
    selection: [],
    selectAll: false
  }
  
  fetchData = (state) => {
    if (!state) {
      state = this.state.tableState
    } else {
      this.setState({
        tableState: state
      })
    }
    
    this.setState({
      loading: true
    })
    this.props.apiListStrategies({
      page: state.page,
      size: state.pageSize,
      sort: state.sorted,
      filter: state.filtered
    }).then((data) => {
      this.setState({
        loading: false,
      })
      if (data) {
        this.setState({
          pages: Math.ceil(data.total / state.pageSize),
          data: getData(data.data)
        })
      }
    })
  }
  
  
  toggleSelection = (key, shift, row) => {
    // start off with the existing state
    let selection = [...this.state.selection]
    const keyIndex = selection.indexOf(key)
    // check to see if the key exists
    if (keyIndex >= 0) {
      // it does exist so we will remove it using destructing
      selection = [
        ...selection.slice(0, keyIndex),
        ...selection.slice(keyIndex + 1)
      ]
    } else {
      // it does not exist so add it
      selection.push(key)
    }
    // update the state
    this.setState({ selection })
  }
  
  toggleAll = () => {
    const selectAll = !this.state.selectAll
    const selection = []
    if (selectAll) {
      // we need to get at the internals of ReactTable
      const wrappedInstance = this.checkboxTable.getWrappedInstance()
      // the 'sortedData' property contains the currently accessible records based on the filter and sort
      const currentRecords = wrappedInstance.getResolvedState().sortedData
      // we just push all the IDs onto the selection array
      currentRecords.forEach(item => {
        selection.push(item._original._id)
      });
    }
    this.setState({ selectAll, selection })
  };
  
  isSelected = key => {
    return this.state.selection.includes(key)
  };
  
  
  showAddStrategyDialog = () => {
    this.props.showAddStrategyDialog()
  }
  
  showRemoveStrategyDialog = () => {
  
  }
  
  
  componentDidUpdate (prevProps, prevState, snapshot) {
    if (this.props.tableState.refresh !== this.state.lastRefresh) {
      this.setState({
        lastRefresh: this.props.tableState.refresh
      })
      this.fetchData()
    }
  }
  
  render () {
    const { classes } = this.props
    const { data, pages, loading, selectAll } = this.state
    const { toggleSelection, toggleAll, isSelected } = this
    const checkboxProps = {
      selectAll,
      isSelected,
      toggleSelection,
      toggleAll,
      selectType: "checkbox"
    }
    
    return (
      <section style={{margin: '0 20px'}}>
        <Toolbar className={classes.toolbar}>
          {this.state.selection.length > 0 ? <Button variant="contained" color="secondary" style={{marginRight: '20px'}} onClick={this.showRemoveStrategyDialog}>
            <Clear/>
            批量删除
          </Button> : ''}
          <Button variant="contained" color="primary" onClick={this.showAddStrategyDialog}>
            <Add />
            添加策略
          </Button>
        </Toolbar>
        <CheckboxTable
          ref={r => (this.checkboxTable = r)}
          noDataText='没有数据'
          columns={[
            {
              Header: 'ID',
              accessor: 'id',
              id: 'id',
              width: 200
            },
            {
              Header: '名称',
              id: 'name',
              accessor: 'name',
              width: 200
            },
            {
              Header: '描述',
              accessor: 'description',
              id: 'description',
              Filter: ({ filter, onChange }) => {}
            },
            {
              Header: '创建',
              accessor: 'create_at',
              id: 'create_at',
              Filter: ({ filter, onChange }) => {},
              width: 200
            },
            {
              Header: '更新',
              accessor: 'update_at',
              id: 'update_at',
              Filter: ({ filter, onChange }) => {},
              width: 200
            },
            {
              Header: '详细',
              expander: true,
              width: 65,
              Expander: ({ isExpanded, ...rest }) =>
                <div>
                  {isExpanded
                    ? <ExpandLess style={{marginTop: '3px'}} />
                    : <ExpandMore style={{marginTop: '3px'}} />}
                </div>,
              style: {
                cursor: "pointer",
                fontSize: 25,
                padding: "0",
                textAlign: "center",
                userSelect: "none"
              },
            }
          ]}
          manual
          data={data}
          pages={pages}
          loading={loading}
          onFetchData={this.fetchData}
          filterable
          defaultPageSize={20}
          className="-striped -highlight"
          style={{maxHeight: '700px'}}
          SubComponent={(row) => {
            const id = row.original.id
            let html = this.props.apiGetStrategy(id).then((data) => {
              let html = <div>
                aaaaaaaaaaaa<h1>bbbbbbb</h1>
              </div>
              return html
            }).catch(() => {})
            console.log(html)
            return <div>aaaa</div>
          }}
          {...checkboxProps}
        />
        <AddStrategyComponent/>
      </section>
    )
  }
}

StrategiesComponent.propTypes = {
  classes: PropTypes.object.isRequired
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
  apiGetStrategy: bindActionCreators(Api.strategy.get, dispatch),
  showAddStrategyDialog: bindActionCreators(showAddStrategyDialog, dispatch),
})

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(StrategiesComponent))
