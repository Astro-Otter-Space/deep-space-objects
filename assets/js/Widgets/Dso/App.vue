<template>
  <div id="app">
    <div class="dso_header">
      <image-header/>
    </div>
    <div class="dso_slider">
      <images-dso-slider
        :flux-images="imagesDso"
      />
    </div>
  </div>
</template>

<script>
  import ImageHeader from './components/Imageheader'
  import ImagesDsoSlider from './components/ImageSlider'

  let images = document.querySelector('div[data-dso-widget]').dataset.images;

  export default {
    name: "App",
    components: {
      ImageHeader,
      ImagesDsoSlider
    },
    data () {
      return {
        imagesDso: images
      }
    }
  }
</script>
