<template>
  <div>
    <p>
      <svgicon name="dsoplanner" width="15" height="15"></svgicon>
      <b><a v-bind:href="url">{{ name }}</a></b>
    </p>
    <p>
      {{username}} - {{ date_observation }}
    </p>
  </div>
</template>

<script>
  import './../../Icons/dsoplanner'

  export default {
    name: "ObservationPopup",
    props: {
      name: {
        type: String,
        default: ""
      },
      url: {
        type: String,
        default: ""
      },
      username: {
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
