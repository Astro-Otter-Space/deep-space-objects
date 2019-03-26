import Vue from 'vue';
import AppCatalog from './Widgets/Dso/AppCatalog';

import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

// Import custom icons
import SvgIcon from 'vue-svgicon'
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(AppCatalog)
}).$mount(`#appCatalog`);