<template>
  <div class="m-select-wrapper">
    <template v-for="(item, index) in value">
      <span class="tag-holder badge badge-pill badge-primary mr-1" @click="removeItem(index)">{{item}}</span>
    </template>
    <input type="text" v-model="tag" @keypress.enter.prevent="addTag()">
  </div>
</template>

<script>
  export default {
    name: 'Multiselect',
    props: ['value'],

    data () {
      return {
        tag: ''
      }
    },

    methods: {
      addTag () {
        if ( this.tag == '' ) {
          return false;
        }
        this.value.push(this.tag)
        this.tag = ''
        this.$emit('input', this.value)
      },

      removeItem (index) {
        this.value.splice(index, 1);
      }
    }
  }
</script>

<style>
.m-select-wrapper {
    border: 1px solid #ced4da;
    height: auto;
    padding: 5px 10px;
    border-radius: 2px;
}

.m-select-wrapper input[type=text] {
  border: 0;
}

.m-select-wrapper input[type=text]:focus {
  outline: 0;
  outline-width: 0
}

.tag-holder {
  cursor: pointer;
}
</style>