<template>
  <div>
    <div class="Dso_header">
    </div>
    <section class="Dso__main">
      <div class="Dso__container">

        <h2 class="Dso__title">
          {{ titleDso }}
        </h2>

        <div class="Dso__list" v-if="0 < itemsDso.length">
          <cards-grid
            :show-controls="false"
            :items="itemsDso"
          >
          </cards-grid>
        </div>
      </div>

    </section>
  </div>
</template>

<script>

  import ImageHeader from './components/Imageheader'
  import CardsGrid from './components/CardsGrid'
  import vPagination from 'vue-plain-pagination'

  let title = document.querySelector('div[data-list-dso]').dataset.title;
  let dsoList = JSON.parse(document.querySelector('div[data-list-dso]').dataset.listDso);

  export default {
    name: "AppCatalog",
    components: {
      ImageHeader,
      CardsGrid,
      vPagination
    },
    data() {
      return {
        title: title,
        itemsDso: dsoList
      }
    }
  }
</script>
