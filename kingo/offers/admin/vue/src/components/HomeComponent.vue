<template>
  <mu-container fluid style="padding: 0px;">
    <mu-row>
      <mu-col span="12">
        <mu-appbar style="width: 100%; position: fixed;" color="primary">
          <mu-button icon slot="left" @click.native="drawerToggle">
            <mu-icon value="menu"></mu-icon>
          </mu-button>
          <router-link to="/" style="color: inherit;">{{$store.state.ui.htmlTitle}}</router-link>
          <mu-menu slot="right">
            <mu-button flat><mu-icon value="account_circle" left></mu-icon>&nbsp;{{user.username}}</mu-button>
            <mu-list slot="content">
              <mu-list-item button @click.native="logout">
                <mu-list-item-content>
                  <mu-list-item-title>退出登录</mu-list-item-title>
                </mu-list-item-content>
              </mu-list-item>
            </mu-list>
          </mu-menu>
        </mu-appbar>
      </mu-col>
    </mu-row>
    <mu-row>
      <mu-col span="12" style="margin-top: 84px;">
        <router-view />
      </mu-col>
    </mu-row>
    <mu-drawer :open.sync="drawerOpen" :docked="drawerDocked">
      <mu-list>
        <mu-list-item>
          <mu-list-item-content>
            <mu-list-item-title>OFFER 总库</mu-list-item-title>
            <mu-list-item-sub-title>KINGO</mu-list-item-sub-title>
          </mu-list-item-content>
        </mu-list-item>
      </mu-list>
      <mu-divider></mu-divider>
      <mu-list @change="drawerNavChange" :value="$store.state.ui.path">
        <mu-list-item button value="/offers">
          <mu-list-item-title>OFFER 库</mu-list-item-title>
        </mu-list-item>
        <mu-list-item button value="/strategies">
          <mu-list-item-title>策略</mu-list-item-title>
        </mu-list-item>
        <mu-list-item button value="/statistics">
          <mu-list-item-title>统计</mu-list-item-title>
        </mu-list-item>
      </mu-list>
    </mu-drawer>
  </mu-container>
</template>

<script>
export default {
  data () {
    return {
      user: {},
      drawerOpen: false,
      drawerDocked: false
    }
  },
  methods: {
    logout () {
      this.$confirm('确认退出吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$api.logout().then((response) => {
          sessionStorage.removeItem('user')
          this.$router.push('/login')
        })
      }).catch(() => {})
    },
    drawerToggle () {
      this.drawerOpen = !this.drawerOpen
    },
    drawerNavChange (value) {
      this.$store.commit('path', value)
      this.$router.push(value)
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
