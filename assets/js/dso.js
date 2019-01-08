import Vue from 'vue';
import App from './Widgets/Dso/App'

Vue.config.productionTip = false;
import VueLazyImageLoading from "vue-lazy-image-loading";
Vue.use(VueLazyImageLoading);

new Vue({
  render: h => h(App)
}).$mount(`#app`);
