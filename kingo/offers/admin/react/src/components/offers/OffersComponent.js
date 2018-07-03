import React from 'react'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'

// ui component
import ReactTable from 'react-table'
import "react-table/react-table.css";

// api
import Api from '../../api'

class OffersComponent extends React.Component {

  state = {
    data: [],
    pages: null,
    loading: false
  }
  
  
  fetchData = (state) => {
    this.setState({
      loading: true
    })
    this.props.apiOffers({
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
          data: data.data
        })
      }
    })
  }

  render () {
    const { data, pages, loading } = this.state
    return (
        <section style={{margin: '0 20px'}}>
          <ReactTable
            noDataText='没有数据'
            columns={[
              {
                Header: 'ID',
                accessor: 'id',
                id: 'id'
              },
              {
                Header: '来源',
                id: 'source',
                accessor: 'source',
                width: 150
              },
              {
                Header: '名称',
                accessor: 'offer_name',
                id: 'offer_name'
              },
              {
                Header: '包名',
                accessor: 'package_name',
                id: 'package_name'
              },
              {
                Header: '国家',
                accessor: 'country',
                id: 'country',
                width: 100
              },
              {
                Header: '支付类型',
                accessor: 'payout_type',
                id: 'payout_type',
                width: 100
              },
              {
                Header: '价值',
                accessor: 'payout',
                id: 'payout',
                width: 100
              }
            ]}
            manual
            data={data}
            pages={pages}
            loading={loading}
            onFetchData={this.fetchData}
            filterable
            defaultPageSize={50}
            className="-striped -highlight"
            style={{maxHeight: '800px'}}
          />
        </section>
    )
  }
}

const mapStateToProps = state => {
    const { getAlertState, getNotificationState } = state
    return {
        alertState: getAlertState,
        notificationState: getNotificationState
    }
}
const mapDispatchToProps = dispatch => ({
    apiOffers: bindActionCreators(Api.offers.list, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(OffersComponent)