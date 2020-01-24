<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h2 class="Dso__title">{{ pageTitle }}</h2>

        <searchautocomplete
          ref="observationsearch"
          :searchPlaceholder="searchPlaceholder"
          :customClasses="autoCompleteClasse"
          :url="urlSearch"
        />

        <h3 class="Dso__title">{{ mapTitle }}</h3>
        <div class="Dso__leaflet">
          <l-map
            :zoom="zoom"
            :center="center"
          >
            <l-geo-json
              :geojson="geojson"
              :options="options"
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
  import popupContent from './GeojsonPopup';

  let pageTitle = document.querySelector('div[data-observations-list]').dataset.title;
  let mapTitle = document.querySelector('div[data-observations-list]').dataset.mapTitle;
  let urlSearchObs = document.querySelector('div[data-observations-list]').dataset.searchRoute;
  let urlAjaxObservations = document.querySelector('div[data-observations-list]').dataset.ajaxObservations;
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
        zoom: 5,
        url: 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
        center: L.latLng(48.856614, 2.3522219),
        geojson: null,
        urlAjaxObservations: urlAjaxObservations
      }
    },
    created() {
      axios.get(this.urlAjaxObservations).then((response) => {
          this.geojson =  response.data;
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
          let PopupContent = Vue.extend(popupContent);
          let popup = new PopupContent({
            propsData: {
              name: feature.properties.name,
              url: feature.properties.full_url,
              username: feature.properties.username,
              date_observation: feature.properties.date
            }
          });
          layer.bindPopup(popup.$mount().$el);
        }
      }
    }
  }
</script>
