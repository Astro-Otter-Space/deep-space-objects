<template>
  <div>
    <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
        :alt-image="title"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <h2 class="Dso__title">
          {{ title }}
        </h2>

        <a id="#description"></a>
        <div class="Dso__description" v-show="0 < description.length">
          {{ description }}
        </div>

        <!--List DSo-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <cards-grid
            :items="itemsDso"
            :show-controls="false"
          >
          </cards-grid>
        </div>

        <!--Sky Map-->
        <a id="#map"></a>
        <div class="Dso__map">
          <h3 class="Dso__title">{{ titleMap }}</h3>
          <div class="map" id="map"></div>
          <div class="Dso__map-legend" v-if="0 < legendMap.length">
            <ul>
              <li v-for="(legend, type) in legendMap">
                <svgicon name="circle" width="10" height="10" :color="listColors[type]"></svgicon> {{legend}} - {{type}}
              </li>
            </ul>
          </div>
          <a v-bind:href="linkDownload" target="_blank" alt="Download map">
            Download map
<!--            <img v-bind:src="map" v-bind:title="title">-->
          </a>
        </div>

      </div>

    </section>
  </div>
</template>

<script>

  import ImageHeader from './../Dso/components/Imageheader';
  import CardsGrid from './../Dso/components/CardsGrid';
  import { color } from './../../legendSkyMap';

  let map = document.querySelector('div[data-const-widget]').dataset.map;
  let coverImage = document.querySelector('div[data-const-widget]').dataset.imgcover;
  let title = document.querySelector('div[data-const-widget]').dataset.title;
  let titleMap = document.querySelector('div[data-const-widget]').dataset.titleMap;
  let description = document.querySelector('div[data-const-widget]').dataset.desc;
  let dsoList = JSON.parse(document.querySelector('div[data-const-widget]').dataset.listDso);
  let linkDownload = document.querySelector('div[data-const-widget]').dataset.link;
  let legendMap = JSON.parse(document.querySelector('div[data-const-widget]').dataset.legendMap);

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
        description: description,
        itemsDso: dsoList,
        linkDownload: linkDownload,
        titleMap: titleMap,
        legendMap: Object.keys(legendMap).map((key) => legendMap[key]),
        listColors: color
      }
    }
  }
</script>
