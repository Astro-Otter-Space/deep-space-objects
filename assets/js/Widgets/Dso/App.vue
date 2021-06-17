<template>
  <div>
    <div v-if="imageCover !== '/build/images/default_large.jpg'" class="Dso_header">
      <image-header
        :cover-image="imageCover"
        :title="title"
        :alt-image="imageCoverAlt"
      />
    </div>
    <section class="Dso__main">
      <div v-bind:class="getHeaderClass()">
        <!--Title-->
        <h1 class="Dso__title">
          {{ title }}
        </h1>

        <!-- Breadcrumbs -->
        <breadcrumbs
          :links="linksBreadcrumbs"
        ></breadcrumbs>

        <div class="Dso__text">
          <div v-if="imagePositionMap">
            <img style="filter: grayscale(100%);" v-bind:src="imagePositionMap" v-bind:class="classVignette" v-bind:alt="altImage" />
          </div>

          <div>
            <div class="Dso__description" v-if="0 < description.length">
              {{description}}
            </div>
            <div>
              <div>
                <svgicon name="clock" width="16" height="16"></svgicon>
                <span v-html="labels.last_update_item"></span>
              </div>
              <div>
                <svgicon name="star" width="16" height="16"></svgicon>
                <a v-on:click="addToFavorites">{{labels.add_favorite}}</a>
              </div>
            </div>
          </div>
        </div>

        <div class="share-network-list">
          <ShareNetwork
            v-for="network in networks"
            :network="network.network"
            :key="network.network"
            :style="{backgroundColor: network.color}"
            :url="sharing.url"
            :title="sharing.title"
            :description="sharing.description"
            :hashtags="sharing.hashtags"
            :twitterUser="sharing.twitterUser"
          >
            <svgicon v-bind:name="network.network" width="30" height="30"></svgicon>
            <span>{{ network.name }}</span>
          </ShareNetwork>
        </div>

        <!--Description-->
        <a id="#description"></a>


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
          <h3 class="Dso__title">{{labels.galery}}</h3>
          <images-dso-slider
            :fluxImages="imagesDso"
          />
          <div class="Dso__text" v-html="labels.astrobin_msg"></div>
        </div>

        <!--Sky Map-->
        <a id="#map"></a>
        <div class="Dso__list">
          <h3 class="Dso__title">{{labels.skymap}}</h3>
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
  import './../Icons/pinterest';
  import './../Icons/whatsapp';
  import './../Icons/email';
  import './../Icons/messenger';
  import './../Icons/up';
  import './../Icons/home';
  import './../Icons/clock';
  import BackToTop from 'vue-backtotop';
  import Breadcrumbs from "../App/Breadcrumbs";

  let coverImage = document.querySelector('div[data-dso-widget]').dataset.imgcover;
  let coverImageMap = document.querySelector('div[data-dso-widget]').dataset.imgcovermap;
  let coverImageAlt = document.querySelector('div[data-dso-widget]').dataset.imgcoveralt;
  let images = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.images);
  let title = document.querySelector('div[data-dso-widget]').dataset.title;
  let breadcrumbsData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.breadcrumbs);
  let description = document.querySelector('div[data-dso-widget]').dataset.description;
  let titleConst = document.querySelector('div[data-dso-widget]').dataset.titleConst;
  let tabData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dso);
  let astrobinMsg = document.querySelector('div[data-dso-widget]').dataset.astrobinMsg;
  let dsoList = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dsoConst);
  let filters = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.filter);
  let labels = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.labels);

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
        imagePositionMap: coverImageMap,
        imagesDso: images,
        linksBreadcrumbs: breadcrumbsData,
        title: title,
        labels: labels,
        titleConst: titleConst,
        gridColumns: ['col0', 'col1'],
        gridData: tabData,
        classTable: "Dso__table",
        classTr: "Dso__tr",
        classTd: "Dso__td",
        description: description,
        itemsDso: dsoList,
        filters: filters,
        bugAstrobin: false,
        sharing: {
          title: title,
          url: document.querySelector("link[rel='canonical']").href,
          description: description,
          hashtags: 'astronomy',
          twitterUser: 'otter_astro'
        },
        networks: [
          { network: 'facebook', name: 'Facebook', color: '#1877f2' },
          { network: 'twitter', name: 'Twitter', color: '#1da1f2' },
          { network: 'whatsapp', name: 'Whatsapp', color: '#25d366' },
          { network: 'messenger', name: 'Messenger', color: '#2529d8' },
          { network: 'pinterest', name: 'Pinterest', color: '#bd081c' },
          { network: 'email', name: 'Email', color: '#333333' },
        ],
        classVignette: "Vignettes__vignette"
      }
    },
    // TODO : better way
    // https://nehalist.io/directly-injecting-data-to-vue-apps-with-symfony-twig/
    // https://stackoverflow.com/questions/42269260/how-to-get-the-values-of-data-attributes-in-vuejs
    methods: {
      getHeaderClass: function () {
        if (this.imageCover !== '/build/images/default_large.jpg') {
          return 'Dso__container';
        } else {
          return 'Dso__container Dso__noHeader';
        }
      },
      addToFavorites: function () {
        if (window.sidebar) {        // Firefox
          window.sidebar.addPanel(this.sharing.url, this.sharing.title);
        } else {
          if (window.external && ('AddFavorite' in window.external)) {
            // Internet Explorer
            window.external.AddFavorite(this.sharing.url, this.sharing.title);
          } else {  // Opera, Google Chrome and Safari
            alert("Your browser doesn't support this example!");
          }
        }
      }
    }
  }
</script>
