<template>
  <div>

    <section class="banner col-md-12">
      <h1 class="banner__headline">Drilling Equipment</h1>
    </section>
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
                  <img src="../../images/logo-thumbnail.jpg" />
                  <div class="card-body slim-padding">
                      <h4 class="featured__company-name">{{company.name}}</h4>
                      <p class="featured__company-category"><span v-for="(service, index) in company.services">{{service.name}} {{(index != company.services.length-1) ? ', ' : ''}}</span></p>
                      <p class="featured__company-address">{{company.locations[0].address}}</p>
                      <hr />
                      <p class="featured__company-description">{{company.description.substr(0, 60)}}...</p>
                  </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-4">
          <h2 class="advanced-search-filters__heading">Company Categories</h2>

          <template v-for="categories in companycategories">
            <div class="row mb-4">
              <template v-for="category in categories">
                  <div class="col-md-4">
                    <div class="card">
                      <div class="card-body slim-padding">
                          <h4 class="advanced-search-filters__head-title"><strong>{{category.name}}</strong> Found: <strong>{{category.companies.length}}</strong></h4>
                          <p class="advanced-search-filters__popular-filters">Services and Products</p>
                          <template v-for="(productservice, index) in category.top_products_services">
                              <a class="advanced-search-filters__sublink1" href="#">{{index}} ({{productservice}})</a>
                          </template>
                          <router-link :to="{ name: 'show-category', params: { id: category.id } }" class="advanced-search-filters__view-all" title="view">
                              View all {{category.name}}
                              <fa icon="angle-double-right" fixed-width/>
                          </router-link>
                      </div>
                    </div>
                  </div>
              </template>
            </div>
          </template>
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
    return { title: this.$t('dashboard') }
  },

  data () {
    return {
      feching: false,
      featuredcompanies: [],
      companycategories: [],
      cert_type: []
    }
  },

  created () {
    this.$store.dispatch('company/GET_FEATURED_COMPANIES')
    this.$store.dispatch('company_category/GET_COMPANY_CATEGORIES', {unfiltered: 1})
  },

  computed: mapGetters({
    featured_companies: 'company/featured_companies',
    company_categories: 'company_category/company_categories'
  }),

  watch: {
    featured_companies () {
      this.featuredcompanies = _.shuffle(this.featured_companies).slice(0, 4)
      this.feching = false
    },

    company_categories () {
      let vue = this
      let categories = []
      _.each(this.company_categories, function(category) {
        if (category.companies && category.companies.length) {

          let products_services = []
          _.each(category.companies, function(company){
            _.each(company.products, function(product) {
              products_services.push(product.name)
            })

            _.each(company.services, function(service) {
              products_services.push(service.name)
            })
          })

          let temp_top_products_services = []
          _.each(products_services, function(productsservices) {
            if (temp_top_products_services.hasOwnProperty(productsservices)) {
              temp_top_products_services[productsservices] += 1
            } else {
              temp_top_products_services[productsservices] = 1
            }
          })

          temp_top_products_services = _.extend({}, temp_top_products_services)
          let top_products_services = _.fromPairs(_.sortBy(_.toPairs(temp_top_products_services), 1).reverse())

          let sliced_ps = []
          let count = 0
          _.each(top_products_services, function(tp, x) {
            if (count < 2) {
              sliced_ps[x] = tp
            }
            count++
          })

          

          let cat = {
            id: category.id,
            name: category.name,
            companies: category.companies,
            top_products_services: _.extend({}, sliced_ps)
          }

          categories.push(cat)
        }
      })
      this.companycategories = _.chunk(categories, 3)
    }
  },

  methods: {
    selectCertType (type) {
      let index = this.cert_type.indexOf(type)
      if (index >= 0) {
        this.cert_type.splice(index, 1)
      } else {
        this.cert_type.push(type)
      }
      this.feching = true
      this.$store.dispatch('company/GET_FEATURED_COMPANIES', {cert_type: this.cert_type})
    }
  }

}
</script>

<style>
.slim-padding {
  padding: 13px;
  padding-top: 0;
}
</style>