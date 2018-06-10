<template>
  <div>
    <card :title="'Add Company'">
      <form @submit.prevent="add" @keydown="form.onKeydown($event)">
        <alert-success :form="form" :message="'Company Added!'"/>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Name</label>
          <div class="col-md-7">
            <input v-model="form.name" type="text" name="name" class="form-control"
              :class="{ 'is-invalid': form.errors.has('name') }">
            <has-error :form="form" field="name"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Category</label>
          <div class="col-md-7">
              <select v-model="form.category_id" name="category_id" class="form-control"
              :class="{ 'is-invalid': form.errors.has('name') }">
                <option v-for="category in company_categories" v-bind:value="category.id">{{category.name}}</option>
              </select>
            <has-error :form="form" field="category_id"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">About</label>
          <div class="col-md-7">
            <textarea v-model="form.description" class="form-control" name="description" rows="4"
              :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
            <has-error :form="form" field="description"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Website</label>
          <div class="col-md-7">
            <input v-model="form.website" type="text" name="website" class="form-control"
              :class="{ 'is-invalid': form.errors.has('website') }">
            <has-error :form="form" field="website"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Year Founded</label>
          <div class="col-md-7">
            <input v-model="form.year_founded" type="text" name="year_founded" class="form-control"
              :class="{ 'is-invalid': form.errors.has('year_founded') }">
            <has-error :form="form" field="year_founded"/>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Company Size</label>
          <div class="col-md-7">
              <select v-model="form.company_size" name="company_size" class="form-control">
                <option value="Solo">Solo</option>
                <option value="2-10">2-10 Employees</option>
                <option value="10-25">10-25 Employees</option>
                <option value="25-50">25-50 Employees</option>
                <option value="50-100">50-100 Employees</option>
                <option value="100-200">100-200 Employees</option>
                <option value="200-500">200-500 Employees</option>
                <option value="500+">500+ Employees</option>
              </select>
            <has-error :form="form" field="company_size"/>
          </div>
        </div>

        <hr class="mb-4 mt-4" />
        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Locations</label>
          <div class="col-md-7">
            <table class="table table-striped" v-if="locations && locations.length">
              <tbody>
                <tr v-for="(location, index) in locations">
                  <td>{{location.address}}</td>
                  <td width="150">
                    <a href="#" class="btn btn-primary" @click.prevent="editLocation(index)"> 
                      <fa icon="edit" fixed-width/>
                    </a>
                    <a href="#" class="btn btn-danger" @click.prevent="deleteLocation(index)"> 
                      <fa icon="trash" fixed-width/>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="row mb-2">
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Name" v-model="editing_location.name" />
              </div>
              <div class="col-md-8 g-map-autocomplete" id="g-map-autocomplete-wrap">
                  <gmap-autocomplete
                    @place_changed="setPlace" :value="editing_location.address" placeholder="Enter Location">
                  </gmap-autocomplete>
              </div>
            </div>

            <a href="#" class="btn btn-outline-primary " @click.prevent="saveLocation">Save Location</a>
          </div>
        </div>

        <hr class="mb-4 mt-4" />
        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Certifications</label>
          <div class="col-md-7">
            <div v-if="certifications && certifications.length" class="certifications-div mb-4">
                <span class="tag-holder badge badge-pill badge-primary mr-1" v-for="(certification, index) in certifications" @click="editCertification(index)">{{certification.name}}</span>
            </div>
            <div class="row">
              <div class="col-md-3">
                <select class="form-control" v-model="editing_certification.type">
                  <option value="iso_certified">ISO Certified</option>
                  <option value="api_certified">API Certified</option>
                  <option value="bbb">Better Business Bureau</option>
                </select>
              </div>
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Name" v-model="editing_certification.name" />
              </div>
              <div class="col-md-5">
                <a v-if="editing_certification_index != null" href="#" class="btn btn-outline-danger" @click.prevent="deleteCertification">Delete</a>
                <a href="#" class="btn btn-outline-primary" @click.prevent="saveCertification">Save</a>
              </div>
            </div>
            <!-- <multiselect v-model="certifications"></multiselect> -->
          </div>
        </div>

        <hr class="mb-4 mt-4" />
        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Services</label>
          <div class="col-md-7">
            <multiselect v-model="services"></multiselect>
          </div>
        </div>

        <hr class="mb-4 mt-4" />
        <div class="form-group row">
          <label class="col-md-3 col-form-label text-md-right">Products</label>
          <div class="col-md-7">
            <multiselect v-model="products"></multiselect>
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
import swal from 'sweetalert2'
import $ from 'jquery'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: 'Add Company' }
  },

  data: () => ({
    form: new Form({
      name: '',
      category_id: '',
      description: '',
      website: '',
      year_founded: '',
      company_size: 'Solo',
      locations: [],
      services: []
    }),

    editing_location: {
      'name': '',
      'address': '',
      'lat': '',
      'lng': ''
    },
    editing_location_index: null,
    locations: [],

    editing_certification: {
      'type': '',
      'name': ''
    },
    editing_certification_index: null,
    certifications: [],

    services: [],
    products: [],
    place: ''
  }),

  created () {
    this.$store.dispatch('company_category/GET_COMPANY_CATEGORIES', {})
  },

  computed: mapGetters({
    company_categories: 'company_category/company_categories'
  }),

  methods: {
    async add () {
      this.form.locations = this.locations
      this.form.certifications = this.certifications
      this.form.services = this.services,
      this.form.products = this.products

      const { data } = await this.form.post('/api/company')

      this.form.reset()
      this.locations = []
      this.certifications = []
      this.services = []
      this.products = []
    },

    setPlace(place) {
      this.place = place
    },

    saveLocation () {
      let address_value = $('#g-map-autocomplete-wrap input[type=text]').val();

      if (this.editing_location.name == '' ||  address_value == '')
        return

      if (this.editing_location_index != null) {
         let loc_info = { 
          name: this.editing_location.name, 
          address: (this.place) ? this.place.formatted_address : address_value
        }

        if (address_value !== this.editing_location.address) {
          loc_info['lat'] = this.place.geometry.location.lat()
          loc_info['lng'] = this.place.geometry.location.lng()
        } else {
          loc_info['lat'] = this.editing_location.lat
          loc_info['lng'] = this.editing_location.lng
        }

        this.locations[this.editing_location_index] = loc_info

      } else {
        this.locations.push({ 
          name: this.editing_location.name, 
          address: (this.place) ? this.place.formatted_address : address_value,
          lat: (this.place) ? this.place.geometry.location.lat() : this.editing_location.lat,
          lng: (this.place) ? this.place.geometry.location.lng() : this.editing_location.lng
        })
      }

      $('#g-map-autocomplete-wrap input[type=text]').val('');
      this.place = ''
      this.editing_location = { 'name': '', 'address': '', 'lat': '', 'lng': '' }
      this.editing_location_index = null
    },

    editLocation (index) {
      this.editing_location_index = index
      this.editing_location = this.locations[index]
    },

    deleteLocation (index) {
      swal({
        title: 'Are you sure?',
        text: "Once company is saved you won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          this.locations.splice(index, 1);
        }
      })
    },

    saveCertification () {
      if (this.editing_certification_index != null) {
        this.certifications[this.editing_certification_index] = {
          type: this.editing_certification.type,
          name: this.editing_certification.name
        }
      } else {
        this.certifications.push({
          type: this.editing_certification.type,
          name: this.editing_certification.name
        })
      }

      this.editing_certification = {type: '', name: ''}
      this.editing_certification_index = null
      console.log(this.certifications)
    },

    editCertification (index) {
      this.editing_certification_index = index
      this.editing_certification = this.certifications[index]
    },

    deleteCertification () {
      this.certifications.splice(this.editing_certification_index, 1)
      this.editing_certification = {type: '', name: ''}
      this.editing_certification_index = null
    }

  }

}
</script>

<style>
.g-map-autocomplete input {
  display: block;
  width: 100%;
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.125rem;
  -webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
  transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
}

.certifications-div {
  border: 1px solid #ced4da;
  padding: 7px;
  border-radius: 3px;
}
</style>
