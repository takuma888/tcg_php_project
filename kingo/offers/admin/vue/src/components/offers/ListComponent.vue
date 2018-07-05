<template>
  <mu-container fluid>
    <mu-row>
      <mu-col span="12">
        <mu-paper :z-depth="1">
          <el-form inline :model="form" style="padding: 18px 10px 0 10px; background: #eef1f6;">
            <el-form-item size="small">
              <el-input type="text" v-model="form.id" placeholder="ID"></el-input>
            </el-form-item>
            <el-form-item size="small">
              <el-input type="text" v-model="form.source" placeholder="来源"></el-input>
            </el-form-item>
            <el-form-item size="small">
              <el-input type="text" v-model="form.offerName" placeholder="名称"></el-input>
            </el-form-item>
            <el-form-item size="small">
              <el-input type="text" v-model="form.packageName" placeholder="包名"></el-input>
            </el-form-item>
            <el-form-item size="small">
              <el-input type="text" v-model="form.country" placeholder="国家"></el-input>
            </el-form-item>
            <el-form-item size="mini">
              <mu-button color="primary" @click="search" small><i class="fa fa-search"></i>&nbsp;&nbsp;查询</mu-button>
            </el-form-item>
          </el-form>
          <mu-divider></mu-divider>
          <el-table :data="data"
                    highlight-current-row
                    max-height="680"
                    stripe
                    size="small" v-loading="loading" style="width: 100%;">
            <el-table-column prop="id" label="ID" width="250"></el-table-column>
            <el-table-column prop="source" label="来源" width="100"></el-table-column>
            <el-table-column prop="offer_name" label="名称"></el-table-column>
            <el-table-column prop="package_name" label="包名" width="250"></el-table-column>
            <el-table-column prop="country" label="国家" width="50"></el-table-column>
            <el-table-column prop="payout_type" label="支付" width="50"></el-table-column>
            <el-table-column prop="payout" label="价值" width="100"></el-table-column>
          </el-table>
          <mu-flex justify-content="end" style="padding: 10px;">
            <el-pagination @size-change="pageSizeChange"
                           @current-change="pageCurrentChange"
                           layout="total, sizes, prev, pager, next, jumper"
                           :current-page="page"
                           :page-sizes="sizes"
                           :page-size="size"
                           :total="total">
            </el-pagination>
          </mu-flex>
        </mu-paper>
      </mu-col>
    </mu-row>
  </mu-container>
</template>

<script>
export default {
  data () {
    return {
      data: [],
      loading: false,
      page: 1,
      size: 25,
      sizes: [15, 25, 50],
      total: 0,
      form: {
        id: '',
        source: '',
        offerName: '',
        packageName: '',
        country: '',
        payoutType: ''
      }
    }
  },
  methods: {
    getData () {
      this.loading = true
      this.$api.offers.list({
        page: this.page,
        size: this.size,
        id: this.form.id,
        source: this.form.source,
        name: this.form.offerName,
        package: this.form.packageName,
        country: this.form.country
      }).then((data) => {
        this.loading = false
        this.data = data.data
        this.total = data.total
      })
    },
    pageSizeChange (val) {
      this.size = val
      this.getData()
    },
    pageCurrentChange (val) {
      this.page = val
      this.getData()
    },
    search () {
      this.getData()
    }
  },
  mounted () {
    this.getData()
  }
}
</script>
