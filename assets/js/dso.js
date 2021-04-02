import Vue from 'vue';
import AppDso from './Widgets/Dso/App'

Vue.config.productionTip = false;
// Import libraries
import VueSocialSharing from "vue-social-sharing";
import VueLazyImageLoading from "vue-lazy-image-loading";
import Lightbox from 'vue-pure-lightbox'
import SvgIcon from 'vue-svgicon'

Vue.use(VueLazyImageLoading);
Vue.use(VueSocialSharing);
Vue.use(Lightbox);
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  el: '#appDso',
  render: h => h(AppDso),
}); //.$mount(`#appDso`);
