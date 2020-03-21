/**
 * Only use in add form twig (add event, add dso planner)
 */
import Vue from 'vue';

import { datePicker } from './flatpicker';
import { latLng } from "leaflet";
import { LMap, LTileLayer, LMarker, LGeoJson } from 'vue2-leaflet';
import { Icon } from 'leaflet'

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
      url:'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
      attribution:'&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
      center: latLng(0, 0),
      markers: [],
      form: document.querySelector('form').name
    }
  },
  methods: {
    addMarker(e) {
      this.removeMarker(0);
      let coordinates = e.latlng;
      this.markers.push(coordinates);

      let valueInput = L.GeoJSON.latLngToCoords(coordinates);

      let formSelector = "[name='"+ this.form + "[location]'";
      let elInput = document.querySelector(formSelector);
      elInput.value = JSON.stringify(valueInput);
    },
    removeMarker(index) {
      this.markers.splice(index, 1);
    }
  },
  mounted: function() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        this.center = latLng(position.coords.latitude, position.coords.longitude);
      },
        (err) => {
          this.center = latLng(48.5734053, 7.7521113);
      });
    } else {
      this.center = latLng(48.5734053, 7.7521113);
    }
  }
});
