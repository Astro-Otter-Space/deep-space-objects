<template>
  <div id="app">
    <agile :options="options">
      <div class="slide slide--1"><h3 v-html="title"></h3></div>
      <div class="slide slide--2"><h3>{{title}}</h3></div>
      <div class="slide slide--3"><h3>{{title}}</h3></div>
      <div class="slide slide--4"><h3>{{title}}</h3></div>
    </agile>
  </div>
</template>

<script>
    import Agile from 'vue-agile'

    let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;

    export default {
      name: 'Slider',
      components: {Agile},
      props: {
        title: homeTitle
      },
      data() {
        return {
          options: {
            arrows: false,
            dots: false,
            infinite: true,
            speed: 200,
            fade: true,
            autoplay: true,
            responsive: []
          }
        }
      }
    }
</script>
