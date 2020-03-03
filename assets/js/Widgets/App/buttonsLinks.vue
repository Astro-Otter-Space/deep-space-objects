<template>
  <ul>
    <li v-for="link in links">
      <a v-bind:href="link.path" v-bind:title="link.label" target="_blank" rel="noopener">
        <svgicon
          v-bind:name="link.icon_class"
          v-bind:width="widthButton"
          v-bind:height="heightButton"
          :color="color"
        >
        </svgicon>
      </a>
    </li>
  </ul>
</template>

<script>
  import './../Icons/index';

  export default {
    name: "buttonsLinks",
    props: {
      links: {
        type: Object,
        default: () => []
      },
      widthButton: {
        type: String,
        default: 30
      },
      heightButton: {
        type: String,
        default: 30
      },
      color: {
        type: String,
        default: ""
      }
    }
  }
</script>

<style scoped>

</style>
