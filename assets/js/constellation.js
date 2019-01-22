import Vue from 'vue';
import AppConst from './Widgets/Constellation/App';

Vue.config.productionTip = false;

import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

new Vue({
  render: h => h(AppConst)
}).$mount(`#appConst`);
