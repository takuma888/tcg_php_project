<template>
  <el-container>
    <el-header>
      <el-row type="flex">
        <el-col :span="24">
          <el-col class="logo" :class="collapsed ? 'logo-collapsed' : ''">
            <router-link class="logo-link" to="/">{{ collapsed ? '&nbsp;': homeName }}</router-link>
          </el-col>
          <el-col :span="16">
            <div class="tools" @click.prevent="collapse">
              <i class="fa fa-align-justify"></i>
            </div>
            <el-menu class="top-menu"
                     mode="horizontal"
                     @select="handleTopMenuSelect"
                     active-text-color="#ffd04b" background-color="#20a0ff">
            </el-menu>
          </el-col>
          <el-col :span="4" class="userinfo">
            <el-dropdown trigger="hover">
              <span class="el-dropdown-link userinfo-inner"><img v-if="user.avatar" :src="user.avatar" /> {{ user.username }}</span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>我的消息</el-dropdown-item>
                <el-dropdown-item>设置</el-dropdown-item>
                <el-dropdown-item divided @click.native="logout">退出登录</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </el-col>
        </el-col>
      </el-row>
    </el-header>
  </el-container>
</template>

<script>
export default {
  data () {
    return {
      homeName: '用户管理系统',
      collapsed: false,
      user: {},
      form: {
        name: '',
        region: '',
        date1: '',
        date2: '',
        delivery: false,
        type: [],
        resource: '',
        desc: ''
      }
    }
  },
  methods: {
    // 处理顶部导航select
    handleTopMenuSelect (key, keyPath) {
      console.log(key, keyPath)
    },
    // 折叠导航栏
    collapse () {
      this.collapsed = !this.collapsed
    },
    // 退出登录
    logout () {
      this.$confirm('确认退出吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        sessionStorage.removeItem('user')
        this.$router.push('/login')
      }).catch(() => {})
    }
  },
  mounted () {
    let user = sessionStorage.getItem('user')
    if (user) {
      user = JSON.parse(user)
      this.user = user
    }
  }
}
</script>

<style lang="scss" scoped>
  .el-header {
    .el-row {
      background-color: #20a0ff;//#18c79c
      color: white;
      height: 60px;
      line-height: 60px;
      .logo {
        width: 200px;
        cursor: pointer;
        border-right: 1px solid hsla(62,77%,76%,.3);
        padding: 0 20px;
        &.logo-collapsed {
          width: 60px;
          .logo-link {
            width: 20px;
          }
        }
        .logo-link {
          text-decoration: none;
          color: white;
          width: 160px;
          display: inline-block;
        }
      }
      .tools {
        cursor: pointer;
        width: 14px;
        height: 60px;
        padding: 0 23px;
        float: left;
      }
      .top-menu {
        height: 60px;
        line-height: 60px;
        padding: 0 20px;
        float: left;
      }
    }
  }
</style>
