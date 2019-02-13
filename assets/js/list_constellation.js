import Vue from 'vue';
import AppList from './Widgets/Constellation/ListApp';

Vue.config.productionTip = false;

import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

new Vue({
  render: h => h(AppList)
}).$mount(`#appListConst`);