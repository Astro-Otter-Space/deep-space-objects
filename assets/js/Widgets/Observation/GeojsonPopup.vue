<template>
  <div>
    <p>
      <a v-bind:href="url">{{ name }}</a>
    </p>
  </div>
</template>

<script>
  export default {
    name: "GeojsonPopup",
    props: {
      name: {
        type: String,
        default: ""
      },
      url: {
        type: String,
        default: ""
      }
    }
  }
</script>
