<template>
  <el-container class="container" :class="collapsed ? 'container-collapsed' : ''">
    <el-header class="header">
      <el-row type="flex" class="row">
        <el-col :span="24">
          <el-col class="logo">
            <router-link class="logo-link" to="/">{{ collapsed ? '': homeName }}</router-link>
            <i class="fa fa-align-justify aside-toggler" @click.prevent="collapse"></i>
          </el-col>
          <el-col :span="16">
            <el-menu :default-active="$route.path"
                     class="top-menu"
                     mode="horizontal"
                     @select="handleTopMenuSelect"
                     active-text-color="#ffd04b" background-color="#20a0ff">
              <el-menu-item class="top-menu-item" index="/">首页</el-menu-item>
              <el-menu-item class="top-menu-item" index="/users">用户管理</el-menu-item>
            </el-menu>
          </el-col>
          <el-col :span="4" class="userinfo">
            <el-dropdown trigger="click" class="userinfo-dropdown">
              <span class="el-dropdown-link userinfo-inner">
                {{ user.username }}
                <img v-if="user.avatar" :src="user.avatar" >
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item @click.native="logout">退出登录</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </el-col>
        </el-col>
      </el-row>
    </el-header>
    <el-container class="container-inner">
      <div class="aside" v-if="$route.path !== '/'">
        <router-view name="aside" v-bind:collapsed="collapsed"></router-view>
      </div>
      <el-main class="main">
        <router-view></router-view>
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import Api from '../api'
export default {
  data () {
    return {
      homeName: '用户管理系统',
      collapsed: false,
      user: {}
    }
  },
  methods: {
    // 处理顶部导航select
    handleTopMenuSelect (key, keyPath) {
      this.$router.push(key)
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
        Api.logout().then((response) => {
          sessionStorage.removeItem('user')
          this.$router.push('/login')
        })
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
  $aside-width: 200px !default;
  $aside-collapse-width: 64px !default;
  .container {
  }
  .header {
    position: fixed;
    left: 0;
    right: 0;
    .fa {
      color: white;
    }
    .row {
      background-color: #20a0ff;//#18c79c
      color: white;
      height: 60px;
      line-height: 60px;
      .logo {
        width: $aside-width;
        border-right: 1px solid hsla(62,77%,76%,.3);
        .logo-link {
          text-decoration: none;
          color: white;
          display: block;
          float: left;
          padding-left: 20px;
          cursor: pointer;
          font-size: 1.2rem;
        }
      }
      .aside-toggler {
        display: inline-block;
        cursor: pointer;
        height: 60px;
        line-height: 60px;
        float: right;
        margin-right: 23px;
      }
      .top-menu {
        height: 60px;
        line-height: 60px;
        padding: 0 20px;
        float: left;
        .top-menu-item {
          color: white;
        }
      }
      .userinfo {
        float: right;
        padding-right: 35px;
        text-align: right;
        cursor: pointer;
        .userinfo-inner {
          color: white;
        }
        .userinfo-dropdown {
          height: 20px;
          line-height: 20px;
          margin-top: 20px;
        }
      }
    }
  }
  .container-inner {
    margin: 60px 20px 0;
    bottom: 0;
    .aside {
      position: fixed;
      width: $aside-width;
      top: 60px;
      bottom: 0;
      .el-menu {
        height: 100%;
      }

      & + .main {
        margin-left: 220px;
      }
    }
    .main {
      margin-top: 20px;
      padding: 0;
      background-color: white;
    }
  }
  .container {
    &.container-collapsed {
      .logo {
        width: $aside-collapse-width;
      }
      .container-inner {
        .aside {
          width: $aside-collapse-width;
          & + .main {
            margin-left: 84px;
          }
        }
      }
    }
  }
</style>
