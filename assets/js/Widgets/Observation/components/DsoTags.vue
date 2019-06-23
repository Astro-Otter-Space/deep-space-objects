<!--https://medium.com/js-dojo/laravel-tags-input-with-autocomplete-using-vuejs-4fceca99b86e-->
<template>
  <tags-input
    element-id="add_observation_dsoList"
    :existing-tags="existingTags"
    placeholder="Search test"
    :typeahead="typeahead"
    typeahead-style="dropdown"
    wrapper-class="Form__input"
    @keyup.native="getListDso"
  >
  </tags-input>
</template>

<script>
  import VoerroTagsInput from '@voerro/vue-tagsinput';
  import axios from 'axios';

  export default {
    name: "App",
    components: {
      "tags-input": VoerroTagsInput
    },
    data() {
      return {
        existingTags: {},
        typeahead: true
      }
    },
    methods: {
      getListDso(e) {
        let paramText = "";
        if (e !== undefined) {
          paramText = e.target.value;
        }

        this.existingTags = {};
        axios.get('/_search_dso_observation', {params: {'q': paramText}})
          .then((dso) => {
            dso.data.forEach(el => {
              this.existingTags[el] = el;
            });
          });
      }
    },
    mounted() {
      this.$el.querySelector('#add_observation_dsoList').setAttribute('name', 'add_observation[dsoList]');
      this.getListDso();
    }
  }
</script>