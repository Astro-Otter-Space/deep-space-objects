import Vue from 'vue';
import App from './Widgets/Dso/App'
//import Deepskymap from './deepskymap'

Vue.config.productionTip = false;
// Import libraries
import SocialSharing from 'vue-social-sharing';
import VueLazyImageLoading from "vue-lazy-image-loading";

Vue.use(VueLazyImageLoading);
Vue.use(SocialSharing);

new Vue({
  render: h => h(App)
}).$mount(`#app`);

// Map
let jsonDso = document.querySelector('div[data-dso-widget]').dataset.geojsonDso;
//Deepskymap({}, jsonDso);
