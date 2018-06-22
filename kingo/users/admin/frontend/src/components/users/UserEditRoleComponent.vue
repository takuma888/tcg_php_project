<template>
  <el-dialog append-to-body :visible.sync="dialogVisible">
    <template slot="title">编辑用户角色</template>
    <div style="text-align: center;">
      <el-transfer v-model="roles"
                   :data="data"
                   :titles="['未拥有角色', '已拥有角色']"
                   style="text-align: left; display: inline-block;"
                   filterable
                   :filter-method="filterMethod"
                   filter-placeholder="请输入角色名称"
                   :button-texts="['移除角色', '添加角色']"></el-transfer>
    </div>
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
      dialogVisible: false,
      roles: [],
      data: [],
      id: null
    }
  },
  methods: {
    showDialog (uid) {
      this.data = []
      this.roles = []
      this.id = uid
      Api.user.roles(uid).then((data) => {
        for (let i in data.roles) {
          const role = data.roles[i]
          this.data.push({
            key: role.id,
            label: role.name
          })
        }
        this.roles = data['user_roles']
        this.dialogVisible = true
      }).catch(() => {})
    },
    submitDialogForm () {
      Api.user.editRoles(this.id, this.roles).then(() => {
        this.dialogVisible = false
        this.$emit('parent')
      }).catch(() => {})
    },
    filterMethod (query, item) {
      if (query) {
        const re = new RegExp(query, 'i')
        return re.test(item['label'])
      }
      return true
    }
  }
}
</script>

<style lang="scss" scoped></style>
