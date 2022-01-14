<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <breadcrumbs
          :links="linksBreadcrumbs"
        ></breadcrumbs>

        <h1 class="Dso__title">
          {{ title }}
        </h1>

        <social-sharing
          :url="urlShare"
          :title="title"
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

        <div class="Dso__list" v-if="0 < itemsDso.length">
          <cards-grid
            :items="itemsDso"
            :show-controls="false"
            :show-ajax="false"
          >
          </cards-grid>
        </div>
      </div>

      <back-to-top visibleoffset="10" bottom="25px" right="25px" text="">
        <svgicon name="up" width="40" height="40"></svgicon>
      </back-to-top>
    </section>
  </div>
</template>

<script>
  import CardsGrid from './../Dso/components/CardsGrid';
  import './../Icons/up';
  import './../Icons/facebook';
  import './../Icons/twitter';
  import BackToTop from 'vue-backtotop';
  import Breadcrumbs from "../App/Breadcrumbs";

  let title = document.querySelector('div[data-last-update]').dataset.title;
  let breadcrumbsData = JSON.parse(document.querySelector('div[data-last-update]').dataset.breadcrumbs);
  let dsoList = JSON.parse(document.querySelector('div[data-last-update]').dataset.listDso);
  import {default as mode} from './../../components/night_mode';

  export default {
    name: "App",
    components: {Breadcrumbs, CardsGrid, BackToTop},
    data () {
      return {
        linksBreadcrumbs: breadcrumbsData,
        title: title,
        itemsDso: dsoList,
        urlShare: document.querySelector("link[rel='canonical']").href,
        description: ""
      }
    },
    mounted() {
      let listItems = [
        '.Dso__main',
        '.Dso__Form',
        '.Dso__description',
      ];

      let listMultiItems = [
        '.Dso__title',
        '.Dso__list a',
        '.Dso__td',
        '.appGridFacet__item a',
        'article.card',
        '.Form__input',
        '.Form__select',
        '.Form__textarea',
        'td',
        'a'
      ];
      mode.setNightMode(listItems, listMultiItems);
    }
  }
</script>
