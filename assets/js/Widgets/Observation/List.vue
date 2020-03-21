<template>
  <div>
    <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
        :alt-image="imageCoverAlt"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <h1 class="Dso__title">
          <label for="observationsearch">{{ pageTitle }}</label>
        </h1>

        <social-sharing
          :url="urlShare"
          :title="pageTitle"
          :description="description"
          hashtags=""
          twitter-user=""
          inline-template
        >
          <div>
            <network network="facebook">
              <svgicon name="facebook" width="15" height="15"></svgicon>
            </network>
            <network network="twitter">
              <svgicon name="twitter" width="15" height="15"></svgicon>
            </network>
          </div>
        </social-sharing>

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
          <l-map
            :zoom="zoom"
            :center="center"
          >
            <l-geo-json v-if="(itemselect === 'event') || (itemselect === 'all')"
              :geojson="geojsonEvents"
              :options="optionsEvents"
              name="l_events"
            >
            </l-geo-json>

            <l-geo-json v-if="(itemselect === 'obs') || (itemselect === 'all')"
              :geojson="geojsonObs"
              :options="optionsDsoPlanner"
              name="l_dsoplanner"
            >
            </l-geo-json>
            <l-tile-layer
              :url="url"
              :attribution="attribution"
            />
          </l-map>
        </div>
      </div>

      <back-to-top visibleoffset="10" bottom="25px" right="25px" text="">
        <svgicon name="up" width="40" height="40"></svgicon>
      </back-to-top>
    </section>
  </div>
</template>

<script>
  import Vue from "vue";
  import ImageHeader from './../Dso/components/Imageheader';
  import Searchautocomplete from "./../Homepage/components/Searchautocomplete"
  import { LMap, LTileLayer, LMarker, LIcon, LGeoJson } from 'vue2-leaflet';
  import axios from 'axios';
  import eventPopupContent from './components/EventPopup';
  import dsoPlannerPopupContent from './components/DsoPlannerPopup';
  import backToTop from 'vue-backtotop';
  import './../Icons/facebook';
  import './../Icons/twitter';
  import './../Icons/up'
  import {latLng} from "leaflet";

  let pageTitle = document.querySelector('div[data-observations-list]').dataset.title;
  let description = document.querySelector('div[data-observations-list]').dataset.description;
  let mapTitle = document.querySelector('div[data-observations-list]').dataset.mapTitle;
  let urlSearchObs = document.querySelector('div[data-observations-list]').dataset.searchRoute;
  let urlAjaxObservations = document.querySelector('div[data-observations-list]').dataset.ajaxObservations;
  let urlAjaxEvents = document.querySelector('div[data-observations-list]').dataset.ajaxEvents;
  let searchPlaceholder = document.querySelector('div[data-observations-list]').dataset.observationAutocomplete;
  let listFilter = JSON.parse(document.querySelector('div[data-observations-list]').dataset.filters);

  export default {
    name: "App",
    components: {
      ImageHeader,
      Searchautocomplete,
      LMap,
      LTileLayer,
      LMarker,
      LGeoJson,
      LIcon,
      backToTop
    },
    data () {
      return {
        pageTitle: pageTitle,
        description: description,
        urlShare: document.querySelector("link[rel='canonical']").href,
        mapTitle: mapTitle,
        urlSearch: urlSearchObs,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText AppSearch__inputTextObservation',
          list: 'AppHeader__list_obs'
        },
        searchPlaceholder: searchPlaceholder,
        enableTooltip: true,
        showObs: true,
        showEvent: true,
        zoom: 5,
        url: 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
        center: L.latLng(0, 0),
        geojsonObs: null,
        geojsonEvents: null,
        urlAjaxObservations: urlAjaxObservations,
        urlAjaxEvents: urlAjaxEvents,
        imageCover: '/build/images/layout/observation_silouhette.jpg',
        imageCoverAlt: pageTitle,
        listFilters: listFilter,
        iconEvent: L.icon({
          iconUrl: '/build/images/markers/telescop.svg',
          iconSize: [32, 32],
          iconAnchor: [16, 16],
          popupAnchor: [0, -5],
        }),
        iconDsoPlanner: L.icon({
          iconUrl: '/build/images/markers/dsoplanner.svg',
          iconSize: [24, 24],
          iconAnchor: [12, 12],
          popupAnchor: [0, -5],
        }),
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
    },
    computed: {
      optionsEvents() {
        return {
          pointToLayer: this.pointToLayer,
          onEachFeature: this.onEachFeatureEventFunction
        }
      },
      optionsDsoPlanner() {
        return {
          pointToLayer: (feature, latLng) => {
            return L.marker(latLng, {icon: this.iconDsoPlanner})
          },
          onEachFeature: (feature, layer) => {
            let DsoPlannerPopupContent = Vue.extend(dsoPlannerPopupContent);
            let popup = new DsoPlannerPopupContent({
              propsData: {
                name: feature.properties.name,
                username: feature.properties.username,
                url: feature.properties.full_url,
                place: '',
                date_observation: feature.properties.date
              }
            });
            layer.bindPopup(popup.$mount().$el);
          }
        }
      },
      pointToLayer() {
        return (feature, latlng) => {
          return L.marker(latlng, {icon: this.iconEvent});
        }
      },
      onEachFeatureEventFunction() {
        if (!this.enableTooltip) {
          return () => {};
        }
        return (feature, layer) => {
          let EventPopupContent = Vue.extend(eventPopupContent);
          let popup = new EventPopupContent({
            propsData: {
              url: feature.properties.full_url,
              name: feature.properties.name,
              date_observation: feature.properties.date,
              location: feature.properties.location,
              organiser: feature.properties.organiserName
            }
          });
          layer.bindPopup(popup.$mount().$el);
        }
      },
      onEachFeatureDsoPlannerFunction() {
      },
    }
  }
</script>
