import Vue from 'vue';
import AppCatalog from './Widgets/Dso/AppCatalog';
import VueLazyImageLoading from "vue-lazy-image-loading";

Vue.use(VueLazyImageLoading);

new Vue({
  render: h => h(AppCatalog)
}).$mount(`#appCatalog`);