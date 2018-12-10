<script>
  export default {
    name: "locale-lang",
    template: '<li><a v-bind:href="item.path">{{item.value}}</a></li>',
    props: {
      dataLangs: Object
    }
  }
</script>
