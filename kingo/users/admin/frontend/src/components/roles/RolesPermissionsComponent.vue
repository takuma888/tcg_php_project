<template>
  <el-dialog title="编辑权限" append-to-body :visible.sync="dialogVisible" width="85%">
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" @selection-change="selectChange" max-height="800">
      <el-table-column type="expand">
        <template slot-scope="props">
          <p style="text-indent: 2em;" v-html="props.row.desc"></p>
        </template>
      </el-table-column>
      <el-table-column prop="name" label="权限名称"></el-table-column>
      <el-table-column v-for="(role, roleId) in roles" :key="roleId" width="100" :label="role.name" header-align="center" align="center">
        <template slot-scope="scope">
          <el-checkbox></el-checkbox>
        </template>
      </el-table-column>
    </el-table>
  </el-dialog>
</template>

<script>
import Api from '@/api'
export default {
  data () {
    return {
      tableLoading: false,
      highlightCurrentRow: true,
      stripe: true,
      border: false,
      multipleSelection: [],
      dialogVisible: false,
      roles: {},
      permissions: {},
      tableData: []
    }
  },
  methods: {
    showDialog (ids) {
      this.roles = {}
      this.permissions = {}
      this.tableData = []
      this.tableLoading = true
      Api.roles.permissions(ids).then((data) => {
        this.tableLoading = false
        this.roles = data.roles
        this.permissions = data.permissions
        for (let permissionId in this.permissions) {
          let val = this.permissions[permissionId]
          let row = {
            permission_id: permissionId,
            name: val.name,
            desc: val.desc
          }
          // for (let role_id in this.roles) {
          // }
          this.tableData.push(row)
        }
        this.dialogVisible = true
      }).catch(() => {})
    },
    selectChange (val) {
      this.multipleSelection = val.map(item => parseInt(item.id))
    }
  }
}
</script>

<style lang="scss" scoped></style>
