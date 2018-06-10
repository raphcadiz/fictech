<template>
  <div>
    <card :title="'Add Category'">
      <form @submit.prevent="add" @keydown="form.onKeydown($event)">
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
    //
  },

  methods: {
    async add () {

      const { data } = await this.form.post('/api/company-category')

      this.form.reset()
    }

  }

}
</script>
