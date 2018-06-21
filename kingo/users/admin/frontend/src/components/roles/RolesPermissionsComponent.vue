<template>
  <el-dialog title="编辑权限" append-to-body :visible.sync="dialogVisible" width="85%">
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" max-height="500">
      <el-table-column type="expand">
        <template slot-scope="props">
          <p v-html="props.row.desc"></p>
        </template>
      </el-table-column>
      <el-table-column prop="name" label="权限名称"></el-table-column>
      <el-table-column v-for="(role, roleId) in roles" :key="roleId" width="100" :label="role.name" header-align="center" align="center">
        <template slot-scope="scope">
          <el-checkbox v-model="checkboxes[roleId][scope.row.permission_id]" :checked="checkboxes[roleId][scope.row.permission_id]"></el-checkbox>
        </template>
      </el-table-column>
    </el-table>
    <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="submitDialogForm">确 定</el-button>
      </span>
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
      border: true,
      dialogVisible: false,
      roles: {},
      permissions: {},
      tableData: [],
      checkboxes: {}
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
        let permissionId
        for (permissionId in this.permissions) {
          let val = this.permissions[permissionId]
          let row = {
            permission_id: permissionId,
            name: val.name,
            desc: val.desc
          }
          this.tableData.push(row)
        }
        for (let roleId in this.roles) {
          let role = this.roles[roleId]
          this.checkboxes[roleId] = role['role_permissions']
        }
        console.log(this.checkboxes)
        this.dialogVisible = true
      }).catch(() => {})
    },
    submitDialogForm () {
      console.log(this.checkboxes)
    }
  }
}
</script>

<style lang="scss" scoped></style>
