<template>
  <el-form ref="form" status-icon :model="form" :rules="rules" label-width="80px" label-position="left">
    <el-form-item label="账号" prop="username">
      <el-input type="text" v-model="form.username" placeholder="登录用户名"></el-input>
    </el-form-item>
    <el-form-item label="手机">
      <el-input type="text" v-modal="form.mobile" placeholder="登录手机号"></el-input>
    </el-form-item>
    <el-form-item label="邮箱">
      <el-input type="text" v-model="form.email" placeholder="登录邮箱地址"></el-input>
    </el-form-item>
    <el-form-item label="密码" prop="password">
      <el-input type="password" v-model="form.password" placeholder="登录密码" auto-complete="off"></el-input>
    </el-form-item>
    <el-form-item label="验证密码" prop="confirmPassword">
      <el-input type="password" v-model="form.confirmPassword" placeholder="再输入一次密码" auto-complete="off"></el-input>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="submitForm">提交</el-button>
      <el-button @click="resetForm">重置</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
export default {
  data () {
    let validatePassword = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入密码'))
      } else {
        if (this.form.confirmPassword !== '') {
          this.$refs.form.validateField('confirmPassword')
        }
        callback()
      }
    }
    let validateConfirmPassword = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请再次输入密码'))
      } else if (value !== this.form.password) {
        callback(new Error('两次输入密码不一致!'))
      } else {
        callback()
      }
    }
    return {
      loading: false,
      form: {
        username: '',
        password: '',
        confirmPassword: '',
        mobile: '',
        email: ''
      },
      rules: {
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入登录密码', trigger: 'blur' },
          { validator: validatePassword, trigger: 'blur' }
        ],
        confirmPassword: [
          { required: true, message: '请再次输入登录密码', trigger: 'blur' },
          { validator: validateConfirmPassword, trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    submitForm () {
      this.$refs.form.validate((valid) => {
        if (valid) {
          alert('submit!')
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    resetForm () {
      this.$refs.form.resetFields()
    }
  }
}
</script>

<style>
</style>
