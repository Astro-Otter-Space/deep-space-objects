<template>
  <div id="app">
    <h1 class="AppSlider__title">{{title}}</h1>
    <slider
      :images="[
        '/assets/images/build/images/background/bg-1.jpg',
        '/assets/images/build/images/background/bg-2.jpg',
        '/assets/images/build/images/background/bg-3.jpg',
        '/assets/images/build/images/background/bg-4.jpg'
      ]"
      :sub-title="${subTitle}"
    />
    <SearchAutocomplete/>
    <h2 class="AppSlider__subTitle">{{subTitle}}</h2>
  </div>
</template>

<script>
  import Slider from './components/Slider'
  import SearchAutocomplete from "./components/SearchAutocomplete";

  let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;

  export default {
    name: "App",
    components: {
      SearchAutocomplete,
      Slider
    },
    data() {
      return {
        subTitle: homeTitle
      }
    }
  }
</script>
