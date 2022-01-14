<template>
  <div>
    <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <h1 class="Dso__title">
          {{ title }}
        </h1>

        <!--List DSo-->
        <div class="Dso__slider" v-if="0 < listConstellations.length">
          <cards-grid
            :items="listConstellations"
            :show-controls="true"
            :list-controls="controls"
          >
          </cards-grid>
        </div>
      </div>

      <back-to-top visibleoffset="10" bottom="25px" right="25px" text="">
        <svgicon name="up" width="40" height="40"></svgicon>
      </back-to-top>
    </section>
  </div>
</template>

<script>
  import {default as mode} from './../../components/night_mode';
  import ImageHeader from './../Dso/components/Imageheader';
  import CardsGrid from './../Dso/components/CardsGrid';
  import './../Icons/up';
  import BackToTop from 'vue-backtotop';

  let title = document.querySelector('div[data-constellations]').dataset.titleConst;
  let listConstellations = JSON.parse(document.querySelector('div[data-constellations]').dataset.listConst);
  let listFilter = JSON.parse(document.querySelector('div[data-constellations]').dataset.filter);
  let coverImage = document.querySelector('div[data-constellations]').dataset.imgcover;

  export default {
    name: "ListApp",
    components: {
      ImageHeader,
      CardsGrid,
      BackToTop
    },
    data() {
      return {
        imageCover: coverImage,
        title: title,
        listConstellations: listConstellations,
        controls: listFilter
      }
    },
    mounted() {
      let listItems = [
        '.Dso__main',
        '.Dso__Form',
        '.Dso__description',
      ];

      let listMultiItems = [
        '.Dso__title',
        '.Dso__list a',
        '.Dso__td',
        '.appGridFacet__item a',
        'article.card',
        '.Form__input',
        '.Form__select',
        '.Form__textarea',
        'td',
        'a'
      ];
      mode.setNightMode(listItems, listMultiItems);
    }
  }
</script>
