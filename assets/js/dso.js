import Vue from 'vue';
import AppDso from './Widgets/Dso/App'
// import Deepskymap from './deepskymap'

Vue.config.productionTip = false;
// Import libraries
import SocialSharing from "vue-social-sharing";
import VueLazyImageLoading from "vue-lazy-image-loading";
import Lightbox from 'vue-pure-lightbox'
import SvgIcon from 'vue-svgicon'

Vue.use(VueLazyImageLoading);
Vue.use(SocialSharing);
Vue.use(Lightbox);
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(AppDso)
}).$mount(`#appDso`);

// Map
// let jsonDso = document.querySelector('div[data-dso-map]').dataset.geojsonDso;
// Deepskymap.map({}, jsonDso);
