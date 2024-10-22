import Vue from 'vue'
import Vue2TouchEvents from "vue2-touch-events";

import App from './Widgets/Homepage/App'

Vue.config.productionTip = false;
Vue.use(Vue2TouchEvents);

new Vue({
  render: h => h(App)
}).$mount(`#appHome`);
