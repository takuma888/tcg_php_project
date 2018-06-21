<template>
  <el-dialog append-to-body :visible.sync="dialogEditVisible">
    <template slot="title">编辑用户 ID # {{ id }}</template>
    <el-form :model="form" ref="form" status-icon :rules="rules" >
      <el-form-item label="用户名" prop="username" size="small">
        <el-input type="text" v-model="form.username"></el-input>
      </el-form-item>
      <el-form-item label="邮箱" prop="email" size="small">
        <el-input type="text" v-model="form.email"></el-input>
      </el-form-item>
      <el-form-item label="手机号" prop="mobile" size="small">
        <el-input type="text" v-model="form.mobile"></el-input>
      </el-form-item>
      <el-form-item label="昵称" size="small">
        <el-input type="text" v-model="form.nickname"></el-input>
      </el-form-item>
      <el-form-item label="QQ" size="small">
        <el-input type="text" v-model="form.qq"></el-input>
      </el-form-item>
      <el-form-item label="微信" size="small">
        <el-input type="text" v-model="form.wei_xin"></el-input>
      </el-form-item>
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button size="small" @click="dialogEditVisible = false">取 消</el-button>
      <el-button size="small" type="primary" @click="submitEditDialogForm">确 定</el-button>
    </span>
  </el-dialog>
</template>

<script>
import Api from '@/api'
export default {
  data () {
    return {
      id: '',
      dialogEditVisible: false,
      form: {
        username: '',
        email: '',
        mobile: '',
        nickname: '',
        qq: '',
        wei_xin: ''
      },
      rules: {
        username: [
          {
            validator: (rule, value, callback) => {
              if (!this.form.username && !this.form.email && !this.form.mobile) {
                callback(new Error('用户名、邮箱、手机不能全部为空'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          },
          {
            validator: (rule, value, callback) => {
              if (value) {
                Api.user.validateUsernameUnique(value, this.id).then((data) => {
                  if (data && data.invalid) {
                    callback(new Error(data.invalid))
                  } else {
                    callback()
                  }
                })
              }
              callback()
            },
            trigger: 'blur'
          }
        ],
        email: [
          { type: 'email', message: '邮箱格式错误', trigger: 'change' },
          {
            validator: (rule, value, callback) => {
              if (!this.form.username && !this.form.email && !this.form.mobile) {
                callback(new Error('用户名、邮箱、手机不能全部为空'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          },
          {
            validator: (rule, value, callback) => {
              if (value) {
                Api.user.validateEmailUnique(value, this.id).then((data) => {
                  if (data && data.invalid) {
                    callback(new Error(data.invalid))
                  } else {
                    callback()
                  }
                })
              }
              callback()
            },
            trigger: 'blur'
          }
        ],
        mobile: [
          {
            validator: (rule, value, callback) => {
              if (!this.form.username && !this.form.email && !this.form.mobile) {
                callback(new Error('用户名、邮箱、手机不能全部为空'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          },
          {
            validator: (rule, value, callback) => {
              if (value) {
                Api.user.validateMobileUnique(value, this.id).then((data) => {
                  if (data && data.invalid) {
                    callback(new Error(data.invalid))
                  } else {
                    callback()
                  }
                })
              }
              callback()
            },
            trigger: 'blur'
          }
        ]
      }
    }
  },
  methods: {
    submitEditDialogForm () {
      this.$refs.form.validate((valid) => {
        if (valid) {
          Api.user.edit(this.id, {
            auth: {
              username: this.form.username,
              email: this.form.email,
              mobile: this.form.mobile
            },
            profile: {
              nickname: this.form.nickname,
              qq: this.form.qq,
              wei_xin: this.form.wei_xin
            }
          }).then(() => {
            this.dialogEditVisible = false
            this.$refs.form.resetFields()
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
    },
    showDialog (id) {
      this.id = id
      Api.user.get(id).then((data) => {
        let user = data.user
        this.form.username = user.username
        this.form.email = user.email
        this.form.mobile = user.mobile
        this.form.nickname = user.nickname
        this.form.qq = user.qq
        this.form.wei_xin = user.wei_xin
        this.dialogEditVisible = true
      }).catch(() => {})
    }
  }
}
</script>

<style lang="scss" scoped></style>
