<template>
  <div>

    <div class="card mb-4">
      <!-- <p>This is going to be you admin landing page, Dashboard.</p> -->
      <!-- <pre>{{featuredcompanies}}</pre> -->
      <section class="featured-filter">
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-default mr-2" @click="selectCertType('api_certified')" :class="(cert_type.indexOf('api_certified') >= 0) ? 'btn-primary' : 'btn-default'">API Certified</button>
            <button class="btn btn-default mr-2" @click="selectCertType('iso_certified')" :class="(cert_type.indexOf('iso_certified') >= 0) ? 'btn-primary' : 'btn-default'">ISO Certified</button>
            <button class="btn btn-default" @click="selectCertType('bbb')" :class="(cert_type.indexOf('bbb') >= 0) ? 'btn-primary' : 'btn-default'">BBB</button>
            <fa icon="spinner" class="fa-spin" v-if="feching" style="color: #fff" fixed-width/>
          </div>
        </div>
      </section>
      <div class="card-body p-4">
        <div class="row">
          <template v-for="company in featuredcompanies">
            <div class="col-md-3">
              <div class="card">
                  <img src="../../../../images/logo-thumbnail.jpg" />
                  <div class="card-body slim-padding">
                      <h4 class="featured__company-name">{{company.name}}</h4>
                      <p class="featured__company-category"><span v-for="(service, index) in company.services">{{service.name}} {{(index != company.services.length-1) ? ', ' : ''}}</span></p>
                      <p class="featured__company-address" v-if="company.locations && company.locations.length">{{company.locations[0].address}}</p>
                      <hr />
                      <p class="featured__company-description">{{company.description.substr(0, 60)}}...</p>
                  </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div class="card product-service-wrap">
      <div class="card-header">
          Products and Services
      </div>
      <div class="card-body p-4">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                  <div class="card-header">
                    Products
                  </div>
                <div class="card-body p-0">
                    <ul class="nav flex-column nav-pills">
                      <template v-for="(product, key) in products">
                        <li class="nav-item">
                          <a href="#" @click.prevent="search(key)" class="nav-link">
                            {{key}} ({{product}})
                          </a>
                        </li>
                      </template>
                    </ul>
                </div>
              </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                      Services
                    </div>
                  <div class="card-body p-0">
                      <ul class="nav flex-column nav-pills">
                        <template v-for="(service, key) in services">
                          <li class="nav-item">
                              <a href="#" @click.prevent="search(key)" class="nav-link">
                                {{key}} ({{service}})
                              </a>
                          </li>
                        </template>
                      </ul>
                  </div>
                </div>
              </div>
          </div>
      </div>
    </div>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import _ from 'lodash'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: 'category' }
  },

  data () {
    return {
      feching: false,
      featuredcompanies: [],
      products: [],
      services: [],
      cert_type: []
    }
  },

  created () {
    this.$store.dispatch('company_category/GET_COMPANY_CATEGORY', this.$route.params.id)
  },

  computed: mapGetters({
    category: 'company_category/category'
  }),

  watch: {
    category () {
      let temp_featured_companies = []
      this.category.companies.map(function(company, key) {
        if (company.featured) {
          temp_featured_companies.push(company)
        }
      })

      this.featuredcompanies = _.shuffle(temp_featured_companies).slice(0, 4)

      let products = []
      let services = []
      _.each(this.category.companies, function(company){
        _.each(company.products, function(product) {
          products.push(product.name)
        })

        _.each(company.services, function(service) {
          services.push(service.name)
        })
      })

      // start filter products
      let temp_products = []
      _.each(products, function(products) {
        if (temp_products.hasOwnProperty(products)) {
          temp_products[products] += 1
        } else {
          temp_products[products] = 1
        }
      })

      temp_products = _.extend({}, temp_products)
      let sorted_products_services = Object.keys(temp_products).sort()
      _.each(sorted_products_services, function(key) {
        let val = temp_products[key]
        delete temp_products[key]
        temp_products[key] = val
      })
      this.products = temp_products

      // start filter services
      let temp_services = []
      _.each(services, function(services) {
        if (temp_services.hasOwnProperty(services)) {
          temp_services[services] += 1
        } else {
          temp_services[services] = 1
        }
      })

      temp_services = _.extend({}, temp_services)
      let sorted_services_services = Object.keys(temp_services).sort()
      _.each(sorted_services_services, function(key) {
        let val = temp_services[key]
        delete temp_services[key]
        temp_services[key] = val
      })
      this.services = temp_services

    // end
    },

  },

  methods: {
    selectCertType (type) {
      this.feching = true
      let index = this.cert_type.indexOf(type)
      if (index >= 0) {
        this.cert_type.splice(index, 1)
      } else {
        this.cert_type.push(type)
      }

      let temp_featured_companies = []
      this.category.companies.map(function(company, key) {
        if (company.featured) {
          if (this.cert_type.length) {
            let company_cert_types = company.certifications.map(function(certification) {
              return certification.pivot.type
            })
            if (_.intersection(this.cert_type, company_cert_types).length > 0) {
              temp_featured_companies.push(company)
            }
          } else {
            temp_featured_companies.push(company)
          }
        }
      }.bind(this))

      this.featuredcompanies = _.shuffle(temp_featured_companies).slice(0, 4)
      this.feching = false
    },


    async search (str) {
      await this.$store.dispatch('auth/setSearch', str)
      this.$router.push({ name: 'company-search' })
    }
  }

}
</script>

<style>
.slim-padding {
  padding: 13px;
  padding-top: 0;
}

.product-service-wrap li.nav-item {
  border-bottom: 1px solid #e8eced;
}

.product-service-wrap li.nav-item:last-child {
  border-bottom: 0;
}

.product-service-wrap li.nav-item:hover {
    background-color: #fbfbfb;
}

.product-service-wrap li a {
  color: #777777;
}

</style>