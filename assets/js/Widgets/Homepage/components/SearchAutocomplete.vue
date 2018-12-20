<template>
  <autocomplete
    url=""
    :placeholder="searchPlaceholder"
    :min="3"
    :param="search"
    :onSelect="callbackDemo"
  >
  </autocomplete>
</template>
<script>
  import Autocomplete from 'vue2-autocomplete-js'

  export default {
    'name': 'Search',
    components: {
      Autocomplete
    },
    props: {
      searchPlaceholder: {
        default: '',
        type: String
      }
    }
  }
</script>
