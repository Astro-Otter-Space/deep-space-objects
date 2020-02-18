<template>
  <div>
    <cards-grid
      :items="imagesDso"
    >
    </cards-grid>
  </div>
</template>
<script>
  import CardsGrid from './../Dso/components/CardsGrid';

  let images = JSON.parse(document.getElementById('appDso').dataset.dso);

  export default {
    name: "appDso",
    components: {
      CardsGrid
    },
    data () {
      return {
        imagesDso: images,
      }
    }
  }
</script>
