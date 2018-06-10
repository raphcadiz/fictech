<template>
  <card :title="$t('companies')">
    
    <router-link :to="{ name: 'add-company' }" class="btn btn-primary mb-2 float-right">
        Add Company
    </router-link>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
          <template v-if="companies && companies.length">
            <tr v-for="(company, index) in companies">
              <td>{{company.name}}</td>
              <td width="300">
                <router-link :to="{ name: 'edit-company', params: { id: company.id } }" class="btn btn-primary" title="edit">
                    <fa icon="edit" fixed-width/>
                </router-link>
                <router-link :to="{ name: 'show-company', params: { id: company.id } }" class="btn btn-success" title="view">
                    <fa icon="eye" fixed-width/>
                </router-link>
                <a href="#" class="btn btn-danger" @click.prevent="deleteCompany(company.id, index)" title="delete">
                  <fa icon="trash" fixed-width/>
                </a>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td colspan="2" align="center">No companies added.</td>
            </tr>
          </template>
        </tbody>
    </table>
  </card>
</template>

<script>
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'
import axios from 'axios'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: 'Companies' }
  },

  created () {
    this.$store.dispatch('company/GET_COMPANIES', {})
  },

  computed: mapGetters({
    companies: 'company/companies'
  }),

  watch: {
    companies: function() {
      // console.log(this.companies)
    }
  },

  methods: {
    deleteCompany(id, index) {
      swal({
        title: 'Are you sure?',
        text: "You won't be able to revet this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.value) {
          const { data } = await axios.delete('/api/company/' + id)

          this.$store.dispatch('company/GET_COMPANIES', {})
        }
      })
    }
  }

}
</script>
