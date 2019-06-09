import Vue from 'vue';
import axios from 'axios';
import { datePicker } from './flatpicker';
import { latLng } from "leaflet";
import { LMap, LTileLayer, LMarker, LGeoJson } from 'vue2-leaflet';
import { Icon } from 'leaflet'
import VoerroTagsInput from '@voerro/vue-tagsinput';
Vue.component('tags-input', VoerroTagsInput);

datePicker();

delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

/**
 * LEAFLET
 */
new Vue({
  el: '#map',
  components: { LMap, LTileLayer, LMarker, LGeoJson },
  data() {
    return {
      zoom: 5,
      url:'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
      attribution:'&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      center: latLng(48.5734053, 7.7521113),
      markers:[]
    }
  },
  methods: {
    addMarker(e) {
      this.removeMarker(0);
      var coordinates = e.latlng;
      this.markers.push(coordinates);

      var valueInput = L.GeoJSON.latLngToCoords(coordinates);

      var elInput = document.querySelector("[name='add_observation[location]']");
      elInput.value = JSON.stringify(valueInput);
    },
    removeMarker(index) {
      this.markers.splice(index, 1);
    }
  }
});

/**
 * TAGS
 * Waiting for https://github.com/voerro/vue-tagsinput/pull/46
 */
// Todo : get symfony route automatically
new Vue({
  el: '#elTags',
  components: {VoerroTagsInput},
  data() {
    return {
      listDso: []
    }
  },
  methods: {
    getListDso() {
      axios.get('/_search_dso_observation', {params: {'q': this.listDso}})
        .then((data) => {
          this.listDso = data.dso;
        })
    }
  },
  created() {
    this.getListDso()
  }
});