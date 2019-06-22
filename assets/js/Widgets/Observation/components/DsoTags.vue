<template>
  <tags-input
    element-id="dsoList"
    v-model="selectedTags"
    placeholder="Search test"
    :typeahead="typeahead"
    typeahead-style="dropdown"
    wrapper-class="Form__input"
  >
  <!--    @keyup.native="getListDso"-->
  </tags-input>
</template>
<!--https://medium.com/js-dojo/laravel-tags-input-with-autocomplete-using-vuejs-4fceca99b86e-->
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
        selectedTags: [],
        typeahead: true
      }
    },
    methods: {
      getListDso(e) {
        let paramText = "";
        if (e !== undefined) {
          paramText = e.target.value;
        }

        this.selectedTags = [];
        axios.get('/_search_dso_observation', {params: {'q': paramText}})
          .then((dso) => {
            dso.data.forEach(el => {
              this.selectedTags.push(el)
            });
          });
      }
    },
    mounted() {
      this.getListDso();
    }
  }
</script>