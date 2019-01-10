import Vue from 'vue';
import App from './Widgets/Dso/App'

Vue.config.productionTip = false;
import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

let jsonDso = document.querySelector('div[data-dso-widget]').dataset.geojsonData;

// Map
import Map from 'celestial'
/** Map */
let mapDso = new Map({}, jsonDso);
mapDso.buildConfig();


new Vue({
  render: h => h(App)
}).$mount(`#app`);
