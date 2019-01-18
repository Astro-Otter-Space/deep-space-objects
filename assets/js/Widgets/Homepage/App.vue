<template>
  <div id="app">
    <div class="AppSlider">
      <slider
        :images="[
          '/build/images/background/bg-1.jpg',
          '/build/images/background/bg-2.jpg',
          '/build/images/background/bg-3.jpg',
          '/build/images/background/bg-4.jpg'
        ]"
      />
      <div class="AppSlider__Research">
        <h2 class="AppSlider__subTitle">{{ subTitle }}</h2>
        <searchautocomplete
          :searchPlaceholder="searchPlaceholder"
          :customClasses="classesSearchAutocomplete"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import Slider from './components/Slider'
  import Searchautocomplete from "./components/Searchautocomplete"

  let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;
  let searchPlaceholder = "Search in catalogs...";

  export default {
    name: "App",
    components: {
      Slider,
      Searchautocomplete
    },
    data() {
      return {
        subTitle: homeTitle,
        searchPlaceholder: searchPlaceholder,
        classesSearchAutocomplete: {
          wrapper: 'AppSearch__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppSearch__list'
        }
      }
    }
  }
</script>
