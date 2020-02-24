<template>
  <section class="Vignette_container">
    <a v-for="dso in vignettes" :href="dso.url" class="vignette"
     v-bind:title="dso.image.title"
      v-bind:style="imgStyle"
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
    computed: {
      imgStyle: () => {
        var bgImage = this.$refs.image.url_regular;
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
  .Vignette_container {
    display: flex;
    width: 100%;
    max-width: 70%;
    justify-content: space-between;

    #background-color: rgba(255,255,255,.25);
    box-shadow: 0 1em 2em -1em rgba(0,0,0,.5);
    padding: 1em 2em;
  }

  .vignette {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 5px solid rgba(255,255,255,0.3);
    width: 200px;
    height: 200px;
    border-radius: 50%;
  }
</style>
