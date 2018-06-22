<template>
  <el-dialog title="添加角色" append-to-body :visible.sync="dialogVisible">
    <el-form :model="form" ref="form" status-icon :rules="rules" >
      <el-form-item label="ID" prop="id" size="small">
        <el-input type="text" v-model="form.id"></el-input>
      </el-form-item>
      <el-form-item label="名称" prop="name" size="small">
        <el-input type="text" v-model="form.name"></el-input>
      </el-form-item>
      <el-form-item label="优先级" size="small">
        <el-input type="text" v-model="form.priority"></el-input>
      </el-form-item>
      <el-form-item label="描述" size="small">
        <el-input type="textarea" v-model="form.desc"></el-input>
      </el-form-item>
    </el-form>
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
      id: '',
      dialogVisible: false,
      form: {
        id: '',
        name: '',
        desc: '',
        priority: 0
      },
      rules: {
        id: [
          {required: true, message: '请输入ID', trigger: 'blur'},
          {
            validator: (rule, value, callback) => {
              if (value) {
                Api.role.validateIdUnique(value, this.id).then((data) => {
                  if (data && data.invalid) {
                    callback(new Error(data.invalid))
                  } else {
                    callback()
                  }
                })
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        name: [
          {required: true, message: '请输入名称', trigger: 'blur'}
        ]
      }
    }
  },
  methods: {
    showDialog (id) {
      this.id = id
      Api.role.get(id).then((data) => {
        let role = data.role
        this.form.id = role.id
        this.form.name = role.name
        this.form.priority = role.priority
        this.form.desc = role.description
        this.dialogVisible = true
      }).catch(() => {})
    },
    submitDialogForm () {
      this.$refs.form.validate((valid) => {
        if (valid) {
          Api.role.edit(this.id, {
            id: this.form.id,
            name: this.form.name,
            priority: this.form.priority,
            desc: this.form.desc
          }).then(() => {
            this.dialogVisible = false
            this.$refs.form.resetFields()
            this.form.desc = ''
            this.$emit('parent')
          })
        } else {
          this.$message({
            message: '验证出错',
            type: 'error'
          })
          return false
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped></style>
