<template>
  <mu-container fluid>
    <mu-row>
      <mu-col span="12">
        <mu-paper :z-depth="1">
          <mu-data-table border selectable select-all stripe checkbox hover
                         :fit="tableFit"
                         height="500"
                         :loading="loading"
                         :selects.sync="selects"
                         :columns="columns"
                         :data="data">
            <template slot-scope="scope">
              <td>{{scope.row.id}}</td>
              <td>{{scope.row.source}}</td>
              <td>{{scope.row.offer_name}}</td>
              <td>{{scope.row.package_name}}</td>
              <td>{{scope.row.country}}</td>
              <td>{{scope.row.payout_type}}</td>
              <td>{{scope.row.payout}}</td>
              <td>&nbsp;</td>
            </template>
          </mu-data-table>
        </mu-paper>
        <mu-flex justify-content="end" style="padding: 16px;">
          <mu-pagination raised :total="pagination.total" :page-size="pagination.pageSize" :current.sync="pagination.current" @change="pager"></mu-pagination>
        </mu-flex>
      </mu-col>
    </mu-row>
  </mu-container>
</template>

<script>
export default {
  data () {
    return {
      columns: [
        { title: 'ID', name: 'id', width: 290 },
        { title: '来源', name: 'source', width: 150 },
        { title: '名称', name: 'offer_name' },
        { title: '包名', name: 'package_name' },
        { title: '国家', name: 'country', width: 80 },
        { title: '支付', name: 'payout_type', width: 80 },
        { title: '价值', name: 'payout' },
        { title: '操作', name: '' }
      ],
      selects: [],
      data: [],
      loading: false,
      pagination: {
        current: 1,
        total: 0,
        pageSize: 25
      },
      tableFit: false
    }
  },
  methods: {
    getData () {
      this.loading = true
      this.$api.offers.list({
        page: this.pagination.current,
        size: this.pagination.pageSize
      }).then((data) => {
        this.loading = false
        this.data = data.data
        this.pagination.total = data.total
        setTimeout(() => {
          document.getElementsByClassName('mu-table-header')[0].style.width = '100%'
          document.getElementsByClassName('mu-table-body')[0].style.width = '100%'
        }, 1000)
      })
    },
    pager (val) {
      this.pagination.current = val
      this.getData()
    }
  },
  mounted () {
    this.getData()
  }
}
</script>
