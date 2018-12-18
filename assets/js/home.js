import Vue from 'vue'
import App from './Widgets/Homepage/App'

Vue.config.productionTip = false;

new Vue({
  el: '#app',
  template: '<App/>',
  components: { App },
});
