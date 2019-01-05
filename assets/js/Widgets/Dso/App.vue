<template>
  <div id="app">
    <div class="dso_header">
      <imageheader/>
    </div>
    <div class="dso_slider">
      <slider/>
    </div>
  </div>
</template>

<script>
  import Imageheader from './components/Imageheader'
  import Slider from './components/Slider'

  export default {
    name: "App",
    components: {
      Imageheader,
      Slider
    }
  }
</script>
