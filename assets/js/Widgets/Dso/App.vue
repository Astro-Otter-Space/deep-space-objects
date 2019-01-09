<template>
  <div id="app">
    <div class="Dso_header">
      <image-header/>
    </div>
    <section class="Dso__container">
      <h2 class="Dso__title">{{ titleDso }}</h2>
      <div class="Dso__table">
      </div>
      <div class="Dso__slider">
        <images-dso-slider
          :fluxImages="imagesDso"
        />
      </div>
    </section>
  </div>
</template>

<script>
  import ImageHeader from './components/Imageheader'
  import ImagesDsoSlider from './components/ImageSlider'

  let images = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.images);
  let title = document.querySelector('div[data-dso-widget]').dataset.title;

  export default {
    name: "App",
    components: {
      ImageHeader,
      ImagesDsoSlider
    },
    data () {
      return {
        imagesDso: images,
        titleDso: title
      }
    }
  }
</script>
