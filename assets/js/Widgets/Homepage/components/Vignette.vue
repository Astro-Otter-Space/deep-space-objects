<template>
  <section class="Vignettes__container">
    <a v-for="dso in vignettes" :href="dso.url" class="Vignettes__vignette"
       v-bind:style="imgStyle(dso)"
    >
    </a>
  </section>
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
        var bgImage = 'https://www.astrobin.com/368734/0/rawthumb/regular/'; //dso.image.url_regular;
        return {
          "background": `url(${bgImage}) no-repeat center center`,
          "background-size": `cover`
        };
      }
    }
  }
</script>

<style scoped>

</style>
