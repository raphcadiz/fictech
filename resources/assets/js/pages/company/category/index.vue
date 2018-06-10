<template>
  <card :title="'Categories'">
    
    <router-link :to="{ name: 'add-category' }" class="btn btn-primary mb-2 float-right">
        Add Category
    </router-link>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
          <template v-if="company_categories && company_categories.length">
            <tr v-for="(category, index) in company_categories">
              <td>{{category.name}}</td>
              <td width="300">
                <router-link :to="{ name: 'edit-category', params: { id: category.id } }" class="btn btn-primary" title="edit">
                    <fa icon="edit" fixed-width/>
                </router-link>
                <router-link :to="{ name: 'show-category', params: { id: category.id } }" class="btn btn-success">
                  <fa icon="eye" fixed-width/>
                </router-link>
                <a href="#" class="btn btn-danger" @click.prevent="deleteCategory(category.id, index)" title="delete">
                  <fa icon="trash" fixed-width/>
                </a>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td colspan="2" align="center">No categories added.</td>
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
    return { title: 'Categories' }
  },

  created () {
    this.$store.dispatch('company_category/GET_COMPANY_CATEGORIES', {})
  },

  computed: mapGetters({
    company_categories: 'company_category/company_categories'
  }),

  watch: {
    company_categories: function() {
      // console.log(this.company_categories)
    }
  },

  methods: {
    deleteCategory(id, index) {
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
          const { data } = await axios.delete('/api/company-category/' + id)

          this.$store.dispatch('category/GET_COMPANY_CATEGORIES', {})
        }
      })
    }
  }

}
</script>
