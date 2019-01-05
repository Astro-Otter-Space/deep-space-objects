<template>
  <lazy-background
    :src="backgroundImage"
    :blur="30"
    position="center"
    size="cover"
  >
    <div slot="content" slot-scope="{ visible }">
      <img :src="coverImage" :style="imgCover">
      <!-- <lazy-img src="coverImage" /> -->
    </div>
  </lazy-background>
</template>

<script>
  import VueLazyImageLoading from 'vue-lazy-image-loading'

  export default {
    name: "Imageheader",
    components: {VueLazyImageLoading},
    data() {
      return {
        visible: true,
        backgroundImage: "https://res.weblium.site/res/5c2df42d715e560022a8ae05/5c2df5722141000022dde670_optimized",
        coverImage: "https://www.wanimo.com/veterinaire/images/articles/chat/fibrosarcome-chat.jpg"
      };
    },
    computed: {
      imgCover() {
        return {
          'position': 'absolute',
          'height': 'auto'
        }
      }
    }
  }
</script>

<style>
  .dso_header {
    height: 300px;
  }

  .lazy-background-image {
    opacity: 0.5;
  }
</style>