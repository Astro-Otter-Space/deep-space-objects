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

        <!--Map-->
        <div>
          <a v-bind:href="linkDownload" target="_blank" alt="Download map">
            <img v-bind:src="map" v-bind:title="title">
          </a>
        </div>

        <!--List DSo-->
        <div class="Dso__slider" v-if="0 < itemsDso.length">
          <cards-grid
            :items="itemsDso"
            :show-controls="false"
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

  let map = document.querySelector('div[data-const-widget]').dataset.map;
  let coverImage = document.querySelector('div[data-const-widget]').dataset.imgcover;
  let title = document.querySelector('div[data-const-widget]').dataset.title;
  let dsoList = JSON.parse(document.querySelector('div[data-const-widget]').dataset.listDso);
  let linkDownload = document.querySelector('div[data-const-widget]').dataset.link;

  export default {
    name: "App",
    components: {
      ImageHeader,
      CardsGrid
    },
    data () {
      return {
        map: map,
        imageCover: coverImage,
        title: title,
        itemsDso: dsoList,
        linkDownload: linkDownload
      }
    }
  }
</script>
