<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h1 class="Dso__title">{{ title }}</h1>
        <div>{{ desc }}</div>

        <!--List-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3>
            <a v-bind:href="urlDownloadData" title="Download data">
              <svgicon name="file-download" width="30" height="30" color="#2B2A34"></svgicon>
            </a>
            &nbsp;|&nbsp;
            {{ nbItems }}
            &nbsp;|&nbsp;
            <span class="checkbox-select__title">Filters</span>
            <svgicon name="down" width="20" height="20" v-if="!showFacets" v-on:click="toggleFacets()"></svgicon>
            <svgicon name="up" width="20" height="20" v-if="showFacets" v-on:click="toggleFacets()"></svgicon>

            <!-- div class="checkbox-select__trigger" :class="{ isActive: activeTrigger }" v-on:click="showDropdown()">
              <span class="checkbox-select__title">Filters</span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 129 129"><path d="M121.3 34.6c-1.6-1.6-4.2-1.6-5.8 0l-51 51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8 0-1.6 1.6-1.6 4.2 0 5.8l53.9 53.9c.8.8 1.8 1.2 2.9 1.2 1 0 2.1-.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2.1-5.8z"/></svg>
            </div -->
          </h3>

          <!-- ul v-if="listFilters.length" class="Filters__list">
            <li v-for="filter in listFilters" class="Filters__badge">
              {{filter.label}}
              <a v-bind:href="filter.delete_url"><svgicon name="cross" width="15" height="15" color="#1ed760"></svgicon></a>
            </li>
          </ul -->
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
  let showFacets = false;
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
        dropdown: false,
        activeTrigger: false,
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
      },
      showDropdown: () => {
        if (this.dropdown === false) {
          this.dropdown = true;
          this.activeTrigger = true;
          TweenMax.fromTo("#dropdown", 0.55, { autoAlpha: 0, y: -10 },  { autoAlpha: 1, y: 0, ease: Power2.easeOut });
        } else {
          this.dropdown = false;
          this.activeTrigger = false;
          TweenMax.to("#dropdown", 0.2, {autoAlpha: 0, y: -10,ease: Power2.easeOut });
        }
      }
    }
  }
</script>

<!--https://codepen.io/ph1p/pen/LRjyPJ-->
