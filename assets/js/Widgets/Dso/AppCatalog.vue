<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h2 class="Dso__title">{{ title }}</h2>
        <div>{{ desc }}</div>

        <!--List-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3>{{ nbItems }}
            <svgicon name="down" width="20" height="20" v-if="!showFacets" v-on:click="toggleFacets()"></svgicon>
            <svgicon name="up" width="20" height="20" v-if="showFacets" v-on:click="toggleFacets()"></svgicon>
          </h3>

          <ul v-if="listFilters.length" class="Filters__list">
            <li class="Filters__badge">
              <a v-bind:href="urlDownloadData" title="Download data"><svgicon name="file-download" width="20" height="20"></svgicon></a>
            </li>
            <li v-for="filter in listFilters" class="Filters__badge">
              {{filter.label}}
              <a v-bind:href="filter.delete_url"><svgicon name="cross" width="15" height="15" color="#1ed760"></svgicon></a>
            </li>
          </ul>

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
    </section>
  </div>
</template>

<script>

  const DATA_SELECTOR = 'div[data-catalog-widget]';

  import ImageHeader from './components/Imageheader'
  import CardsGrid from './components/CardsGrid'
  import Pagination from './../App/Pagination'
  import './../Icons/cross';
  import './../Icons/up';
  import './../Icons/down';

  let title = document.querySelector(DATA_SELECTOR).dataset.title;
  let desc = document.querySelector(DATA_SELECTOR).dataset.desc;
  let dsoList = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.listDso);
  let listFacets = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.listFacets);
  let listFilters = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.selectedFilters);
  let currentPage = parseInt(document.querySelector(DATA_SELECTOR).dataset.page);
  let totalPage = parseInt(document.querySelector(DATA_SELECTOR).dataset.totalPage);
  let nbItems = document.querySelector(DATA_SELECTOR).dataset.totalDso;
  let showFacets = false;
  let pagination = JSON.parse(document.querySelector(DATA_SELECTOR).dataset.pagination);
  let urlDownloadData = "/download-data?type=pn";

  export default {
    name: "AppCatalog",
    components: {
      ImageHeader,
      CardsGrid,
      Pagination
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
      removePills: function(id) {
        this.pills.splice(id, 1);
      },
      toggleFacets: function() {
        this.showFacets = !this.showFacets;
      }
    }
  }
</script>

<!--https://codepen.io/ph1p/pen/LRjyPJ-->