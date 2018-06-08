<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
  #app {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 100%;
    background-color: #efefef;
  }
</style>
