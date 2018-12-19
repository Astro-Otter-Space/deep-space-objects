import Vue from 'vue';
import App from 'Widgets/Dso/App'
import Agile from "vue-agile";

Vue.config.productionTip = false;

Vue.use(Agile);

new Vue({
  el: "#app",
  template: "<App/>",
  components: { App }
});
