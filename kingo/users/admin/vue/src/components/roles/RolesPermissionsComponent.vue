<template>
  <el-dialog title="编辑权限" append-to-body :visible.sync="dialogVisible" width="85%">
    <el-table :data="tableData"
              :highlight-current-row="highlightCurrentRow"
              :stripe="stripe"
              :border="border" size="mini" v-loading="tableLoading" max-height="500">
      <el-table-column type="expand" label="#">
        <template slot-scope="props">
          <p v-html="props.row.desc"></p>
        </template>
      </el-table-column>
      <el-table-column prop="name" label="权限名称" :filters="tableFilters" :filter-method="filterHandler">
        <template slot-scope="props">
          <span v-html="props.row.name"></span>
        </template>
      </el-table-column>
      <el-table-column v-for="(role, roleId) in roles" :key="roleId" width="100" :label="role.name" header-align="center" align="center">
        <template slot-scope="scope">
          <el-checkbox v-model="rolesPermissions[roleId][scope.row.permission_id]" :checked="rolesPermissions[roleId][scope.row.permission_id]"></el-checkbox>
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
      rolesPermissions: {},
      tableFilters: []
    }
  },
  methods: {
    showDialog (ids) {
      this.roles = {}
      this.permissions = {}
      this.tableData = []
      this.tableLoading = true
      this.rolesPermissions = {}
      this.tableFilters = []
      Api.permissions.list(ids).then((data) => {
        this.tableLoading = false
        this.roles = data.roles || {}
        this.permissions = data.permissions || {}
        let permissionId
        for (permissionId in this.permissions) {
          const val = this.permissions[permissionId]
          const row = {
            permission_id: permissionId,
            name: val.name,
            desc: val.desc,
            scope: val.scope
          }
          this.tableData.push(row)
        }
        for (let roleId in this.roles) {
          const role = this.roles[roleId]
          this.rolesPermissions[roleId] = role['role_permissions']
        }
        const scopes = data.scopes || []
        for (let index in scopes) {
          const scope = scopes[index]
          this.tableFilters.push({
            text: scope,
            value: scope
          })
        }
        this.dialogVisible = true
      }).catch(() => {})
    },
    submitDialogForm () {
      Api.permissions.edit(this.rolesPermissions).then(() => {
        this.$emit('parent')
        this.dialogVisible = false
      }).catch(() => {})
    },
    filterHandler (value, row, column) {
      return row['scope'] === value
    }
  }
}
</script>

<style lang="scss" scoped></style>
