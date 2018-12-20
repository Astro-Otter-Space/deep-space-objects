<template>
  <div id="app">
    <div class="AppSlider">
      <slider
        :images="[
          '/assets/images/build/images/background/bg-1.jpg',
          '/assets/images/build/images/background/bg-2.jpg',
          '/assets/images/build/images/background/bg-3.jpg',
          '/assets/images/build/images/background/bg-4.jpg'
        ]"
        :sub-title="${subTitle}"
      />
      <div class="AppSlider__Research">
        <h2 class="AppSlider__subTitle">{{subTitle}}</h2>
        <searchautocomplete
          :searchPlaceholder="searchPlaceholder"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import Slider from './components/Slider'
  import SearchAutocomplete from "./components/SearchAutocomplete";

  let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;
  let searchPlaceholder = "Reasearch in the catalogue...";

  export default {
    name: "App",
    components: {
      SearchAutocomplete,
      Slider
    },
    data() {
      return {
        subTitle: homeTitle,
        searchPlaceholder: searchPlaceholder
      }
    }
  }
</script>
