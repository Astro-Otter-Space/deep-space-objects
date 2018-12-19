<template>
  <div id="app">
    <slider
      :images="[
        'https://raw.githubusercontent.com/HamHamFonFon/deep-space-objects/master/assets/images/background/bg-1.jpg',
        'https://raw.githubusercontent.com/HamHamFonFon/deep-space-objects/master/assets/images/background/bg-2.jpg',
        'https://raw.githubusercontent.com/HamHamFonFon/deep-space-objects/master/assets/images/background/bg-3.jpg',
        'https://raw.githubusercontent.com/HamHamFonFon/deep-space-objects/master/assets/images/background/bg-4.jpg'
      ]"
    />
    <h2>{{subTitle}}</h2>
  </div>
</template>

<script>
  import Slider from './components/Slider'
  // import SearchAutocomplete from '../Homepage/components/SearchAutocomplete'

  let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;

  export default {
    name: "App",
    components: {Slider},
    props: {
      subTitle: homeTitle
    }
  }
</script>
