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
        <breadcrumbs
          :links="linksBreadcrumbs"
        ></breadcrumbs>

        <h1 class="Dso__title">
          {{ title }}
        </h1>

        <a id="#description"></a>
        <div class="Dso__description" v-show="0 < description.length">
          {{ description }}
        </div>

        <!--List DSo-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <cards-grid
            :items="itemsDso"
            :show-controls="true"
            :list-controls="controls"
            :show-ajax="true"
            :url-ajax-data="urlAjax"
          >
          </cards-grid>
        </div>

        <!--Sky Map-->
        <a id="#map"></a>
        <div class="Dso__map">
          <h3 class="Dso__title">{{ titleMap }}</h3>
          <div class="map" id="map"></div>
          <div class="Dso__map-legend" v-if="0 <  Object.keys(legendMap).length">
            <ul>
              <li v-for="(value, key) in legendMap">
                <svgicon name="circle" width="10" height="10" :color="listColors[key]"></svgicon> {{value}}
              </li>
            </ul>
          </div>
          <a v-bind:href="linkDownload" target="_blank" alt="Download map" rel="nofollow">
            Download map
<!--            <img v-bind:src="map" v-bind:title="title">-->
          </a>
        </div>
      </div>

      <back-to-top visibleoffset="10" bottom="25px" right="25px" text="">
        <svgicon name="up" width="40" height="40"></svgicon>
      </back-to-top>
    </section>
  </div>
</template>

<script>

  import ImageHeader from './../Dso/components/Imageheader';
  import Breadcrumbs from "../App/Breadcrumbs";
  import CardsGrid from './../Dso/components/CardsGrid';
  import { color } from './../../legendSkyMap';
  import './../Icons/up';
  import BackToTop from 'vue-backtotop';

  let map = document.querySelector('div[data-const-widget]').dataset.map;
  let coverImage = document.querySelector('div[data-const-widget]').dataset.imgcover;
  let title = document.querySelector('div[data-const-widget]').dataset.title;
  let breadcrumbsData = JSON.parse(document.querySelector('div[data-const-widget]').dataset.breadcrumbs);
  let titleMap = document.querySelector('div[data-const-widget]').dataset.titleMap;
  let description = document.querySelector('div[data-const-widget]').dataset.desc;
  let dsoList = JSON.parse(document.querySelector('div[data-const-widget]').dataset.listDso);
  let linkDownload = document.querySelector('div[data-const-widget]').dataset.link;
  let legendMap = JSON.parse(document.querySelector('div[data-const-widget]').dataset.legendMap);
  let listFilter = JSON.parse(document.querySelector('div[data-const-widget]').dataset.filter);
  let urlAjax = document.querySelector('div[data-const-widget]').dataset.urlAjax
  export default {
    name: "App",
    components: {
      ImageHeader,
      Breadcrumbs,
      CardsGrid,
      BackToTop
    },
    data () {
      return {
        map: map,
        imageCover: coverImage,
        linksBreadcrumbs: breadcrumbsData,
        title: title,
        description: description,
        itemsDso: dsoList,
        linkDownload: linkDownload,
        titleMap: titleMap,
        legendMap: legendMap, //Object.keys(legendMap).map((key) => legendMap[key]),
        listColors: color,
        controls: listFilter,
        urlAjax: urlAjax
      }
    }
  }
</script>
