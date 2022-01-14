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
          :searchPlaceholder="placeholder"
          :customClasses="classesSearchAutocomplete"
          :url="urlSearchHome"
          id="homesearch"
        />
<!--        <h1 class="AppSlider__subTitle" itemprop="title" v-show="isMobile">-->
<!--          <label for="homesearch">{{ subTitle }}</label>-->
<!--        </h1>-->
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
  import deviceDetect from 'mobile-device-detect';
  import nightMode from './../../components/night_mode'

  let homeTitle = document.getElementById('appHome').dataset.homeTitle;
  let searchPlaceholder = document.getElementById('appHome').dataset.searchPlaceholder;
  let searchPlaceholderMobile = document.getElementById('appHome').dataset.searchPlaceholderMobile;
  let urlSearchHome = document.getElementById('appHome').dataset.searchRoute;
  let listVignettes = JSON.parse(document.querySelector('div#appVignette').dataset.vignettes);

  export default {
    name: "App",
    components: {
      Searchautocomplete,
      Vignette,
      nightMode
    },
    data() {
      return {
        subTitle: homeTitle,
        placeholder: (deviceDetect.isMobileOnly) ? searchPlaceholderMobile : searchPlaceholder,
        classesSearchAutocomplete: {
          wrapper: 'AppSearch__wrapper',
          input: 'AppSearch__inputText AppSearch__inputTextHome', //(deviceDetect.isMobileOnly) ? 'AppSearch__inputText AppSearch__inputTextHome' : 'AppSearch__inputText',
          list: 'AppSearch__list'
        },
        urlSearchHome: urlSearchHome,
        images: [
          '/build/images/background/bg-1.webp',
          '/build/images/background/bg-1.webp',
          '/build/images/background/bg-1.webp',
        ],
        listVignettes: listVignettes,
        isMobile: deviceDetect.isMobileOnly
      }
    },
    methods: {
      test: function() {
        console.log('coucou coucou');
      }
    },
    mounted() {
      if (null !== nightMode.isNightMode()) {
        nightMode.setNightMode();
      }
    }
  }
</script>
