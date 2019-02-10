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
        <div class="Dso__slider" v-if="0 < listConstellations.length">
          <cards-grid
            :items="listConstellations"
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

  let title = document.querySelector('div[data-constellations]').dataset.titleConst;
  let listConstellations = JSON.parse(document.querySelector('div[data-constellations]').dataset.listConst);

  export default {
    name: 'ListApp',
    components: {
      ImageHeader,
      CardsGrid
    },
    data() {
      return {
        imageCover: '/build/images/ursa_major_illustration.jpg',
        title: title,
        listConstellations: listConstellations
      }
    }
  }
</script>