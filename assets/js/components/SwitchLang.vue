<template>
  <ul>
    <li v-for="item in dataLangs" >
      <a
        v-bind:href="item.path"
      >
        {{item.value}}
      </a>
    </li>
  </ul>
</template>

<script>
  export default {
    name: "listLocales",
    props: {
      dataLangs: Object
    }
  }
</script>
