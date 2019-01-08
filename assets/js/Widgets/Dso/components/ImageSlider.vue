<template>
  <vue-flux
    :options="fluxOptions"
    :images="fluxImages"
    :transitions="fluxTransitions"
    ref="slider"
  >
    <flux-pagination slot="pagination"></flux-pagination>
  </vue-flux>
</template>

<script>
  // https://deulos.github.io/vue-flux/
  import { VueFlux, FluxPagination, Transitions } from 'vue-flux';

  export default {
    name: 'ImageSlider',
    components: {
      VueFlux,
      FluxPagination
    },
    data: () => ({
      fluxOptions: {
        autoplay: true,
      },
      fluxTransitions: {
        transitionKenburn: Transitions.transitionKenburn
      }
    }),
    props: {
      fluxImages: {
        default: () => [],
        type: Array
      }
    }
  }
</script>

<style lang="scss" scoped>

</style>
