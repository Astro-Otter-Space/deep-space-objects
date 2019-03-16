<template>
  <div id="app">
    <div class="AppSlider">
      <slider
        :images="[
          '/build/images/interstellar-black-hole.jpg'
        ]"
      />
      <div class="AppSlider__Research">
        <h1 class="AppSlider__subTitle">{{ title }}</h1>
        <!--<searchautocomplete-->
                <!--ref="homesearch"-->
                <!--:searchPlaceholder="searchPlaceholder"-->
                <!--:customClasses="classesSearchAutocomplete"-->
                <!--:url="urlSearchHome"-->
        <!--/>-->
      </div>
    </div>
  </div>
</template>

<script>
  import Slider from '../Homepage/components/Slider'
  // import Searchautocomplete from "./components/Searchautocomplete"

  let homeTitle = document.getElementById('app').dataset.title;
  // let searchPlaceholder = document.getElementById('appHome').dataset.searchPlaceholder;
  // let urlSearchHome = document.getElementById('appHome').dataset.searchRoute;

  export default {
    name: "App",
    components: {
      Slider,
      // Searchautocomplete
    },
    data() {
      return {
        title: homeTitle,
        // searchPlaceholder: searchPlaceholder,
        classesSearchAutocomplete: {
          wrapper: 'AppSearch__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppSearch__list'
        },
        // urlSearchHome: urlSearchHome
      }
    }
  }
</script>
