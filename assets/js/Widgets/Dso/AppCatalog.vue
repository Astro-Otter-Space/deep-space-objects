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

          <cards-grid
            :show-controls="false"
            :items="itemsDso"
            :list-facets="listFacets"
            :show-facets="showFacets"
          >
          </cards-grid>
          <!--<v-pagination v-model="currentPage" :page-count="475"></v-pagination>-->
        </div>
      </div>

    </section>
  </div>
</template>

<script>

  import ImageHeader from './components/Imageheader'
  import CardsGrid from './components/CardsGrid'
  import './../Icons/cross';
  import './../Icons/up';
  import './../Icons/down';

  let title = document.querySelector('div[data-catalog-widget]').dataset.title;
  let desc = document.querySelector('div[data-catalog-widget]').dataset.desc;
  let dsoList = JSON.parse(document.querySelector('div[data-catalog-widget]').dataset.listDso);
  let listFacets = JSON.parse(document.querySelector('div[data-catalog-widget]').dataset.listFacets);
  let currentPage = document.querySelector('div[data-catalog-widget]').dataset.page;
  let nbItems = document.querySelector('div[data-catalog-widget]').dataset.totalDso;
  let showFacets = false;

  export default {
    name: "AppCatalog",
    components: {
      ImageHeader,
      CardsGrid,
    },
    data() {
      return {
        title: title,
        desc: desc,
        itemsDso: dsoList,
        nbItems: nbItems,
        currentPage: currentPage,
        listFacets: listFacets,
        showFacets: showFacets
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