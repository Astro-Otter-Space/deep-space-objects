<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h1 class="Dso__title">{{ title }}</h1>
        <div>{{ desc }}</div>

        <!--List-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <div class="Dso__catalog_header">
            <h3>
              <!-- a v-bind:href="urlDownloadData" title="Download data">
                <svgicon name="file-download" width="30" height="30" color="#2B2A34"></svgicon>
              </a -->
              {{ nbItems }}
              <span v-show="0 < listFilters.length" >
                &nbsp;|&nbsp;
                Filters
              </span>
            </h3>

            <ul v-if="listFilters.length" class="Filters__list">
              <li v-for="filter in listFilters" class="Filters__badge">
                {{filter.label}}
                <a v-bind:href="filter.delete_url"><svgicon name="cross" width="15" height="15" color="#1ed760"></svgicon></a>
              </li>
            </ul>

          </div>
          <cards-grid
            :show-controls="false"
            :items="itemsDso"
            :list-facets="listFacets"
            :show-facets="showFacets"
          >
          </cards-grid>
          <pagination
            :current-page="currentPage"
            :last-page="totalPage"
            :link-previous="pagination.prev"
            :link-next="pagination.next"
          ></pagination>
        </div>
      </div>

      <back-to-top visibleoffset="10" bottom="25px" right="25px" text="">
        <svgicon name="up" width="40" height="40"></svgicon>
      </back-to-top>
    </section>
  </div>
</template>

<script>

  const DATA_SELECTOR = 'div[data-catalog-widget]';

  import {default as mode} from './../../components/night_mode';
  import ImageHeader from './components/Imageheader'
  import CardsGrid from './components/CardsGrid'
  import Pagination from './../App/Pagination'
  import './../Icons/cross';
  import './../Icons/up';
  import './../Icons/down';
  import './../Icons/up';
  import BackToTop from 'vue-backtotop';

  let title = document.querySelector(DATA_SELECTOR).dataset.title;
  let desc = document.querySelector(DATA_SELECTOR).dataset.desc;
  let dsoList = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.listDso);
  let listFacets = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.listFacets);
  let listFilters = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.selectedFilters);
  let currentPage = parseInt(document.querySelector(DATA_SELECTOR).dataset.page);
  let totalPage = parseInt(document.querySelector(DATA_SELECTOR).dataset.totalPage);
  let nbItems = document.querySelector(DATA_SELECTOR).dataset.totalDso;
  let showFacets = true;
  let pagination = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.pagination);
  let urlDownloadData = document.querySelector(DATA_SELECTOR).dataset.download;

  export default {
    name: "AppCatalog",
    components: {
      ImageHeader,
      CardsGrid,
      Pagination,
      BackToTop
    },
    data() {
      return {
        title: title,
        desc: desc,
        itemsDso: dsoList,
        nbItems: nbItems,
        currentPage: currentPage,
        totalPage: totalPage,
        listFilters: listFilters,
        listFacets: listFacets,
        showFacets: showFacets,
        pagination: pagination,
        urlDownloadData: urlDownloadData
      }
    },
    methods: {
      removePills: (id) => {
        this.pills.splice(id, 1);
      },
      // toggleFacets: function() {
      //   this.showFacets = !this.showFacets;
      // }
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

<!--https://codepen.io/ph1p/pen/LRjyPJ-->
