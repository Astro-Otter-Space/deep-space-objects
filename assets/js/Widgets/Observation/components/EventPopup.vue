<template>
  <div>
    <p>
      <b><a v-bind:href="url">{{ name }} - {{ organiser }}</a></b>
    </p>
    <p>
      {{location}} - {{date_observation}}
    </p>
  </div>
</template>

<script>
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
