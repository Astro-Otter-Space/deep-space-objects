import Vue from 'vue'
import Vue2TouchEvents from "vue2-touch-events";

import App from './Widgets/Homepage/App'
// import VueRouter from 'vue-router';

Vue.config.productionTip = false;
Vue.use(Vue2TouchEvents);

//Vue.use(VueRouter);

new Vue({
  render: h => h(App)
}).$mount(`#app`);
