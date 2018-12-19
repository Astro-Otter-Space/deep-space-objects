<template>
  <div id="app">
    <agile :options="options">
      <div class="slide slide--1"></div>
      <div class="slide slide--2"></div>
      <div class="slide slide--3"></div>
      <div class="slide slide--4"></div>
    </agile>
  </div>
</template>

<script>
  export default {
    name: 'Slider',
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
