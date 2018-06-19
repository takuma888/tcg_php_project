<template>
  <div class="users-view">
    <!--<div class="aside">-->
      <!--<el-menu :default-active="$store.state.static.path" style="height: 100%;"-->
               <!--:unique-opened="uniqueOpened"-->
               <!--@select="handleSelect"-->
               <!--:collapse="$store.state.static.asideCollapsed"-->
               <!--:collapse-transition="collapseTransition">-->
        <!--<el-menu-item index="/users/add" class="aside-menu-item">-->
          <!--<i class="el-icon-plus"></i>-->
          <!--<span slot="title">创建用户</span>-->
        <!--</el-menu-item>-->
        <!--&lt;!&ndash;<el-menu-item index="/users/test" class="aside-menu-item">&ndash;&gt;-->
          <!--&lt;!&ndash;<i class="el-icon-menu"></i>&ndash;&gt;-->
          <!--&lt;!&ndash;<span slot="title">test</span>&ndash;&gt;-->
        <!--&lt;!&ndash;</el-menu-item>&ndash;&gt;-->
      <!--</el-menu>-->
    <!--</div>-->
    <el-main class="main">
      <router-view add="1" edit="1"></router-view>
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
  }
}
</script>

<style lang="scss" scoped>
  .users-view {
    width: 100%;
  }
</style>
