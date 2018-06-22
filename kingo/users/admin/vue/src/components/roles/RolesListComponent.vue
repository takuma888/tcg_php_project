<template>
  <section>
    <el-form :inline="true" :model="searchForm">
      <el-form-item label="ID" size="small">
        <el-input type="text" v-model="searchForm.id" placeholder="ID"></el-input>
      </el-form-item>
      <el-form-item label="名称" size="small">
        <el-input type="text" v-model="searchForm.name" placeholder="名称"></el-input>
      </el-form-item>
      <el-form-item size="mini">
        <el-button type="primary" size="small" @click="handleSearch"><i class="fa fa-search"></i>&nbsp;&nbsp;查询</el-button>
        <RoleAddComponent v-if="add" size="small" type="success" @parent="getData"></RoleAddComponent>
      </el-form-item>
    </el-form>
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" @selection-change="selectChange" style="width: 100%;">
      <el-table-column type="selection" width="36"></el-table-column>
      <el-table-column prop="id" label="ID #" width="100"></el-table-column>
      <el-table-column prop="name" label="名称" width="150"></el-table-column>
      <el-table-column prop="priority" label="优先级" width="100"></el-table-column>
      <el-table-column prop="description" label="描述"></el-table-column>
      <el-table-column prop="create_at" label="创建" width="150"></el-table-column>
      <el-table-column label="操作" width="130">
        <template slot-scope="scope">
          <el-button v-if="edit" type="text" size="mini" @click="$refs.RoleEditComponent.showDialog(scope.row.id)">编辑</el-button>
          <el-button type="text" size="mini" @click="$refs.RolesPermissionsComponent.showDialog([scope.row.id])" style="color: green;">权限</el-button>
          <el-button type="text" size="mini" @click="singleDelete(scope.row)" style="color: red;">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-row>
      <el-button v-show="multipleSelection.length > 0" type="danger" size="mini" style="margin-top: 10px;" @click="multipleDelete">删除选中项</el-button>
      <el-button v-show="multipleSelection.length > 0" type="info" size="mini" style="margin-top: 10px;" @click="$refs.RolesPermissionsComponent.showDialog(multipleSelection)">设置选中项权限</el-button>
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
    <RoleEditComponent ref="RoleEditComponent" v-if="edit" @parent="getData"></RoleEditComponent>
    <RolesPermissionsComponent ref="RolesPermissionsComponent" @parent="getData"></RolesPermissionsComponent>
  </section>
</template>

<script>
import RoleAddComponent from '@/components/roles/RoleAddComponent'
import RoleEditComponent from '@/components/roles/RoleEditComponent'
import RolesPermissionsComponent from '@/components/roles/RolesPermissionsComponent'
import Api from '@/api'
export default {
  components: {
    RoleAddComponent: RoleAddComponent,
    RoleEditComponent: RoleEditComponent,
    RolesPermissionsComponent: RolesPermissionsComponent
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
        name: ''
      }
    }
  },
  methods: {
    selectChange (val) {
      this.multipleSelection = val.map(item => item.id)
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
    getData () {
      this.tableLoading = true
      Api.roles.list({
        page: this.page,
        size: this.size,
        id: this.searchForm.id,
        name: this.searchForm.name
      }).then((data) => {
        this.tableLoading = false
        this.tableData = data.data
        this.dataTotal = data.total
      }).catch(() => {
        this.tableLoading = false
      })
    },
    singleDelete (row) {
      if (row.id) {
        this.$confirm('确认删除选中的 ID # ' + row.id + ' 吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          Api.role.delete(row.id).then(() => {
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
          Api.roles.delete(this.multipleSelection).then(() => {
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

<style lang="scss" scoped></style>
