import Vue from 'vue'
import * as VueGoogleMaps from 'vue2-google-maps'

Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyC0qEZ85ngmINWoPrWqlKUJeHZnn7CsaQs',
    libraries: 'places',
    v: '3'
  }
})