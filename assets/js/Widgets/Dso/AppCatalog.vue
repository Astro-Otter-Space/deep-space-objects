<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h2 class="Dso__title">{{ title }}</h2>
        <div>{{ desc }}</div>

        <!--List-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3>{{ nbItems }}</h3>

          <cards-grid
            :show-controls="false"
            :items="itemsDso"
            :list-facets="listFacets"
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
 // import vPagination from 'vue-plain-pagination'
  import badge from './../App/Badge'

  let title = document.querySelector('div[data-catalog-widget]').dataset.title;
  let desc = document.querySelector('div[data-catalog-widget]').dataset.desc;
  let dsoList = JSON.parse(document.querySelector('div[data-catalog-widget]').dataset.listDso);
  let listFacets = JSON.parse(document.querySelector('div[data-catalog-widget]').dataset.listFacets);
  let currentPage = document.querySelector('div[data-catalog-widget]').dataset.page;
  let nbItems = document.querySelector('div[data-catalog-widget]').dataset.totalDso;

  export default {
    name: "AppCatalog",
    components: {
      ImageHeader,
      CardsGrid,
      // vPagination,
      // badge
    },
    data() {
      return {
        title: title,
        desc: desc,
        itemsDso: dsoList,
        nbItems: nbItems,
        currentPage: currentPage,
        listFacets: listFacets
      }
    },
    methods: {
      removePills: function(id) {
        this.pills.splice(id, 1);
      }
    }
  }
</script>

<!--https://codepen.io/ph1p/pen/LRjyPJ-->