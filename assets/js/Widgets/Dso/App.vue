<template>
  <div>
    <div v-if="imageCover !== '/build/images/default_large.jpg'" class="Dso_header">
      <image-header
        :cover-image="imageCover"
        :alt-image="imageCoverAlt"
      />
    </div>
    <section class="Dso__main">
      <div v-bind:class="getHeaderClass()">

        <!-- Breadcrumbs -->
        <breadcrumbs
          :links="linksBreadcrumbs"
        ></breadcrumbs>

        <!--Title-->
        <h1 class="Dso__title">
          {{ title }}
        </h1>

        <social-sharing
          :url="urlShare"
          :title="title"
          :description="description"
          hashtags=""
          twitter-user=""
          inline-template
        >
          <div>
            <network network="facebook">
              <svgicon name="facebook" width="15" height="15"></svgicon>
            </network>
            <network network="twitter">
              <svgicon name="twitter" width="15" height="15"></svgicon>
            </network>
          </div>
        </social-sharing>

        <!--Description-->
        <a id="#description"></a>
        <div class="Dso__description" v-if="0 < description.length">
          {{description}}
        </div>

        <!--Table data-->
        <a id="#information"></a>
        <div class="Dso__data">
          <Table
            :columns="gridColumns"
            :data="gridData"
            :classTable="classTable"
            :classTr="classTr"
            :classTd="classTd"
          >
          </Table>
        </div>

        <!--Slider-->
        <a id="#gallery"></a>
        <div class="Dso__slider" v-if="0 < imagesDso.length">
          <h3 class="Dso__title">{{ titleGallery }}</h3>
          <images-dso-slider
            :fluxImages="imagesDso"
          />
          <div v-html="astrobinMsg"></div>
        </div>

        <!--Sky Map-->
        <a id="#map"></a>
        <div class="Dso__list">
          <h3 class="Dso__title">{{ titleMap }}</h3>
          <div class="map" id="map"></div>
          <legend><a href="https://github.com/ofrohn/d3-celestial" target="_blank" rel="noopener">Map by ofrohn/d3-celestial</a></legend>
        </div>

        <!--Constellation-->
        <a id="#constellation"></a>
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3 class="Dso__title">{{ titleConst }}</h3>
          <cards-grid
            :show-controls="true"
            :list-controls="filters"
            :items="itemsDso"
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
  import ImageHeader from './components/Imageheader'
  import ImagesDsoSlider from './components/ImageSlider'
  import Table from './../App/SimpleTable'
  import CardsGrid from './components/CardsGrid'
  import './../Icons/facebook';
  import './../Icons/twitter';
  import './../Icons/up';
  import './../Icons/home';
  import BackToTop from 'vue-backtotop';
  import Breadcrumbs from "../App/Breadcrumbs";

  let coverImage = document.querySelector('div[data-dso-widget]').dataset.imgcover;
  let coverImageAlt = document.querySelector('div[data-dso-widget]').dataset.imgcoveralt;
  let images = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.images);
  let title = document.querySelector('div[data-dso-widget]').dataset.title;
  let breadcrumbsData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.breadcrumbs);
  let description = document.querySelector('div[data-dso-widget]').dataset.description;
  let titleConst = document.querySelector('div[data-dso-widget]').dataset.titleConst;
  let titleGallery = document.querySelector('div[data-dso-widget]').dataset.titleGallery;
  let titleMap = document.querySelector('div[data-dso-widget]').dataset.titleMap;
  let tabData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dso);
  let astrobinMsg = document.querySelector('div[data-dso-widget]').dataset.astrobinMsg;
  let dsoList = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dsoConst);
  let filters = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.filter);

  export default {
    name: "App",
    components: {
      ImageHeader,
      ImagesDsoSlider,
      Breadcrumbs,
      Table,
      CardsGrid,
      BackToTop
    },
    data () {
      return {
        imageCover: coverImage,
        imageCoverAlt: coverImageAlt,
        imagesDso: images,
        linksBreadcrumbs: breadcrumbsData,
        title: title,
        titleGallery: titleGallery,
        titleConst: titleConst,
        titleMap: titleMap,
        gridColumns: ['col0', 'col1'],
        gridData: tabData,
        classTable: "Dso__table",
        classTr: "Dso__tr",
        classTd: "Dso__td",
        urlShare: document.querySelector("link[rel='canonical']").href,
        description: description,
        astrobinMsg: astrobinMsg,
        itemsDso: dsoList,
        filters: filters,
        bugAstrobin: false
      }
    },
    // TODO : better way
    // https://nehalist.io/directly-injecting-data-to-vue-apps-with-symfony-twig/
    // https://stackoverflow.com/questions/42269260/how-to-get-the-values-of-data-attributes-in-vuejs
    methods: {
      getHeaderClass: function() {
        if (this.imageCover !== '/build/images/default_large.jpg') {
          return 'Dso__container';
        } else {
          return 'Dso__container Dso__noHeader';
        }
      }
    }
  }
</script>
