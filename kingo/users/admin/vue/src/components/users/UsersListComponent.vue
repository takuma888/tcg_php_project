<template>
  <section>
    <el-form :inline="true" :model="searchForm">
      <el-form-item label="ID" size="small">
        <el-input type="text" v-model="searchForm.id" placeholder="ID"></el-input>
      </el-form-item>
      <el-form-item label="用户名" size="small">
        <el-input type="text" v-model="searchForm.username" placeholder="用户名"></el-input>
      </el-form-item>
      <el-form-item label="邮箱" size="small">
        <el-input type="text" v-model="searchForm.email" placeholder="邮箱地址"></el-input>
      </el-form-item>
      <el-form-item label="手机" size="small">
        <el-input type="text" v-model="searchForm.mobile" placeholder="手机号码"></el-input>
      </el-form-item>
      <el-form-item label="创建" size="small">
        <el-date-picker v-model="searchForm.createAt"
                        type="daterange"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        format="yyyy 年 MM 月 dd 日"
                        value-format="yyyy-MM-dd"></el-date-picker>
      </el-form-item>
      <el-form-item label="登录" size="small">
        <el-date-picker v-model="searchForm.loginAt"
                        type="daterange"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        format="yyyy 年 MM 月 dd 日"
                        value-format="yyyy-MM-dd"></el-date-picker>
      </el-form-item>
      <el-form-item size="mini">
        <el-button type="primary" size="small" @click="handleSearch"><i class="fa fa-search"></i>&nbsp;&nbsp;查询</el-button>
        <UserAddComponent v-if="add" size="small" type="success" @parent="getData"></UserAddComponent>
      </el-form-item>
    </el-form>
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" @selection-change="selectChange" style="width: 100%;">
      <el-table-column type="expand" label="#">
        <template slot-scope="props">
          <el-form label-position="left" inline class="demo-table-expand">
            <el-form-item label="昵称:">
              <span>{{ props.row.nickname }}</span>
            </el-form-item>
            <el-form-item label="QQ:">
              <span>{{ props.row.qq }}</span>
            </el-form-item>
            <el-form-item label="微信:">
              <span>{{ props.row.wei_xin }}</span>
            </el-form-item>
            <el-form-item label="更新:">
              <span>{{ props.row.profile_update_at }}</span>
            </el-form-item>
            <!--<el-form-item label="头像:">-->
              <!--<span></span>-->
            <!--</el-form-item>-->
            <el-form-item label="角色:">
              <strong v-for="(role, key) in props.row.roles" :key="key" style="color: blue;">&nbsp;{{ role.name }}&nbsp;</strong>
              <el-button type="text" size="mini" @click="$refs.UserEditRoleComponent.showDialog(props.row.id)" style="color: red;">修改</el-button>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column type="selection" width="42"></el-table-column>
      <el-table-column prop="id" label="ID" width="60"></el-table-column>
      <el-table-column prop="username" label="用户名"></el-table-column>
      <el-table-column prop="email" label="邮箱"></el-table-column>
      <el-table-column prop="mobile" label="手机"></el-table-column>
      <el-table-column prop="create_at" label="创建" width="150"></el-table-column>
      <el-table-column prop="login_at" label="登录" width="150"></el-table-column>
      <el-table-column label="操作" width="90">
        <template slot-scope="scope">
          <el-button v-if="edit" type="text" size="mini" @click="$refs.UserEditComponent.showDialog(scope.row.id)">编辑</el-button>
          <el-button type="text" size="mini" @click="singleDelete(scope.row)" style="color: red;">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-row>
      <el-button v-show="multipleSelection.length > 0" type="danger" size="mini" style="margin-top: 10px;" @click="multipleDelete">删除选中项</el-button>
      <el-col :span="12" style="margin-top: 10px; float: right;">
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
    <UserEditComponent ref="UserEditComponent" v-if="edit" @parent="getData"></UserEditComponent>
    <UserEditRoleComponent ref="UserEditRoleComponent" @parent="getData"></UserEditRoleComponent>
  </section>
</template>

<script>
import Api from '@/api'
import UserAddComponent from '@/components/users/UserAddComponent'
import UserEditComponent from '@/components/users/UserEditComponent'
import UserEditRoleComponent from '@/components/users/UserEditRoleComponent'
export default {
  components: {
    UserAddComponent: UserAddComponent,
    UserEditComponent: UserEditComponent,
    UserEditRoleComponent: UserEditRoleComponent
  },
  props: {
    add: {
      default: false
    },
    edit: {
      default: false
    }
  },
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
      multipleSelection: [],
      searchForm: {
        id: '',
        username: '',
        email: '',
        mobile: '',
        createAt: [],
        loginAt: []
      }
    }
  },
  methods: {
    getData () {
      this.tableLoading = true
      Api.users.list({
        page: this.page,
        size: this.size,
        id: this.searchForm.id,
        username: this.searchForm.username,
        email: this.searchForm.email,
        mobile: this.searchForm.mobile,
        create_at: this.searchForm.createAt,
        login_at: this.searchForm.loginAt
      }).then((data) => {
        this.tableLoading = false
        this.tableData = data.data
        this.dataTotal = data.total
      }).catch(() => {
        this.tableLoading = false
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
    },
    handleSearch () {
      this.getData()
    },
    singleDelete (row) {
      if (row.id) {
        this.$confirm('确认删除选中的 ID # ' + row.id + ' 吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          Api.user.delete(row.id).then(() => {
            this.getData()
          })
        }).catch(() => {})
      }
    },
    multipleDelete () {
      if (this.multipleSelection.length) {
        this.$confirm('确认删除选中的 ' + this.multipleSelection.length + ' 项吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          Api.users.delete(this.multipleSelection).then(() => {
            this.getData()
          })
        }).catch(() => {})
      } else {
        this.$message({
          message: '请选择要删除的行',
          type: 'error'
        })
      }
    }
  },
  mounted () {
    this.getData()
  }
}
</script>

<style lang="scss">
  .demo-table-expand {
    .el-form-item {
      margin-right: 0;
      margin-bottom: 0;
      width: 50%;
      float: left;
    }
    label {
      width: 50px;
      color: #99a9bf;
    }
  }
</style>
