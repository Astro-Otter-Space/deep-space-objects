import Vue from 'vue';
import AppDso from './Widgets/Dso/App'
// import Deepskymap from './deepskymap'

Vue.config.productionTip = false;
// Import libraries
import SocialSharing from "vue-social-sharing";
import VueLazyImageLoading from "vue-lazy-image-loading";
import Lightbox from 'vue-pure-lightbox'

import { library } from '@fortawesome/fontawesome-svg-core';
import { faTwitter, faFacebook } from "@fortawesome/free-brands-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faTwitter, faFacebook);
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.use(VueLazyImageLoading);
Vue.use(SocialSharing);
Vue.use(Lightbox);

new Vue({
  render: h => h(AppDso)
}).$mount(`#appDso`);

// Map
// let jsonDso = document.querySelector('div[data-dso-map]').dataset.geojsonDso;
// Deepskymap.map({}, jsonDso);
