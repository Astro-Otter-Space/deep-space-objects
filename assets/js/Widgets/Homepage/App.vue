<template>
    <div id="main">
      <Slider/>
      <SearchAutocomplete/>
    </div>
</template>

<script>
  import Slider from './components/Slider'
  import SearchAutocomplete from './components/SearchAutocomplete'

  export default {
    name: "app",
    components: {Slider, SearchAutocomplete}
  }
</script>
