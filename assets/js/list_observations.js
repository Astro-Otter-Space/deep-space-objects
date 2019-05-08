import Vue from 'vue';

Vue.config.productionTip = false;

// Leaflet
import { Icon }  from 'leaflet'
delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

import AppList from './Widgets/Observation/List'
new Vue({
  el: '#app',
  render: h => h(AppList),
});

