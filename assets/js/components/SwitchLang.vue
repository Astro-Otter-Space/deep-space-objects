<template>
  <select>
    <option v-for="option in dataLangs" ></option>
  </select>
</template>

<script>
  export default {
    name: "select-lang",
    props: ["dataLangs"],
    data: function () {
      return {}
    }
  }
</script>
