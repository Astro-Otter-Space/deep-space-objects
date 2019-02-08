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

        <!--List DSo-->
        <div class="Dso__slider" v-if="0 < itemsDso.length">
          <cards-grid
            :items="itemsDso"
          >
          </cards-grid>
        </div>
      </div>
    </section>
  </div>
</template>

<script>

  import ImageHeader from './../Dso/components/Imageheader';
  import CardsGrid from './../Dso/components/CardsGrid';

  let coverImage = document.querySelector('div[data-const-widget]').dataset.imgcover;
  let title = document.querySelector('div[data-const-widget]').dataset.title;
  let dsoList = JSON.parse(document.querySelector('div[data-const-widget]').dataset.listDso);

  export default {
    name: "App",
    components: {
      ImageHeader,
      CardsGrid
    },
    data () {
      return {
        imageCover: coverImage,
        title: title,
        itemsDso: dsoList
      }
    }
  }
</script>
