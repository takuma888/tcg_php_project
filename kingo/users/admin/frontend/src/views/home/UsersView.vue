<template>
  <div class="users-view">
    <div class="aside">
      <el-menu :default-active="$store.state.static.path" style="height: 100%;"
               :unique-opened="uniqueOpened"
               @select="handleSelect"
               :collapse="$store.state.static.asideCollapsed"
               :collapse-transition="collapseTransition">
        <el-menu-item index="/users/add" class="aside-menu-item">
          <i class="el-icon-plus"></i>
          <span slot="title">创建用户</span>
        </el-menu-item>
        <!--<el-menu-item index="/users/test" class="aside-menu-item">-->
          <!--<i class="el-icon-menu"></i>-->
          <!--<span slot="title">test</span>-->
        <!--</el-menu-item>-->
      </el-menu>
    </div>
    <el-main class="main">
      <router-view></router-view>
    </el-main>
  </div>
</template>

<script>
export default {
  data () {
    return {
      uniqueOpened: true,
      collapseTransition: false
    }
  },
  methods: {
    handleSelect (key, keyPath) {
      this.$store.commit('path', key)
      this.$router.push(key)
    }
  },
  mounted () {
  }
}
</script>

<style lang="scss" scoped>
  .users-view {
    width: 100%;
  }
</style>
