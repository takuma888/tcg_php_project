<template>
  <section>
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" @selection-change="selectChange" style="width: 100%;">
      <el-table-column type="selection" width="36"></el-table-column>
      <el-table-column prop="id" type="index" label="ID #" width="60"></el-table-column>
      <el-table-column prop="username" label="用户名"></el-table-column>
      <el-table-column prop="email" label="邮箱"></el-table-column>
      <el-table-column prop="mobile" label="手机"></el-table-column>
      <el-table-column prop="create_at" label="创建"></el-table-column>
      <el-table-column prop="register_at" label="注册"></el-table-column>
      <el-table-column prop="login_at" label="登录"></el-table-column>
      <el-table-column prop="update_at" label="更新"></el-table-column>
      <el-table-column label="操作" width="150">
        <!--<template slot-scope="scope">-->
          <!--<el-button size="mini">编辑</el-button>-->
          <!--<el-button type="danger" size="mini">删除</el-button>-->
        <!--</template>-->
      </el-table-column>
    </el-table>
    <el-row>
      <el-col :span="24" style="margin-top: 10px;">
        <el-pagination @size-change="pageSizeChange"
                       @current-change="pageCurrentChange"
                       layout="total, sizes, prev, pager, next, jumper"
                       :current-page="page"
                       :page-sizes="sizes"
                       :page-size="size"
                       :total="dataTotal"
                       style="float:right;">
        </el-pagination>
      </el-col>
    </el-row>
  </section>
</template>

<script>
import Api from '@/api'
export default {
  data () {
    return {
      page: 1,
      size: 15,
      sizes: [15, 25, 50],
      tableLoading: false,
      tableData: [],
      dataTotal: 0,
      highlightCurrentRow: true,
      stripe: true,
      border: true,
      multipleSelection: []
    }
  },
  methods: {
    getData () {
      this.tableLoading = true
      Api.users.list({
        page: this.page,
        size: this.size
      }).then((data) => {
        this.tableLoading = false
        this.tableData = data.data
        this.dataTotal = data.total
      })
    },
    selectChange (val) {
      this.multipleSelection = val.map(item => parseInt(item.id))
    },
    pageSizeChange (val) {
      this.size = val
      this.getData()
    },
    pageCurrentChange (val) {
      this.page = val
      this.getData()
    }
  },
  mounted () {
    this.getData()
  }
}
</script>
