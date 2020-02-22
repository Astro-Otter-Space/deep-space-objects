import Vue from 'vue';

// Leaflet
import { Icon }  from 'leaflet'
delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

import SocialSharing from "vue-social-sharing";
Vue.use(SocialSharing);

import AppEvent from './Widgets/Observation/EventApp'
new Vue({
  el: '#app',
  render: h => h(AppEvent),
});
