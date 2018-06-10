<template>
  <div>
    <card :title="'Add Category'">
      <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <alert-success :form="form" :message="'Category Added!'"/>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Name</label>
          <div class="col-md-7">
            <input v-model="form.name" type="text" name="name" class="form-control"
              :class="{ 'is-invalid': form.errors.has('name') }">
            <has-error :form="form" field="name"/>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group row mt-4">
          <div class="col-md-9 ml-md-auto">
            <v-button type="success" :loading="form.busy">Submit</v-button>
          </div>
        </div>
      </form>
    </card>

  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: 'Add Category' }
  },

  data: () => ({
    form: new Form({
      name: ''
    }),
  }),

  created () {
    this.$store.dispatch('company_category/GET_COMPANY_CATEGORY', this.$route.params.id)
  },

  computed: mapGetters({
    category: 'company_category/category'
  }),

  watch: {
    category: function () {
        // Fill the form with user data.
        this.form.keys().forEach(key => {
          this.form[key] = this.category[key]
        })
    }
  },

  methods: {
    async update () {

      const { data } = await this.form.patch('/api/company-category/' + this.$route.params.id)

    }

  }

}
</script>
