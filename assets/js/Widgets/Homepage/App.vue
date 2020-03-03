<template>
  <div id="app" itemscope>
    <div class="AppSlider">
      <div class="AppSlider__slides">
        <div
          v-for="(image, index) in this.images"
          v-bind:key="image"
          :style="{ backgroundImage: `url(${image})` }"
          class="AppSlider__dImage"
        ></div>
      </div>

      <div class="AppSlider__Research">
        <h1 class="AppSlider__subTitle" itemprop="title">
          <label for="homesearch">{{ subTitle }}</label>
        </h1>
        <searchautocomplete
          ref="homesearch"
          :searchPlaceholder="searchPlaceholder"
          :customClasses="classesSearchAutocomplete"
          :url="urlSearchHome"
          id="homesearch"
        />
      </div>

      <div class="AppSlider__Vignettes" id="appVignette">
        <vignette
          :vignettes="listVignettes"
        ></vignette>
      </div>
    </div>
  </div>
</template>

<script>
  import Searchautocomplete from "./components/Searchautocomplete"
  import Vignette from "./components/Vignette";

  let homeTitle = document.getElementById('appHome').dataset.homeTitle;
  let searchPlaceholder = document.getElementById('appHome').dataset.searchPlaceholder;
  let urlSearchHome = document.getElementById('appHome').dataset.searchRoute;
  let listVignettes = JSON.parse(document.querySelector('div#appVignette').dataset.vignettes);

  export default {
    name: "App",
    components: {
      Searchautocomplete,
      Vignette
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
        urlSearchHome: urlSearchHome,
        images: [
          '/build/images/background/bg-1.webp',
          '/build/images/background/bg-6.webp',
          '/build/images/background/bg-3.webp',
          '/build/images/background/bg-4.webp'
        ],
        listVignettes: listVignettes
      }
    }
  }
</script>
