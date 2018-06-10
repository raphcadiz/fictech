<template>
  <div>
    <card class="mb-4">
      <h1>{{company.name}}</h1>
      <template v-if="company.services && company.services.length">
        <span v-for="(service, index) in company.services">{{service.name}} {{(index != company.services.length-1) ? ', ' : ''}}</span>
      </template>
    </card>

    <card :title="'About Us'" class="mb-4">
      {{company.description}}
    </card>

    <card :title="'Company Details'" class="mb-4">
      <strong>Website</strong>
      <p>{{company.website}}</p>

      <strong>Year Founded</strong>
      <p>{{company.year_founded}}</p>

      <strong>Company Size</strong>
      <p>{{company.company_size}} Employee/Employees</p>
    </card>

    <card :title="'Locations'" class="mb-4">
      <template v-if="company.locations && company.locations.length">
        <div  v-for="location in company.locations">
          <strong>{{location.name}}</strong>
          <p>{{location.address}}</p>
        </div>
      </template>

      <template v-else>
        <p> No locations found.</p>
      </template>
    </card>

    <card :title="'Services'" class="mb-4">
      <template v-if="company.services && company.services.length">
        <div  v-for="service in company.services">
          <ul>
            <li>{{service.name}}</li>
          </ul>
        </div>
      </template>

      <template v-else>
        <p> No services found.</p>
      </template>
    </card>

    <card :title="'Products'" class="mb-4">
      <template v-if="company.products && company.products.length">
        <div  v-for="product in company.products">
          <ul>
            <li>{{product.name}}</li>
          </ul>
        </div>
      </template>

      <template v-else>
        <p> No products found.</p>
      </template>
    </card>

  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: 'Company' }
  },

  created () {
    this.$store.dispatch('company/GET_COMPANY', this.$route.params.id)
  },

  computed: mapGetters({
    company: 'company/company'
  })

}
</script>
