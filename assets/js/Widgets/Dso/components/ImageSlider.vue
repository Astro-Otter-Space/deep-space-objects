<template>
  <vue-flux
    :options="fluxOptions"
    :images="fluxImages"
    :transition="fluxTransitions"
    ref="slider"
  >
  </vue-flux>
</template>

<script>

  import { VueFlux, FluxPagination, Transitions } from 'vue-flux';
  //import 'vue-flux/dist/vue-flux.css';

  export default {
    name: 'ImageSlider',
    components: {
      'vue-flux': VueFlux
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
