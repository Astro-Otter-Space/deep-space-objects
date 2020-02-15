<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h2 class="Dso__title">
          <label for="observationsearch">{{ pageTitle }}</label>
        </h2>

        <searchautocomplete
          ref="observationsearch"
          :searchPlaceholder="searchPlaceholder"
          :customClasses="autoCompleteClasse"
          :url="urlSearch"
          id="observationsearch"
        />

        <h3 class="Dso__title">{{ mapTitle }}</h3>
        <div class="appGridFilter">
          <div class="appGridFilter__filter">
            <label v-for="control in listFilters" class="appGridFilter__btn-radio">
              <input type="radio"
                     name="appGridFilter__radio-grp"
                     v-model="itemselect"
                     :id="control.value"
                     :value="control.value"
              />
              <svg width="20px" height="20px" viewBox="0 0 20 20">
                <circle cx="10" cy="10" r="9"></circle>
                <path d="M10,7 C8.34314575,7 7,8.34314575 7,10 C7,11.6568542 8.34314575,13 10,13 C11.6568542,13 13,11.6568542 13,10 C13,8.34314575 11.6568542,7 10,7 Z" class="inner"></path>
                <path d="M10,1 L10,1 L10,1 C14.9705627,1 19,5.02943725 19,10 L19,10 L19,10 C19,14.9705627 14.9705627,19 10,19 L10,19 L10,19 C5.02943725,19 1,14.9705627 1,10 L1,10 L1,10 C1,5.02943725 5.02943725,1 10,1 L10,1 Z" class="outer"></path>
              </svg>
              <span>{{ control.label }}</span>
            </label>
          </div>
        </div>
        <div class="Dso__leaflet">
<!--  TODO  https://travishorn.com/interactive-maps-with-vue-leaflet-5430527353c8-->
          <l-map
            :zoom="zoom"
            :center="center"
          >
            <l-geo-json v-if="(itemselect === 'obs') || (itemselect === 'all')"
              :geojson="geojsonObs"
              :options="options"
              :options-style="styleObservation"
            ></l-geo-json>

            <l-geo-json v-if="(itemselect === 'event') || (itemselect === 'all')"
              :geojson="geojsonEvents"
              :options="options"
              :options-style="styleEvents"
            ></l-geo-json>

            <l-tile-layer
              :url="url"
              :attribution="attribution"
            />
          </l-map>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import Vue from "vue";
  import Searchautocomplete from "./../Homepage/components/Searchautocomplete"
  import { LMap, LTileLayer, LMarker, LGeoJson } from 'vue2-leaflet';
  import axios from 'axios';
  import obsPopupContent from './ObservationPopup';

  let pageTitle = document.querySelector('div[data-observations-list]').dataset.title;
  let mapTitle = document.querySelector('div[data-observations-list]').dataset.mapTitle;
  let urlSearchObs = document.querySelector('div[data-observations-list]').dataset.searchRoute;
  let urlAjaxObservations = document.querySelector('div[data-observations-list]').dataset.ajaxObservations;
  let urlAjaxEvents = document.querySelector('div[data-observations-list]').dataset.ajaxEvents;
  let searchPlaceholder = document.querySelector('div[data-observations-list]').dataset.observationAutocomplete;

  export default {
    name: "App",
    components: {
      Searchautocomplete,
      LMap,
      LTileLayer,
      LMarker,
      LGeoJson,
    },
    data () {
      return {
        pageTitle: pageTitle,
        mapTitle: mapTitle,
        urlSearch: urlSearchObs,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText AppSearch__inputTextObservation',
          list: 'AppHeader__list'
        },
        searchPlaceholder: searchPlaceholder,
        enableTooltip: true,
        showObs: true,
        showEvent: true,
        zoom: 5,
        url: 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
        center: L.latLng(48.856614, 2.3522219),
        geojsonObs: null,
        geojsonEvents: null,
        urlAjaxObservations: urlAjaxObservations,
        urlAjaxEvents: urlAjaxEvents,
        listFilters: [
          {
            value: 'all',
            label: 'All'
          },
          {
            value: 'event',
            label: "Events"
          },
          {
            value: 'obs',
            label: 'Observations',
          }
        ],
        itemselect: 'all',
      }
    },
    created() {
      axios.get(this.urlAjaxObservations).then((response) => {
          this.geojsonObs =  response.data;
        }
      );

      axios.get(this.urlAjaxEvents).then((response) => {
          this.geojsonEvents =  response.data;
        }
      );
    },
    computed: {
      options() {
        return {
          onEachFeature: this.onEachFeatureFunction
        }
      },
      onEachFeatureFunction() {
        if (!this.enableTooltip) {
          return () => {};
        }
        return (feature, layer) => {
          /*let ObsPopupContent = Vue.extend(obsPopupContent);
          let popup = new ObsPopupContent({
            propsData: {
              name: feature.properties.name,
              url: feature.properties.full_url,
              username: feature.properties.username,
              date_observation: feature.properties.date
            }
          });
          layer.bindPopup(popup.$mount().$el);*/
        }
      },
      styleObservation() {
        /*return {
          pointToLayer(feature, latlng) {
            return L.Marker(latlng, {
              icon: new L.icon({
                iconUrl: 'assets/images/markers/telescope.png',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
              })
            })
          }
        }*/
        return () => {
          return {
            color: '#ff0000',
            weight: 5,
            opacity: 0.5
          }
        }
      },
      styleEvents() {
        return () => {
          return {
            color: '#265575',
            weight: 5,
            opacity: 0.5
          }
        }
      },
    }
  }
</script>
