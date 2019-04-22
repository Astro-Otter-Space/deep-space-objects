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

<!--        <div id="map">-->
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
<!--        </div>-->

        <!--List DSo-->
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3 class="Dso__title">Items</h3>
          <cards-grid
            :items="itemsDso"
            :show-controls="false"
          >
          </cards-grid>
        </div>
      </div>
    </section>
  </div>
</template>

<script>

  import CardsGrid from './../Dso/components/CardsGrid';
  import './../Icons/facebook';
  import './../Icons/twitter';

  import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

  let title = document.querySelector('div[data-observation-widget]').dataset.title;
  let desc = document.querySelector('div[data-observation-widget]').dataset.description;
  let dsoList = JSON.parse(document.querySelector('div[data-observation-widget]').dataset.listDso);

  export default {
    name: "App",
    components: {
      CardsGrid,
      LMap,
      LTileLayer,
      LMarker
    },
    data () {
      return {
        pageTitle: title,
        description: desc,
        urlShare: document.querySelector("link[rel='canonical']").href,
        itemsDso: dsoList,
        zoom: 15,
        url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        marker: L.latLng(47.413220, -1.219482),
      }
    }
  }
</script>
