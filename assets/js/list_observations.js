import Vue from 'vue';
import AppList from './Widgets/Observation/List'

Vue.config.productionTip = false;

new Vue({
  el: '#app',
  render: h => h(AppList),
});

