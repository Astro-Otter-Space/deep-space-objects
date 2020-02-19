import Vue from 'vue';
import AppAstrobin from './Widgets/Astrobin/App';
new Vue({
  el: '#appDso',
  render: h => h(AppAstrobin),
}); //.$mount(`#appDso`);
