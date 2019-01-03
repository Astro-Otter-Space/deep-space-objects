<template>
  <autocomplete
    url="/_search"
    :placeholder="searchPlaceholder"
    :min="3"
    param="q"
    anchor="value"
    label="label"
    :classes="{ wrapper: 'AppSearch__wrapper', input: 'AppSearch__inputText', list: 'AppSearch__list' }"
    :onSelect="callbackDemo"
  >
  </autocomplete>
</template>
<script>
  import Autocomplete from 'vue2-autocomplete-js'
  require('vue2-autocomplete-js/dist/style/vue2-autocomplete.css')

  export default {
    name: 'Searchautocomplete',
    components: {
      Autocomplete
    },
    props: {
      searchPlaceholder: {
        default: '',
        type: String
      }
    },
    methods: {
      callbackDemo(obj) {
        console.log(obj.id);
        // router.push({ name: 'dso_show', params: {id: obj.id}})
      }
    }
  }
</script>
