import Vue from 'vue';
import L from 'leaflet';
import AppObs from './Widgets/Observation/App';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

import SocialSharing from "vue-social-sharing";
Vue.use(SocialSharing);

new Vue({
  el: '#app',
  render: h => h(AppObs),
});