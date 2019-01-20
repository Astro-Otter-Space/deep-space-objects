import Vue from 'vue';
import AppDso from './Widgets/Dso/App'
import Deepskymap from './deepskymap'

Vue.config.productionTip = false;
// Import libraries
import SocialSharing from 'vue-social-sharing';
import VueLazyImageLoading from "vue-lazy-image-loading";

Vue.use(VueLazyImageLoading);
Vue.use(SocialSharing);

new Vue({
  render: h => h(AppDso)
}).$mount(`#appDso`);

// Map
let jsonDso = document.querySelector('div[data-dso-map]').dataset.geojsonDso;
Deepskymap({}, jsonDso);
