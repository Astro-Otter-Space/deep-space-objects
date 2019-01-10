import Vue from 'vue';
import App from './Widgets/Dso/App'
// Map
import Deepskymap from './deepskymap'

Vue.config.productionTip = false;
import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

new Vue({
  render: h => h(App)
}).$mount(`#app`);

// Map
let jsonDso = document.querySelector('div[data-dso-widget]').dataset.geojsonData;
Deepskymap({}, jsonDso);
