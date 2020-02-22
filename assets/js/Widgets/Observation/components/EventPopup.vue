<template>
  <div>
    <p>
      <svgicon name="telescop" width="15" height="15"></svgicon>
      <b><a v-bind:href="url">{{ name }} - {{ organiser }}</a></b>
    </p>
    <p>
      {{location}} - {{date_observation}}
    </p>
  </div>
</template>

<script>
   import './../../Icons/telescop'

  export default {
    name: "EventPopup",
    props: {
      name: {
        type: String,
        default: ""
      },
      url: {
        type: String,
        default: ""
      },
      organiser: {
        type: String,
        default: ""
      },
      location: {
        type: String,
        default: ""
      },
      date_observation: {
        type: String,
        default: ""
      }
    }
  }
</script>
