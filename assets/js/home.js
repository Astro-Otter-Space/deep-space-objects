import Vue from 'vue'
import App from './Widgets/Homepage/App'
import VueRouter from 'vue-router';

Vue.config.productionTip = false;

Vue.use(VueRouter);

new Vue({
  el: '#app',
  template: '<App/>',
  components: { App },
});

// new Vue({
//   render: h => h(App)
// }).$mount(`#app`);
