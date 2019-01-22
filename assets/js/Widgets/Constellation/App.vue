<template>
  <div>
    <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <h2 class="Dso__title">
          {{ title }}
        </h2>
      </div>
    </section>
  </div>
</template>

<script>

  import ImageHeader from './../Dso/components/Imageheader';

  let coverImage = document.querySelector('div[data-const-widget]').dataset.imgcover;
  let title = document.querySelector('div[data-const-widget]').dataset.title;

  export default {
    name: "App",
    components: {
      ImageHeader
    },
    data () {
      return {
        imageCover: coverImage,
        title: title,
      }
    }
  }
</script>
