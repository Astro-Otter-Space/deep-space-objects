<template>
  <tags-input
    element-id="dsoList"
    v-model="selectedTags"
    placeholder="Search test"
    :typeahead="typeahead"
    typeahead-style="dropdown"
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
        selectedTags: [],
        typeahead: true
      }
    },
    methods: {
      getListDso(e) {
        let paramText = e.target.value;
        this.selectedTags = [];
        axios.get('/_search_dso_observation', {params: {'q': paramText}})
          .then((dso) => {
            dso.data.forEach(el => {
              this.selectedTags.push(el)
            });
          });
      }
    }
  }
</script>