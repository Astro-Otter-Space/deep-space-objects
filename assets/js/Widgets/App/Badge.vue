<!--<script>-->
  <!--Vue.component("vue-pill", {-->
    <!--props: [-->
      <!--"text",-->
      <!--"pillStyle",-->
      <!--"pillable"-->
    <!--],-->
    <!--template: `<div class="badge align-middle align-text-top align-text-bottom" v-bind:class="[computedStyle,computedPills]">-->
      <!--{{text}} &nbsp;&nbsp;-->
      <!--<span class="clickable" v-on:click="emit">x</span>-->
    <!--</div>`,-->
    <!--computed:{-->
      <!--computedStyle: function() {-->
        <!--return 'badge-' + this.pillStyle;-->
      <!--},-->
      <!--computedPills: function() {-->
        <!--return (this.pillable === 'true') ? 'badge-pill' : '';-->
      <!--}-->
    <!--},-->
    <!--methods: {-->
      <!--emit: function() {-->
        <!--this.$emit("pill_clicked", this.$el);-->
      <!--}-->
    <!--}-->
  <!--});-->
<!--</script>-->