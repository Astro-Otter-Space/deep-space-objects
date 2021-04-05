import Vue from 'vue';
import AppConst from './Widgets/Constellation/App';

Vue.config.productionTip = false;

import VueLazyImageLoading from "vue-lazy-image-loading";
import SvgIcon from 'vue-svgicon'
import VueSocialSharing from "vue-social-sharing";

Vue.use(VueLazyImageLoading);
Vue.use(VueSocialSharing);
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(AppConst)
}).$mount(`#appConst`);
