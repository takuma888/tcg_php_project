<template>
  <mu-container class="login-container">
    <mu-form :model="form" label-position="left" ref="form" label-width="0px" class="login-form">
      <h3 class="title">管理员登录</h3>
      <mu-form-item prop="username" :rules="rules.username">
        <mu-text-field v-model="form.username" prop="username" auto-complete="off" placeholder="用户名"></mu-text-field>
      </mu-form-item>
      <mu-form-item prop="password" :rules="rules.password">
        <mu-text-field v-model="form.password" prop="password" auto-complete="off" placeholder="密码"
                       :action-icon="passwordVisibility ? 'visibility_off' : 'visibility'"
                       :action-click="() => (passwordVisibility = !passwordVisibility)"
                       :type="passwordVisibility ? 'text' : 'password'"></mu-text-field>
      </mu-form-item>
      <mu-form-item>
        <mu-flex align-items="center" justify-content="between" style="width: 100%;">
          <mu-checkbox label="自动登录" v-model="checked"></mu-checkbox>
          <mu-button color="primary" @click="submit">提交</mu-button>
        </mu-flex>
      </mu-form-item>
    </mu-form>
  </mu-container>
</template>

<script>
export default {
  data () {
    return {
      form: {
        username: '',
        password: ''
      },
      rules: {
        username: [
          { validate: (val) => !!val, message: '必须填写用户名' }
        ],
        password: [
          { validate: (val) => !!val, message: '必须填写密码' }
        ]
      },
      checked: true,
      passwordVisibility: false
    }
  },
  methods: {
    submit () {
      this.$refs.form.validate().then((valid) => {
        if (valid) {
          this.$api.login(this.form.username, this.form.password).then((data) => {
            sessionStorage.setItem('user', JSON.stringify(data.user))
            this.$message({
              message: '欢迎回来！' + data.user.username,
              type: 'success'
            })
            this.$router.push('/')
          }).catch(() => {
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .login-container {
    background-clip: padding-box;
    margin: 180px auto 0 auto;
    width: 350px;
    padding: 35px 35px 15px 35px;
    background: #fff;
    border: 1px solid #eaeaea;
    box-shadow: 0 0 25px #cac6c6;
    .title {
      margin: 0px auto 40px auto;
      text-align: center;
      color: #505458;
    }
    .remember {
      margin: 0px 0px 35px 0px;
    }
  }
</style>
