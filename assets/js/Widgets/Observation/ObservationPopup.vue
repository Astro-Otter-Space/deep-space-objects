<template>
  <div>
    <p>
      <a v-bind:href="url">{{ name }}</a>
    </p>
    <p>{{ username }} - {{ date_observation }}</p>
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
      date_observation: {
        type: String,
        default: ""
      }
    }
  }
</script>
