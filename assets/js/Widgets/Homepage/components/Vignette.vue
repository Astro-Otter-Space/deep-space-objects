<template>
  <div id="appVignette" itemscope>
    <section class="Vignettes__container">
      <a v-for="dso in vignettes" :href="dso.url" class="Vignettes__vignette"
         v-bind:style="imgStyle(dso)"
      >
      </a>
    </section>
  </div>
</template>

<script>
  export default {
    name: "Vignette",
    props: {
      vignettes: {
        default: () => [],
        type: Array
      }
    },

    methods: {
      imgStyle: (dso) => {
        var bgImage = dso.image.url_regular;
        return {
          "background-size": "cover",
          "background-position" : "50% 50%",
          "background": 'url('+bgImage+')'
        };
      }
    }
  }
</script>

<style scoped>

</style>
