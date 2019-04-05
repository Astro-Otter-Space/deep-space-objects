import Vue from 'vue';
import AppConst from './Widgets/Constellation/App';

Vue.config.productionTip = false;

import VueLazyImageLoading from "vue-lazy-image-loading";
import SvgIcon from 'vue-svgicon'

Vue.use(VueLazyImageLoading);
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(AppConst)
}).$mount(`#appConst`);
