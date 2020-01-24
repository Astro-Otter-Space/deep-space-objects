<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">
        <h2 class="Dso__title">{{ pageTitle }}</h2>

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

        <a id="#description"></a>
        <div class="Dso__description" v-if="0 < description.length">
          <p>{{ description }}</p>
        </div>

        <a id="#information"></a>
        <div class="Dso__data">
          <h3 class="Dso__title">Setup</h3>
          <Table
            :columns="gridColumns"
            :data="gridData"
            :classTable="classTable"
            :classTr="classTr"
            :classTd="classTd"
          >
          </Table>
        </div>

        <!--List DSo-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3 class="Dso__title">Items</h3>
          <cards-grid
                  :items="itemsDso"
                  :show-controls="false"
          >
          </cards-grid>
        </div>

        <div id="map" style="height: 25rem;">
          <h3 class="Dso__title">Location</h3>
          <l-map
            :zoom="zoom"
            :center="marker"
          >
            <l-tile-layer
              :url="url"
              :attribution="attribution"
            />
            <l-marker
              :lat-lng="marker"
            ></l-marker>
          </l-map>
        </div>
      </div>
    </section>
  </div>
</template>

<script>

  import CardsGrid from './../Dso/components/CardsGrid';
  import Table from './../App/SimpleTable';
  import './../Icons/facebook';
  import './../Icons/twitter';

  import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

  let title = document.querySelector('div[data-observation-widget]').dataset.title;
  let desc = document.querySelector('div[data-observation-widget]').dataset.description;
  let data = JSON.parse(document.querySelector('div[data-observation-widget]').dataset.observation);
  let dsoList = JSON.parse(document.querySelector('div[data-observation-widget]').dataset.listDso);
  let coordinates = JSON.parse(document.querySelector('div[data-observation-widget]').dataset.coordinates);

  export default {
    name: "App",
    components: {
      CardsGrid,
      Table,
      LMap,
      LTileLayer,
      LMarker
    },
    data () {
      return {
        pageTitle: title,
        description: desc,
        urlShare: document.querySelector("link[rel='canonical']").href,
        gridColumns: ['col0', 'col1'],
        gridData: data,
        classTable: "Dso__table",
        classTr: "Dso__tr",
        classTd: "Dso__td",
        itemsDso: dsoList,
        zoom: 15,
        url: 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
        marker: L.latLng(coordinates.lat, coordinates.lon)
      }
    }
  }
</script>
