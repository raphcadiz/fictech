<template>
  <div class="row mb-4">
    <div class="col-md-6 mb-4 order-lg-last order-md-last">
      <gmap-map
        :center="center"
        :zoom="6"
        map-type-id="terrain"
        style="width: 100%; height: 600px;"
      >
        <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen" @closeclick="infoWinOpen=false">
          <h5 class="map-location-title">{{infoContent.title}}</h5>
          <span v-if="infoContent.location"  class="map-location-name">{{infoContent.location}}</span>
          <p class="map-location-desc">{{infoContent.description}}</p>
        </gmap-info-window>
        
        <gmap-marker
          :key="index"
          v-for="(m, index) in markers"
          :position="m.position"
          :clickable="true"
          :draggable="false"
          @click="toggleInfoWindow(m,index)"
        ></gmap-marker>
      </gmap-map>

    </div>

    <div class="col-md-6 order-lg-first order-md-first">
      <card :title="'Companies - ' + this.companies.length + ' Results'">

        <div class="row mb-4">
          <div class="col-md-12">
              <fa icon="spinner" class="fa-spin" v-if="searching" fixed-width/>
              <span>Filters: </span>
              <span class="badge p-2" :class="(cert_type.indexOf('api_certified') >= 0) ? 'badge-primary' : 'badge-default'" @click="selectCertType('api_certified')">API Certified</span>
              <span class="badge p-2" :class="(cert_type.indexOf('iso_certified') >= 0) ? 'badge-primary' : 'badge-default'" @click="selectCertType('iso_certified')">ISO Certified</span>
              <span class="badge p-2" :class="(cert_type.indexOf('bbb') >= 0) ? 'badge-primary' : 'badge-default'" @click="selectCertType('bbb')">BBB</span>
          </div>
        </div>

        <template v-if="companies.length">
          <div class="row mb-3" v-for="company in companies">
            <div class="col-md-3">
              <img src="../../../images/company-logo.png" />
            </div>
            <div class="col-md-9">
              <h3 class="s-company-title" @click.prevent="toggleInfoWindow(company.marker, 0, true)">{{company.name}}</h3>
              <span v-if="company.locations && company.locations.length" class="s-company-address">{{ company.locations[0].address }}</span>
              <span v-else  class="s-company-address">{{company.address}}</span>
              <p>{{company.description}}</p>
              <hr />
            </div>
          </div>
        </template>
        <template v-else>
          <p class="align-center">No Companies Found.</p>
        </template>
      </card>
    </div>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import _ from 'lodash'
import $ from 'jquery'
import axios from 'axios'

export default {
  middleware: 'auth',

  data () {
    return {
      searching: false,
      center: {lat: 10.6770687, lng: 122.9528936},
      infoOptions: {
        pixelOffset: {
          width: 0,
          height: -35
        }
      },
      infoWindowPos: {
        lat: 0,
        lng: 0
      },
      currentMidx: null,
      infoWinOpen: false,
      infoContent: '',
      markers: [],
      companies: [],
      cert_type: []
    }
  },

  metaInfo () {
    return { title: 'Companies' }
  },

  created () {
    this.loadCompanies()
  },

  computed: mapGetters({
    search: 'auth/search'
  }),

  watch: {
    search: _.debounce(
      function () {
        if (this.search === '') {
          this.getDefaultPlaces()
        } else {
          this.searchCompanies()
        }
      },
      500
    )
  },

  methods: {
    loadCompanies () {
      let geojson_url = 'https://raw.githubusercontent.com/yerffejjapitana/GeoMap/master/map.geojson'
      $.getJSON(geojson_url, function(result) {
        this.dummy_companies = result.features

        if (this.search === '') {
          this.getDefaultPlaces()
        } else {
          this.searchCompanies()
        }
      }.bind(this))
    },
    
    getDefaultPlaces () {
      this.searching = true
      let markers = []
      let dummy_companies = []

      _.forEach(this.dummy_companies, function(val, key) {
        // set map center
        if (key == 0) {
          this.center = { lat: parseFloat(val.geometry.coordinates[1]), lng: parseFloat(val.geometry.coordinates[0]) }
        }

        // set markers
        let marker = {
          content: {title: val.properties.title, description: val.properties.description},
          position: { lat: parseFloat(val.geometry.coordinates[1]), lng: parseFloat(val.geometry.coordinates[0]) }
        }          
        markers.push(marker)

        // set dummy companies
        let dummy_company = {
          name: val.properties.title,
          address: val.properties.address,
          description: val.properties.description,
          marker: marker
        }
        dummy_companies.push(dummy_company)
      }.bind(this))

      this.companies = dummy_companies
      this.markers = markers
      this.searching = false
    },

    searchCompanies () {
      this.searching = true
      axios.get('/api/company-search?s=' + this.search + '&cert_type=' + this.cert_type).then(function(result) {
        // this.companies = result.data

        let markers = []
        let companies_temp = []
        _.each(result.data, function(company, key) {

          let vue = this
          _.each(company.locations, function(location, x) {

            if (location.lat) {
              // set markers
              let marker = {
                content: {title: company.name, location: location.name, description: company.description},
                position: { lat: parseFloat(location.lat), lng: parseFloat(location.lng) }
              }
              markers.push(marker)

              // set map center
              if (key == 0) {
                vue.center = { lat: parseFloat(location.lat), lng: parseFloat(location.lng) }
              }

              // set company default marker
              if(x == 0) {
                company['marker'] = marker
              }
              
            } //endif
            
          })

          companies_temp.push(company)
        }.bind(this))
  
        this.markers = markers
        this.companies = companies_temp
        this.searching = false
      }.bind(this))
    },

    toggleInfoWindow: function (marker, idx, fcompany = false) {
      if (marker == undefined || marker == '')
        return

      this.center = marker.position
      this.infoWindowPos = marker.position
      this.infoContent =  {title: marker.content.title, location: marker.content.location, description: marker.content.description}

      // check if its the same marker that was selected if yes toggle
      if (fcompany) {
        this.infoWinOpen = true
        this.currentMidx = idx
        window.scrollTo(0, 0)
      }
      else if (this.currentMidx === idx) {
        this.infoWinOpen = !this.infoWinOpen
      } else {
        this.infoWinOpen = true
        this.currentMidx = idx
      }
    },

    selectCertType (type) {
      let index = this.cert_type.indexOf(type)
      if (index >= 0) {
        this.cert_type.splice(index, 1)
      } else {
        this.cert_type.push(type)
      }

      this.searchCompanies()
    }

  // end methods
  }

}
</script>

<style scoped>
.vue-google-map {
  height: 100%;
  width: 100%;
  min-height: 600px;
}

.s-company-title {
  cursor: pointer;
}

span.badge {
  cursor: pointer;
  border-radius: 5px;
}

span.badge-default {
  background-color: #f3f3f3;
  border: 1px solid #dcdcdc;
  color: #7f7f7f;
}

</style>
  
