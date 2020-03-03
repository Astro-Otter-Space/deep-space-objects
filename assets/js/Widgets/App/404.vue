<template>
  <div id="app">
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
        <h1 class="AppSlider__subTitle">
          <label>{{ title }}</label>
        </h1>
      </div>
    </div>
  </div>
</template>

<script>
  let homeTitle = document.getElementById('appNotFound').dataset.title;

  export default {
    name: "App",
    data() {
      return {
        title: homeTitle,
        images: [
          '/build/images/notfound/interstellar-black-hole.jpg',
          '/build/images/notfound/interstellar-black-hole2.jpg'
        ]
      }
    }
  }
</script>
