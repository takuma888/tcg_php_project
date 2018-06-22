<template>
  <div class="users-view">
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
