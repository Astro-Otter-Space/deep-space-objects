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
          :url="urlSearchHome"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import Slider from './components/Slider'
  import Searchautocomplete from "./components/Searchautocomplete"

  let homeTitle = document.getElementById('appHome').dataset.homeTitle;
  let searchPlaceholder = document.getElementById('appHome').dataset.searchPlaceholder;
  let urlSearchHome = document.getElementById('appHome').dataset.searchRoute;

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
        },
        urlSearchHome: urlSearchHome
      }
    }
  }
</script>
