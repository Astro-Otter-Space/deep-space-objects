import Vue from 'vue';
import L from 'leaflet';
import AppList from './Widgets/Observation/List'

Vue.config.productionTip = false;
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

new Vue({
  el: '#app',
  render: h => h(AppList),
});

