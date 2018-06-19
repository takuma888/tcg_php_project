<template>
  <span>
    <el-dropdown>
      <el-button :type="type" :size="size">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;添加<i class="el-icon-arrow-down el-icon--right"></i>
      </el-button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item @click.native="dialogUsernameVisible = true">用户名方式添加</el-dropdown-item>
        <el-dropdown-item @click.native="dialogEmailVisible = true">邮箱方式添加</el-dropdown-item>
        <el-dropdown-item @click.native="dialogMobileVisible = true">手机方式添加</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <!-- dialogs -->
    <el-dialog title="通过用户名方式添加用户" append-to-body :visible.sync="dialogUsernameVisible">
      <el-form :model="addUsernameForm" ref="addUsernameForm" status-icon :rules="addUsernameRules" >
        <el-form-item label="用户名" prop="username">
          <el-input type="text" v-model="addUsernameForm.username"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="addUsernameForm.password" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="confirmPassword">
          <el-input type="password" v-model="addUsernameForm.confirmPassword" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogUsernameVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="submitUsernameDialogForm">确 定</el-button>
      </span>
    </el-dialog>
    <el-dialog title="通过邮箱方式添加用户" append-to-body :visible.sync="dialogEmailVisible">
      <el-form :model="addEmailForm" ref="addEmailForm" status-icon :rules="addEmailRules" >
        <el-form-item label="邮箱" prop="email">
          <el-input type="text" v-model="addEmailForm.email"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="addEmailForm.password" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="confirmPassword">
          <el-input type="password" v-model="addEmailForm.confirmPassword" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogEmailVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="submitEmailDialogForm">确 定</el-button>
      </span>
    </el-dialog>
    <el-dialog title="通过手机号方式添加用户" append-to-body :visible.sync="dialogMobileVisible">
      <el-form :model="addMobileForm" ref="addMobileForm" status-icon :rules="addMobileRules" >
        <el-form-item label="手机号" prop="mobile">
          <el-input type="text" v-model="addMobileForm.mobile"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="addMobileForm.password" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="confirmPassword">
          <el-input type="password" v-model="addMobileForm.confirmPassword" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogMobileVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="submitMobileDialogForm">确 定</el-button>
      </span>
    </el-dialog>
  </span>
</template>

<script>
import Api from '@/api'
export default {
  props: ['size', 'type'],
  data () {
    return {
      dialogUsernameVisible: false,
      dialogEmailVisible: false,
      dialogMobileVisible: false,
      addUsernameForm: {
        username: '',
        password: '',
        confirmPassword: ''
      },
      addUsernameRules: {
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              Api.user.validateUsernameUnique(value).then((data) => {
                if (data.invalid) {
                  callback(new Error(data.invalid))
                } else {
                  callback()
                }
              })
            },
            trigger: 'blur'
          }
        ],
        password: [
          { required: true, message: '请输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请输入密码'))
              } else {
                if (this.addUsernameForm.confirmPassword !== '') {
                  this.$refs.addUsernameForm.validateField('confirmPassword')
                }
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        confirmPassword: [
          { required: true, message: '请再次输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请再次输入密码'))
              } else if (value !== this.addUsernameForm.password) {
                callback(new Error('两次输入密码不一致!'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      },
      addEmailForm: {
        email: '',
        password: '',
        confirmPassword: ''
      },
      addEmailRules: {
        email: [
          { required: true, message: '请输入邮箱地址', trigger: 'blur' },
          { type: 'email', message: '邮箱格式错误', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              Api.user.validateEmailUnique(value).then((data) => {
                if (data.invalid) {
                  callback(new Error(data.invalid))
                } else {
                  callback()
                }
              })
            },
            trigger: 'blur'
          }
        ],
        password: [
          { required: true, message: '请输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请输入密码'))
              } else {
                if (this.addEmailForm.confirmPassword !== '') {
                  this.$refs.addEmailForm.validateField('confirmPassword')
                }
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        confirmPassword: [
          { required: true, message: '请再次输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请再次输入密码'))
              } else if (value !== this.addEmailForm.password) {
                callback(new Error('两次输入密码不一致!'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      },
      addMobileForm: {
        mobile: '',
        password: '',
        confirmPassword: ''
      },
      addMobileRules: {
        mobile: [
          { required: true, message: '请输入手机号', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              Api.user.validateMobileUnique(value).then((data) => {
                if (data.invalid) {
                  callback(new Error(data.invalid))
                } else {
                  callback()
                }
              })
            },
            trigger: 'blur'
          }
        ],
        password: [
          { required: true, message: '请输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请输入密码'))
              } else {
                if (this.addMobileForm.confirmPassword !== '') {
                  this.$refs.addMobileForm.validateField('confirmPassword')
                }
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        confirmPassword: [
          { required: true, message: '请再次输入登录密码', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value === '') {
                callback(new Error('请再次输入密码'))
              } else if (value !== this.addMobileForm.password) {
                callback(new Error('两次输入密码不一致!'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      }
    }
  },
  methods: {
    submitUsernameDialogForm () {
      this.$refs.addUsernameForm.validate((valid) => {
        if (valid) {
          Api.user.addUsername(
            this.addUsernameForm.username,
            this.addUsernameForm.password,
            this.addUsernameForm.confirmPassword
          ).then(() => {
            this.dialogUsernameVisible = false
            this.$emit('parent')
          })
        } else {
          this.$message({
            message: '验证出错，请刷新重试',
            type: 'error'
          })
          return false
        }
      })
    },
    submitEmailDialogForm () {
      this.$refs.addEmailForm.validate((valid) => {
        if (valid) {
          Api.user.addEmail(
            this.addEmailForm.email,
            this.addEmailForm.password,
            this.addEmailForm.confirmPassword
          ).then(() => {
            this.dialogEmailVisible = false
            this.$emit('parent')
          })
        } else {
          this.$message({
            message: '验证出错，请刷新重试',
            type: 'error'
          })
          return false
        }
      })
    },
    submitMobileDialogForm () {
      this.$refs.addMobileForm.validate((valid) => {
        if (valid) {
          Api.user.addMobile(
            this.addMobileForm.mobile,
            this.addMobileForm.password,
            this.addMobileForm.confirmPassword
          ).then(() => {
            this.dialogMobileVisible = false
            this.$emit('parent')
          })
        } else {
          this.$message({
            message: '验证出错，请刷新重试',
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
