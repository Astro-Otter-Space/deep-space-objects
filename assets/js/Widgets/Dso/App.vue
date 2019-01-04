<template>
    <div id="main">
      <Imageheader/>
      <Slider/>
    </div>
</template>

<script>
  import Imageheader from './components/Imageheader'
  import Slider from './components/Slider'

  export default {
    name: "app",
    components: { Imageheader, Slider }
  }
</script>
