<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="scss">
  html,body {
    height: 100%;
    background-color: #efefef;
  }
  #app {
    height: 100%;
  }
</style>
