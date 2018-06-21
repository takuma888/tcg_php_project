<template>
  <el-container class="container" :class="collapsed ? 'container-collapsed' : ''">
    <el-header class="header">
      <el-row type="flex" class="row">
        <el-col :span="24">
          <el-col class="logo">
            <a class="logo-link" href="/">{{ collapsed ? '': '用户管理系统' }}</a>
            <i class="fa fa-align-justify aside-toggler" @click.prevent="collapse"></i>
          </el-col>
          <el-col :span="16">
            <el-menu :default-active="path"
                     class="top-menu"
                     mode="horizontal"
                     @select="handleSelect"
                     active-text-color="#ffd04b" background-color="#20a0ff">
              <el-menu-item class="top-menu-item" index="/">首页</el-menu-item>
              <el-menu-item class="top-menu-item" index="/users">用户管理</el-menu-item>
              <el-menu-item class="top-menu-item" index="/roles">角色管理</el-menu-item>
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
      <router-view v-if="$store.state.static.path !== '/'"></router-view>
      <template v-if="$store.state.static.path === '/'">
        <div class="home-view">
          <el-main class="main">
            <h1>欢迎来到 "用户管理系统" {{ user.username }}</h1>
          </el-main>
        </div>
      </template>
    </el-container>
  </el-container>
</template>

<script>
import Api from '@/api'
export default {
  data () {
    this.$store.commit('path', this.$route.path)
    let path = this.$store.state.static.path.split('/')
    return {
      path: '/' + path[1],
      user: {},
      collapsed: false
    }
  },
  methods: {
    // 折叠导航栏
    collapse () {
      this.collapsed = !this.collapsed
      if (this.collapsed) {
        this.$store.commit('collapseAside')
      } else {
        this.$store.commit('expandAside')
      }
    },
    // 处理顶部导航select
    handleSelect (key, keyPath) {
      this.$store.commit('path', key)
      this.$router.push(key)
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

<style lang="scss">
  .header {
    position: fixed;
    left: 0;
    right: 0;
    z-index: 1020;
    .fa {
      color: white;
    }
    .row {
      background-color: #20a0ff;//#18c79c
      color: white;
      height: 60px;
      line-height: 60px;
      .logo {
        border-right: 1px solid hsla(62,77%,76%,.3);
        width: 200px;
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
  .container {
    height: 100%;
  }
  .container-inner {
    margin: 60px 20px 0 20px;
  }
  .main {
    background-color: white;
    margin-top: 20px;
  }
  .aside {
    background-color: white;
    position: fixed;
    width: 200px;
    height: 100%;
    margin-right: 20px;
    & + .main {
      margin-left: 220px;
    }
  }
  .container-collapsed {
    .header {
      .row {
        .logo {
          width: 64px;
        }
      }
    }
    .aside {
      width: 64px;
      & + .main {
        margin-left: 84px;
      }
    }
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
</style>

<style lang="scss" scoped>
  .home-view {
    width: 100%;
  }
</style>
