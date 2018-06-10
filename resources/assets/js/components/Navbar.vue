<template>
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <router-link :to="{ name: user ? 'dashboard' : 'welcome' }" class="navbar-brand">
        <!-- {{ appName }} -->
        <img src="../../images/logo.png" class="logo-img" />
      </router-link>

      <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#navbarToggler" aria-controls="navbarToggler"
        aria-expanded="false" :aria-label="$t('toggle_navigation')"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav">
           <template  v-if="user">
              <li class="ml-2 mr-2">
                <input type="text" class="form-control search-control" name="" placeholder="Search.." @keydown.enter="setSearch($event)" v-model="search_nav">
              </li>
             <li class="nav-item">
                <router-link :to="{ name: 'company'}" class="nav-link">
                  Companies
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'company-category'}" class="nav-link">
                  Categories
                </router-link>
              </li>
              <!-- <li class="nav-item">
                  <router-link :to="{ name: 'company-search'}" class="nav-link">
                    Search
                  </router-link>
                </li> -->
           </template>
        </ul>

        <ul class="navbar-nav ml-auto">
          <locale-dropdown/>
          <!-- Authenticated -->
          <li v-if="user" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle"
              href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img :src="user.photo_url" class="rounded-circle profile-photo mr-1">
              {{ user.name }}
            </a>
            <div class="dropdown-menu">
              <router-link :to="{ name: 'settings.profile' }" class="dropdown-item pl-3">
                <fa icon="cog" fixed-width/>
                {{ $t('settings') }}
              </router-link>

              <div class="dropdown-divider"></div>
              <a @click.prevent="logout" class="dropdown-item pl-3"  href="#">
                <fa icon="sign-out-alt" fixed-width/>
                {{ $t('logout') }}
              </a>
            </div>
          </li>
          <!-- Guest -->
          <template v-else>
            <li class="nav-item">
              <router-link :to="{ name: 'login' }" class="nav-link" active-class="active">
                {{ $t('login') }}
              </router-link>
            </li>
            <!-- <li class="nav-item">
              <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                {{ $t('register') }}
              </router-link>
            </li> -->
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'

export default {
  data: () => ({
    appName: window.config.appName,
    search_nav: ''
  }),

  computed: mapGetters({
    user: 'auth/user',
    search: 'auth/search'
  }),

  components: {
    LocaleDropdown
  },

  watch: {
    search () {
      this.search_nav = this.search
    },

    search_nav () {
      this.$store.dispatch('auth/setSearch', this.search_nav)
    }
  },

  methods: {
    setSearch (evt) {
      if (this.$route.name !== 'company-search') {
        this.$router.push({ name: 'company-search' })
      }
    },

    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>

<style scoped>
.profile-photo {
  display: inline-block;
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
</style>
