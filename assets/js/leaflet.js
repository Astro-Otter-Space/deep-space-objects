import Vue from 'vue';
import { latLng } from "leaflet";
import { LMap, LTileLayer, LControl } from 'vue2-leaflet';
import { Icon } from 'leaflet'
import 'leaflet/dist/leaflet.css'

delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

new Vue({
  el: '#map',
  components: { LMap, LTileLayer,  LControl },
  data() {
    return {
      zoom: 5,
      url:'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
      attribution:'&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      center: latLng(48.5734053, 7.7521113)
    }
  },
  methods: {
    showCoordinates(e) {
      alert(e.latlng);
    }
  }
});