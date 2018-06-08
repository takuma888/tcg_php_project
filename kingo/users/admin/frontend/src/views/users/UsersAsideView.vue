<template>
  <div></div>
</template>

<script>
export default {
  props: ['collapsed'],
  data () {
    return {
    }
  },
  methods: {
    // 处理侧边栏导航select
    handleAsideMenuSelect (key, keyPath) {
      this.$router.push(key)
    }
  }
}
</script>

<style lang="scss" scoped></style>
