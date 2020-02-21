<template>
  <div>
    <p>
      <b><a v-bind:href="url">{{ name }} - {{ date_observation }}</a></b>
    </p>
    <p>
      {{place}}
    </p>
  </div>
</template>

<script>
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
      place: {
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
